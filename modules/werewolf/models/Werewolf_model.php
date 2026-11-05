<?php

class Werewolf_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    // ═══════════════════════════════════════════════════════
    // INIT GAME
    // ═══════════════════════════════════════════════════════

    public function init_game($room_id, $players) {
        $jumlah = count($players);
        $dist   = ww_role_distribution($jumlah);

        // Buat daftar peran
        $roles = [];
        foreach ($dist as $role => $count) {
            for ($i = 0; $i < $count; $i++) $roles[] = $role;
        }
        shuffle($roles);
        shuffle($players);

        // Fase pertama: malam
        $phase_end = date('Y-m-d H:i:s', time() + ww_phase_duration('malam'));

        $this->db->table('ww_games')->insert([
            'room_id'        => $room_id,
            'day_count'      => 1,
            'current_phase'  => 'malam',
            'phase_end_time' => $phase_end,
            'winner_team'    => null,
        ]);
        $game_id = $this->db->last_insert_id();

        // Assign peran ke pemain
        foreach ($players as $i => $p) {
            $this->db->table('ww_players')->insert([
                'ww_game_id'        => $game_id,
                'user_id'           => $p['user_id'],
                'role'              => $roles[$i],
                'is_alive'          => 1,
                'eliminated_reason' => null,
            ]);
        }

        // Pesan narator pembuka
        $this->_narasi($game_id, '🌙 Selamat datang di Desa Seru! Malam pertama telah tiba. Para Werewolf bergerak dalam kegelapan...', 'publik');
        $this->_narasi($game_id, '🐺 [Khusus Werewolf] Saatnya berburu! Diskusikan target mangsamu malam ini.', 'werewolf');

        // Bump state
        $this->_bump_state($room_id);

        return $game_id;
    }

    // ═══════════════════════════════════════════════════════
    // GET STATE (Aman — filter berdasarkan role pemain)
    // ═══════════════════════════════════════════════════════

    public function get_state($room_id, $user_id) {
        $game = $this->db->query(
            "SELECT wg.*, gr.state_version FROM ww_games wg JOIN game_rooms gr ON wg.room_id = gr.id WHERE wg.room_id = ?",
            [$room_id]
        )->row();

        if (!$game) return null;

        // Cek apakah fase sudah habis & perlu di-advance
        $this->check_and_advance_phase($game->id, $room_id);

        // Reload setelah advance
        $game = $this->db->query(
            "SELECT wg.*, gr.state_version FROM ww_games wg JOIN game_rooms gr ON wg.room_id = gr.id WHERE wg.room_id = ?",
            [$room_id]
        )->row();

        // Info pemain sendiri
        $me = $this->db->query(
            "SELECT wp.*, u.username, u.avatar FROM ww_players wp JOIN users u ON wp.user_id = u.id WHERE wp.ww_game_id = ? AND wp.user_id = ?",
            [$game->id, $user_id]
        )->row();

        // Semua pemain (untuk tampilan grid)
        $all_players = $this->db->query(
            "SELECT wp.user_id, wp.is_alive, wp.eliminated_reason, u.username, u.avatar FROM ww_players wp JOIN users u ON wp.user_id = u.id WHERE wp.ww_game_id = ? ORDER BY wp.id",
            [$game->id]
        )->result_array();

        // Hitung sisa waktu fase
        $phase_end_ts = strtotime($game->phase_end_time);
        $seconds_left = max(0, $phase_end_ts - time());

        // Cek apakah pemain sudah melakukan aksi malam/voting ini
        $my_action = null;
        if (in_array($game->current_phase, ['malam', 'siang_voting'])) {
            $phase_key = ($game->current_phase === 'malam') ? 'malam' : 'siang_voting';
            $act = $this->db->query(
                "SELECT target_user_id FROM ww_actions WHERE ww_game_id=? AND day_count=? AND phase=? AND actor_user_id=?",
                [$game->id, $game->day_count, $phase_key, $user_id]
            )->row();
            $my_action = $act ? (int)$act->target_user_id : null;
        }

        // Hasil terawang (hanya untuk Penerawang)
        $seer_result = null;
        if ($me && $me->role === 'penerawang') {
            $terawang = $this->db->query(
                "SELECT a.target_user_id, wp.role, u.username FROM ww_actions a
                 JOIN ww_players wp ON wp.user_id = a.target_user_id AND wp.ww_game_id = a.ww_game_id
                 JOIN users u ON u.id = a.target_user_id
                 WHERE a.ww_game_id=? AND a.day_count=? AND a.phase='malam' AND a.actor_user_id=? AND a.action_type='terawang'",
                [$game->id, $game->day_count, $user_id]
            )->row();
            if ($terawang) {
                $seer_result = [
                    'username' => $terawang->username,
                    'role'     => $terawang->role,
                ];
            }
        }

        // Mapping pemain untuk display (jangan bocorkan role!)
        $players_display = array_map(function($p) use ($me) {
            return [
                'user_id'  => (int)$p['user_id'],
                'username' => $p['username'],
                'avatar'   => $p['avatar'],
                'is_alive' => (bool)$p['is_alive'],
                'eliminated_reason' => $p['eliminated_reason'],
                // Role hanya diketahui oleh pemain yang bersangkutan (atau sudah tewas → semua lihat)
            ];
        }, $all_players);

        return [
            'game_id'        => (int)$game->id,
            'state_version'  => (int)$game->state_version,
            'day_count'      => (int)$game->day_count,
            'current_phase'  => $game->current_phase,
            'phase_label'    => ww_phase_label($game->current_phase),
            'seconds_left'   => $seconds_left,
            'winner_team'    => $game->winner_team,
            'my_role'        => $me ? $me->role : null,
            'my_is_alive'    => $me ? (bool)$me->is_alive : false,
            'my_user_id'     => (int)$user_id,
            'my_action'      => $my_action,
            'seer_result'    => $seer_result,
            'players'        => $players_display,
        ];
    }

    // ═══════════════════════════════════════════════════════
    // ADVANCE PHASE (Otomatis saat fase habis)
    // ═══════════════════════════════════════════════════════

    public function check_and_advance_phase($game_id, $room_id) {
        $game = $this->db->table('ww_games')->where('id', $game_id)->get()->row();
        if (!$game || $game->current_phase === 'selesai') return;

        if (strtotime($game->phase_end_time) > time()) return; // belum waktunya

        $next_phase = ww_next_phase($game->current_phase);

        // Proses aksi sebelum pindah fase
        if ($game->current_phase === 'malam') {
            $this->_process_night_actions($game_id, $game->day_count, $room_id);
        } elseif ($game->current_phase === 'siang_voting') {
            $this->_process_voting($game_id, $game->day_count, $room_id);
        }

        // Cek kemenangan
        $winner = $this->_check_winner($game_id);
        if ($winner) {
            $this->db->table('ww_games')->where('id', $game_id)->update([
                'current_phase' => 'selesai',
                'winner_team'   => $winner,
            ]);
            $this->db->table('game_rooms')->where('id', $room_id)->update(['status' => 'finished']);

            // Update statistik pemenang
            $winning_role = ($winner === 'warga') ? 'warga' : 'werewolf';
            $winners = $this->db->query(
                "SELECT user_id FROM ww_players WHERE ww_game_id=? AND role" .
                ($winner === 'warga' ? " != 'werewolf'" : " = 'werewolf'"),
                [$game_id]
            )->result_array();
            foreach ($winners as $w) {
                $this->db->query("UPDATE users SET ww_wins = ww_wins + 1 WHERE id = ?", [$w['user_id']]);
            }
            $this->_bump_state($room_id);
            return;
        }

        // Naikkan day_count saat fase kembali ke malam
        $new_day = $game->day_count;
        if ($game->current_phase === 'siang_voting' && $next_phase === 'malam') {
            $new_day++;
            $this->_narasi($game_id, "🌙 Hari ke-{$new_day}: Malam kembali menyelimuti desa... Bersiaplah!", 'publik');
        } elseif ($next_phase === 'siang_diskusi') {
            $this->_narasi($game_id, "☀️ Pagi telah tiba! Warga berkumpul untuk berdiskusi. Siapakah yang bersembunyi di antara kalian?", 'publik');
        } elseif ($next_phase === 'siang_voting') {
            $this->_narasi($game_id, "⚖️ Waktunya voting! Siapa yang kalian curigai sebagai Werewolf? Berikan suaramu sekarang!", 'publik');
        }

        $phase_end = date('Y-m-d H:i:s', time() + ww_phase_duration($next_phase));

        $this->db->table('ww_games')->where('id', $game_id)->update([
            'current_phase'  => $next_phase,
            'phase_end_time' => $phase_end,
            'day_count'      => $new_day,
        ]);

        $this->_bump_state($room_id);
    }

    /**
     * Proses aksi malam: mangsa vs lindungi
     */
    private function _process_night_actions($game_id, $day_count, $room_id) {
        // Cari target mangsa (majority vote werewolf)
        $mangsa_rows = $this->db->query(
            "SELECT target_user_id, COUNT(*) AS votes FROM ww_actions
             WHERE ww_game_id=? AND day_count=? AND phase='malam' AND action_type='mangsa'
             GROUP BY target_user_id ORDER BY votes DESC LIMIT 1",
            [$game_id, $day_count]
        )->row();

        // Cari yang dilindungi
        $lindungi_row = $this->db->query(
            "SELECT target_user_id FROM ww_actions
             WHERE ww_game_id=? AND day_count=? AND phase='malam' AND action_type='lindungi'
             LIMIT 1",
            [$game_id, $day_count]
        )->row();

        if (!$mangsa_rows) {
            $this->_narasi($game_id, "🌅 Fajar menyingsing... Malam ini tidak ada korban. Warga bernafas lega.", 'publik');
            return;
        }

        $target_id  = $mangsa_rows->target_user_id;
        $lindung_id = $lindungi_row ? $lindungi_row->target_user_id : null;

        if ($lindung_id && $lindung_id == $target_id) {
            $this->_narasi($game_id, "🌅 Fajar menyingsing... Semalam ada yang hampir menjadi korban, tetapi berhasil diselamatkan! 🛡️ Seseorang melindungi mereka tepat waktu.", 'publik');
            return;
        }

        // Korban tewas
        $victim = $this->db->query("SELECT u.username FROM ww_players wp JOIN users u ON wp.user_id = u.id WHERE wp.ww_game_id=? AND wp.user_id=?", [$game_id, $target_id])->row();
        $vname  = $victim ? $victim->username : 'Seseorang';

        $this->db->query(
            "UPDATE ww_players SET is_alive=0, eliminated_reason='dimangsa' WHERE ww_game_id=? AND user_id=?",
            [$game_id, $target_id]
        );

        $this->_narasi($game_id, "🌅 Fajar menyingsing... Warga menemukan tubuh **@{$vname}** tak bernyawa di tepi desa! Mereka tewas dimangsa malam tadi! 😱", 'publik');
        $this->_bump_state($room_id);
    }

    /**
     * Proses voting siang
     */
    private function _process_voting($game_id, $day_count, $room_id) {
        $votes = $this->db->query(
            "SELECT target_user_id, COUNT(*) AS votes FROM ww_actions
             WHERE ww_game_id=? AND day_count=? AND phase='siang_voting' AND action_type='vote_gantung'
             GROUP BY target_user_id ORDER BY votes DESC",
            [$game_id, $day_count]
        )->result_array();

        if (empty($votes)) {
            $this->_narasi($game_id, "⚖️ Tidak ada yang memberikan suara. Tidak ada yang digantung hari ini.", 'publik');
            return;
        }

        // Cek apakah ada seri
        if (count($votes) >= 2 && $votes[0]['votes'] === $votes[1]['votes']) {
            $this->_narasi($game_id, "⚖️ Suara seri! Tidak ada keputusan hari ini. Diskusi berlanjut...", 'publik');
            return;
        }

        $target_id = $votes[0]['target_user_id'];
        $victim    = $this->db->query("SELECT u.username, wp.role FROM ww_players wp JOIN users u ON wp.user_id = u.id WHERE wp.ww_game_id=? AND wp.user_id=?", [$game_id, $target_id])->row();
        $vname     = $victim ? $victim->username : 'Seseorang';
        $vrole     = $victim ? $victim->role     : 'warga';

        $this->db->query(
            "UPDATE ww_players SET is_alive=0, eliminated_reason='divoting' WHERE ww_game_id=? AND user_id=?",
            [$game_id, $target_id]
        );

        $role_labels = ['warga' => 'Warga 🏘️', 'werewolf' => 'Werewolf 🐺', 'penerawang' => 'Penerawang 🔮', 'pelindung' => 'Pelindung 🛡️'];
        $role_label  = $role_labels[$vrole] ?? $vrole;

        $this->_narasi($game_id, "⚖️ Warga bersepakat menggantung **@{$vname}**! Ternyata mereka adalah seorang **{$role_label}**!", 'publik');
        $this->_bump_state($room_id);
    }

    /**
     * Cek kondisi kemenangan
     */
    private function _check_winner($game_id) {
        $players = $this->db->query(
            "SELECT role, is_alive FROM ww_players WHERE ww_game_id=?",
            [$game_id]
        )->result_array();

        $alive_ww    = 0;
        $alive_nonww = 0;
        foreach ($players as $p) {
            if (!$p['is_alive']) continue;
            if ($p['role'] === 'werewolf') $alive_ww++;
            else $alive_nonww++;
        }

        if ($alive_ww === 0) return 'warga';
        if ($alive_ww >= $alive_nonww) return 'werewolf';
        return null;
    }

    // ═══════════════════════════════════════════════════════
    // AKSI PEMAIN
    // ═══════════════════════════════════════════════════════

    /**
     * Submit aksi malam (mangsa, terawang, lindungi) atau vote siang
     */
    public function submit_action($game_id, $actor_id, $target_id, $action_type, $day_count, $phase) {
        // Cek apakah sudah ada aksi dari pemain ini di fase ini
        $existing = $this->db->query(
            "SELECT id FROM ww_actions WHERE ww_game_id=? AND day_count=? AND phase=? AND actor_user_id=?",
            [$game_id, $day_count, $phase, $actor_id]
        )->row();

        if ($existing) {
            // Update aksi (ganti pilihan)
            $this->db->query(
                "UPDATE ww_actions SET target_user_id=?, action_type=? WHERE id=?",
                [$target_id, $action_type, $existing->id]
            );
        } else {
            $this->db->table('ww_actions')->insert([
                'ww_game_id'     => $game_id,
                'day_count'      => $day_count,
                'phase'          => $phase,
                'actor_user_id'  => $actor_id,
                'target_user_id' => $target_id,
                'action_type'    => $action_type,
            ]);
        }
        return ['ok' => true];
    }

    // ═══════════════════════════════════════════════════════
    // CHAT
    // ═══════════════════════════════════════════════════════

    public function send_chat($game_id, $sender_id, $channel, $message) {
        // Validasi akses channel
        if ($channel === 'werewolf') {
            $player = $this->db->query(
                "SELECT role, is_alive FROM ww_players WHERE ww_game_id=? AND user_id=?",
                [$game_id, $sender_id]
            )->row();
            if (!$player || $player->role !== 'werewolf') {
                return ['ok' => false, 'message' => 'Kamu tidak punya akses ke channel Werewolf.'];
            }
        }

        if ($channel === 'arwah') {
            $player = $this->db->query(
                "SELECT is_alive FROM ww_players WHERE ww_game_id=? AND user_id=?",
                [$game_id, $sender_id]
            )->row();
            if (!$player || $player->is_alive) {
                return ['ok' => false, 'message' => 'Hanya pemain yang sudah tewas yang bisa mengirim pesan ke channel arwah.'];
            }
        }

        $this->db->table('ww_chats')->insert([
            'ww_game_id'     => $game_id,
            'sender_user_id' => $sender_id,
            'channel'        => $channel,
            'message'        => sanitize_chat($message),
        ]);

        return ['ok' => true, 'chat_id' => $this->db->last_insert_id()];
    }

    /**
     * Ambil pesan chat sesuai akses pemain
     */
    public function get_chats($game_id, $user_id, $since_id = 0) {
        $player = $this->db->query(
            "SELECT role, is_alive FROM ww_players WHERE ww_game_id=? AND user_id=?",
            [$game_id, $user_id]
        )->row();

        // Bangun kondisi channel yang boleh dilihat
        $allowed = ["'publik'"];
        if ($player) {
            if ($player->role === 'werewolf') $allowed[] = "'werewolf'";
            if (!$player->is_alive) $allowed[] = "'arwah'";
        }
        $channel_in = implode(',', $allowed);

        return $this->db->query(
            "SELECT wc.id, wc.channel, wc.message, wc.created_at,
                    u.username AS sender_name, u.id AS sender_id
             FROM ww_chats wc
             LEFT JOIN users u ON wc.sender_user_id = u.id
             WHERE wc.ww_game_id=? AND wc.id > ? AND wc.channel IN ({$channel_in})
             ORDER BY wc.id ASC
             LIMIT 50",
            [$game_id, $since_id]
        )->result_array();
    }

    // ═══════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════

    private function _narasi($game_id, $msg, $channel = 'publik') {
        $this->db->table('ww_chats')->insert([
            'ww_game_id'     => $game_id,
            'sender_user_id' => null, // null = narator
            'channel'        => $channel,
            'message'        => $msg,
        ]);
    }

    private function _bump_state($room_id) {
        $this->db->query("UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?", [$room_id]);
    }

    public function get_game($room_id) {
        return $this->db->table('ww_games')->where('room_id', $room_id)->get()->row();
    }

    public function get_state_version($room_id) {
        $r = $this->db->table('game_rooms')->select('state_version')->where('id', $room_id)->get()->row();
        return $r ? (int)$r->state_version : 0;
    }

    /**
     * Aksi Menyerah (Surrender) dari Permainan Werewolf
     */
    public function surrender($room_id, $user_id) {
        $game = $this->get_game($room_id);
        if (!$game) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];

        $user = $this->db->table('users')->where('id', $user_id)->get()->row();
        $username = $user ? $user->username : 'Pemain';

        // Tandai tewas/menyerah
        $this->db->query("UPDATE ww_players SET is_alive = 0, eliminated_reason = 'Menyerah' WHERE ww_game_id = ? AND user_id = ?", [$game->id, $user_id]);

        if (!class_exists('Room_model')) {
            require_once dirname(dirname(__DIR__)) . '/lobby/models/Room_model.php';
        }
        $room_model = new Room_model();
        $room_model->leave_room($room_id, $user_id);

        $this->_narasi($game->id, "🏳️ {$username} telah MENYERAH dan meninggalkan Desa Seru.");

        // Cek pemenang
        $alive = $this->db->query("SELECT * FROM ww_players WHERE ww_game_id=? AND is_alive=1", [$game->id])->result_array();
        $winner_team = $this->_check_winner($game->id);

        if (count($alive) <= 1 || $winner_team) {
            $team = $winner_team ?? 'warga';
            $this->db->query("UPDATE ww_games SET winner_team=? WHERE id=?", [$team, $game->id]);
            $this->db->query("UPDATE game_rooms SET status='finished' WHERE id=?", [$room_id]);
            $this->_narasi($game->id, "🏆 PERMAINAN SELESAI! Kemenangan untuk Tim " . strtoupper($team) . "!");
        }

        $this->_bump_state($room_id);
        return ['ok' => true, 'redirect' => site_url('lobby')];
    }
}
