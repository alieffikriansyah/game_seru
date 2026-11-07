<?php

class Room_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Ambil semua room yang sedang aktif (waiting & playing)
     */
    public function get_active_rooms() {
        return $this->db->query(
            "SELECT gr.*, u.username AS host_name, u.avatar AS host_avatar,
                    COUNT(rp.id) AS player_count
             FROM game_rooms gr
             LEFT JOIN users u ON gr.host_user_id = u.id
             LEFT JOIN room_players rp ON gr.id = rp.room_id
             WHERE gr.status IN ('waiting', 'playing')
             GROUP BY gr.id
             ORDER BY gr.created_at DESC
             LIMIT 50"
        )->result_array();
    }

    /**
     * Cari room berdasarkan room_code
     */
    public function get_room_by_code($room_code) {
        return $this->db->table('game_rooms')
            ->where('room_code', strtoupper(trim($room_code)))
            ->get()->row();
    }

    /**
     * Ambil detail room lengkap (join dengan host info)
     */
    public function get_room_detail($room_code) {
        $rows = $this->db->query(
            "SELECT gr.*, u.username AS host_name, u.avatar AS host_avatar
             FROM game_rooms gr
             LEFT JOIN users u ON gr.host_user_id = u.id
             WHERE gr.room_code = ?",
            [strtoupper(trim($room_code))]
        )->result_array();
        return $rows[0] ?? null;
    }

    /**
     * Buat room baru, kembalikan room_code
     */
    public function create_room($data) {
        // Generate kode unik (coba sampai tidak collision)
        $attempts = 0;
        do {
            $code = generate_room_code(6);
            $existing = $this->get_room_by_code($code);
            $attempts++;
        } while ($existing && $attempts < 10);

        global $config;
        if ($data['game_type'] === 'ular_tangga') {
            $max = $config['ut_max_players'] ?? 6;
        } elseif ($data['game_type'] === 'skenalitik') {
            $max = $config['sk_max_players'] ?? 6;
        } elseif ($data['game_type'] === 'ludo') {
            $max = $config['ludo_max_players'] ?? 4;
        } elseif ($data['game_type'] === 'uno') {
            $max = $config['uno_max_players'] ?? 10;
        } else {
            $max = $config['ww_max_players'] ?? 10;
        }

        $this->db->table('game_rooms')->insert([
            'room_code'     => $code,
            'room_name'     => $data['room_name'],
            'game_type'     => $data['game_type'],
            'host_user_id'  => $data['host_user_id'],
            'max_players'   => $max,
            'status'        => 'waiting',
            'state_version' => 1,
        ]);

        $room_id = $this->db->last_insert_id();
        // Host otomatis join sebagai seat_order 1
        $this->_join_room_direct($room_id, $data['host_user_id'], 1);

        return $code;
    }

    /**
     * Gabung ke room via room_code
     * Return: ['ok' => bool, 'message' => string, 'room_code' => string]
     */
    public function join_room($room_code, $user_id) {
        $room = $this->get_room_by_code($room_code);

        if (!$room) {
            return ['ok' => false, 'message' => 'Kode room tidak ditemukan. Periksa kembali kodenya.'];
        }

        if ($room->status !== 'waiting') {
            return ['ok' => false, 'message' => 'Room ini sudah dimulai atau sudah selesai.'];
        }

        // Cek apakah user sudah ada di room ini
        $existing = $this->db->table('room_players')
            ->where('room_id', $room->id)
            ->where('user_id', $user_id)
            ->get()->row();

        if ($existing) {
            // Sudah di room, langsung redirect
            return ['ok' => true, 'message' => 'Kamu sudah ada di room ini!', 'room_code' => $room->room_code];
        }

        // Hitung jumlah pemain saat ini
        $count = $this->db->table('room_players')
            ->where('room_id', $room->id)
            ->get()->num_rows();

        if ($count >= $room->max_players) {
            return ['ok' => false, 'message' => 'Room sudah penuh! Cari room lain.'];
        }

        $seat = $count + 1;
        $this->_join_room_direct($room->id, $user_id, $seat);
        $this->_bump_state_version($room->id);

        return ['ok' => true, 'message' => 'Berhasil bergabung ke room!', 'room_code' => $room->room_code];
    }

    /**
     * Insert ke room_players (internal)
     */
    private function _join_room_direct($room_id, $user_id, $seat_order) {
        $this->db->table('room_players')->insert([
            'room_id'    => $room_id,
            'user_id'    => $user_id,
            'seat_order' => $seat_order,
            'is_ready'   => 0,
        ]);
    }

    /**
     * Toggle is_ready untuk pemain
     */
    public function toggle_ready($room_id, $user_id) {
        $player = $this->db->table('room_players')
            ->where('room_id', $room_id)
            ->where('user_id', $user_id)
            ->get()->row();

        if (!$player) return false;

        $new_ready = $player->is_ready ? 0 : 1;
        $this->db->table('room_players')
            ->where('room_id', $room_id)
            ->where('user_id', $user_id)
            ->update(['is_ready' => $new_ready]);

        $this->_bump_state_version($room_id);
        return true;
    }

    /**
     * Ambil daftar pemain dalam room beserta info user
     */
    public function get_players($room_id) {
        return $this->db->query(
            "SELECT rp.*, u.username, u.avatar
             FROM room_players rp
             JOIN users u ON rp.user_id = u.id
             WHERE rp.room_id = ?
             ORDER BY rp.seat_order ASC",
            [(int)$room_id]
        )->result_array();
    }

    /**
     * Mulai game — ubah status room ke 'playing'
     */
    public function start_game($room_id) {
        $this->db->table('game_rooms')
            ->where('id', $room_id)
            ->update(['status' => 'playing']);
        $this->_bump_state_version($room_id);
    }

    /**
     * Tandai room sebagai selesai
     */
    public function finish_game($room_id) {
        $this->db->table('game_rooms')
            ->where('id', $room_id)
            ->update(['status' => 'finished']);
    }

    /**
     * Pemain keluar dari room
     */
    public function leave_room($room_id, $user_id) {
        $this->db->table('room_players')
            ->where('room_id', $room_id)
            ->where('user_id', $user_id)
            ->delete();

        // Reorder seat_order
        $players = $this->get_players($room_id);
        foreach ($players as $i => $p) {
            $this->db->table('room_players')
                ->where('id', $p['id'])
                ->update(['seat_order' => $i + 1]);
        }

        // Cek apakah room kosong → hapus room
        $remaining = count($players);
        if ($remaining === 0) {
            $this->db->table('game_rooms')->where('id', $room_id)->delete();
            return;
        }

        // Jika host keluar, promosi pemain pertama jadi host baru
        $room = $this->db->table('game_rooms')->where('id', $room_id)->get()->row();
        if ($room && $room->host_user_id == $user_id && isset($players[0])) {
            $this->db->table('game_rooms')
                ->where('id', $room_id)
                ->update(['host_user_id' => $players[0]['user_id']]);
        }

        $this->_bump_state_version($room_id);
    }

    /**
     * Cek apakah user adalah host room
     */
    public function is_host($room_id, $user_id) {
        $room = $this->db->table('game_rooms')
            ->where('id', $room_id)
            ->where('host_user_id', $user_id)
            ->get()->row();
        return $room !== null;
    }

    /**
     * Ambil state_version room (untuk polling)
     */
    public function get_state_version($room_id) {
        $room = $this->db->table('game_rooms')
            ->select('state_version')
            ->where('id', $room_id)
            ->get()->row();
        return $room ? (int)$room->state_version : 0;
    }

    /**
     * Increment state_version (untuk sinyal perubahan ke client)
     */
    private function _bump_state_version($room_id) {
        $this->db->query(
            "UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?",
            [(int)$room_id]
        );
    }

    /**
     * Apakah semua pemain sudah ready?
     */
    public function all_ready($room_id) {
        $not_ready = $this->db->table('room_players')
            ->where('room_id', $room_id)
            ->where('is_ready', 0)
            ->get()->num_rows();
        return $not_ready === 0;
    }

    /**
     * Hapus room-room lama yang sudah selesai (cleanup)
     */
    public function cleanup_old_rooms() {
        $this->db->query(
            "DELETE FROM game_rooms WHERE status = 'finished' AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
    }
}
