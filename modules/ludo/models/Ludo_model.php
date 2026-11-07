<?php

class Ludo_model extends Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_schema();
    }

    // ── Self-Healing Schema Migration Otomatis ────────────────────
    private function ensure_schema() {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        // 1. Tambahkan enum 'ludo' ke game_rooms jika belum ada
        try {
            $this->db->query("ALTER TABLE `game_rooms` MODIFY COLUMN `game_type` enum('uno','werewolf','ular_tangga','skenalitik','ludo') NOT NULL");
        } catch (\Throwable $e) {}

        // 2. Tambahkan kolom ludo_wins ke tabel users jika belum ada
        try {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `ludo_wins` int(11) DEFAULT '0'");
        } catch (\Throwable $e) {}

        // 3. Tabel Sesi Permainan Ludo
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `ludo_games` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `room_id` int(11) NOT NULL,
                `current_turn_user_id` int(11) NOT NULL,
                `winner_user_id` int(11) DEFAULT NULL,
                `dice_value` tinyint(4) DEFAULT NULL,
                `consecutive_sixes` tinyint(4) NOT NULL DEFAULT '0',
                `game_phase` enum('roll','select_token','ended') NOT NULL DEFAULT 'roll',
                `last_action` varchar(50) DEFAULT NULL,
                `last_action_desc` text DEFAULT NULL,
                `state_version` int(11) NOT NULL DEFAULT '1',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_ludo_room` (`room_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Throwable $e) {}

        // 4. Tabel Pemain Ludo
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `ludo_players` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ludo_game_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `color` enum('red','green','yellow','blue') NOT NULL,
                `turn_order` tinyint(4) NOT NULL DEFAULT '1',
                `finished_rank` tinyint(4) NOT NULL DEFAULT '0',
                `tokens_finished` tinyint(4) NOT NULL DEFAULT '0',
                `is_resigned` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `idx_lp_game` (`ludo_game_id`),
                KEY `idx_lp_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Throwable $e) {}

        // 5. Tabel 4 Bidak / Pion Ludo per Pemain
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `ludo_tokens` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ludo_game_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `color` enum('red','green','yellow','blue') NOT NULL,
                `token_index` tinyint(4) NOT NULL,
                `state` enum('yard','track','home_run','finished') NOT NULL DEFAULT 'yard',
                `position` int(11) NOT NULL DEFAULT '-1',
                `steps_moved` tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `idx_lt_game_user` (`ludo_game_id`,`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Throwable $e) {}

        // 6. Tabel Log Aksi Permainan Ludo
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `ludo_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ludo_game_id` int(11) NOT NULL,
                `user_id` int(11) DEFAULT NULL,
                `action_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
                `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_ludo_logs` (`ludo_game_id`,`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Throwable $e) {}
    }

    // ── Master Konfigurasi Papan Ludo 15x15 ───────────────────────
    public static function get_board_config() {
        return [
            // Offset petak awal masuk lintasan 52 petak utama
            'color_offsets' => [
                'red'    => 0,   // Start Petak 0  (x: 1, y: 6)
                'green'  => 13,  // Start Petak 13 (x: 8, y: 1)
                'yellow' => 26,  // Start Petak 26 (x: 13, y: 8)
                'blue'   => 39,  // Start Petak 39 (x: 6, y: 13)
            ],
            // 8 Petak Aman (4 Petak Start + 4 Petak Bintang)
            'safe_squares' => [0, 8, 13, 21, 26, 34, 39, 47],
            // Total langkah dari Start hingga Segitiga Kemenangan
            'total_steps_to_finish' => 56, // 51 langkah jalur utama + 5 langkah home column
            // Metadata visual warna
            'colors' => [
                'red'    => ['name' => 'Merah',  'hex' => '#ef4444', 'icon' => '🔴'],
                'green'  => ['name' => 'Hijau',  'hex' => '#10b981', 'icon' => '🟢'],
                'yellow' => ['name' => 'Kuning', 'hex' => '#f59e0b', 'icon' => '🟡'],
                'blue'   => ['name' => 'Biru',   'hex' => '#3b82f6', 'icon' => '🔵'],
            ],
        ];
    }

    // ── Inisialisasi Game Baru dari Lobby ──────────────────────────
    public function init_game($room_id, $players) {
        $color_keys = ['red', 'green', 'yellow', 'blue'];
        $player_count = count($players);

        // Jika hanya 2 pemain, gunakan posisi berseberangan: Merah vs Kuning
        if ($player_count === 2) {
            $assigned_colors = ['red', 'yellow'];
        } else {
            $assigned_colors = array_slice($color_keys, 0, $player_count);
        }

        // 1. Buat sesi game
        $first_player_id = (int)$players[0]['user_id'];
        $this->db->table('ludo_games')->insert([
            'room_id'              => $room_id,
            'current_turn_user_id' => $first_player_id,
            'winner_user_id'       => null,
            'dice_value'           => null,
            'consecutive_sixes'    => 0,
            'game_phase'           => 'roll',
            'last_action'          => 'GAME_START',
            'last_action_desc'     => 'Permainan Ludo dimulai! Giliran @' . $players[0]['username'] . ' melempar dadu.',
            'state_version'        => 1,
        ]);
        $game_id = $this->db->insert_id();

        // 2. Daftarkan pemain dan buat 4 pion untuk masing-masing pemain
        $order = 1;
        foreach ($players as $idx => $p) {
            $u_id  = (int)$p['user_id'];
            $color = $assigned_colors[$idx] ?? $color_keys[$idx % 4];

            $this->db->table('ludo_players')->insert([
                'ludo_game_id'    => $game_id,
                'user_id'         => $u_id,
                'color'           => $color,
                'turn_order'      => $order++,
                'finished_rank'   => 0,
                'tokens_finished' => 0,
                'is_resigned'     => 0,
            ]);

            // Buat 4 pion di Kandang (Yard)
            for ($token_idx = 0; $token_idx < 4; $token_idx++) {
                $this->db->table('ludo_tokens')->insert([
                    'ludo_game_id' => $game_id,
                    'user_id'      => $u_id,
                    'color'        => $color,
                    'token_index'  => $token_idx,
                    'state'        => 'yard',
                    'position'     => -1,
                    'steps_moved'  => 0,
                ]);
            }
        }

        // 3. Catat log pembuka
        $this->add_log($game_id, $first_player_id, 'START', '🎲 Permainan Ludo resmi dimulai! Dapatkan angka 6 untuk membuka pion keluar Markas.');
        return $game_id;
    }

    // ── Ambil Seluruh Data State untuk Frontend ───────────────────
    public function get_state($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM ludo_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return null;
        $game = $game[0];
        $game_id = (int)$game['id'];

        // Daftar Pemain
        $raw_players = $this->db->query(
            "SELECT lp.*, u.username, u.avatar 
             FROM ludo_players lp
             JOIN users u ON lp.user_id = u.id
             WHERE lp.ludo_game_id = ?
             ORDER BY lp.turn_order ASC",
            [$game_id]
        )->result_array();

        $players = [];
        foreach ($raw_players as $p) {
            $is_current = ((int)$game['current_turn_user_id'] === (int)$p['user_id']);
            $is_me = ((int)$p['user_id'] === (int)$user_id);
            $players[] = [
                'id'              => (int)$p['id'],
                'user_id'         => (int)$p['user_id'],
                'username'        => $p['username'],
                'avatar'          => $p['avatar'] ?? 'default.png',
                'color'           => $p['color'],
                'turn_order'      => (int)$p['turn_order'],
                'finished_rank'   => (int)$p['finished_rank'],
                'tokens_finished' => (int)$p['tokens_finished'],
                'is_resigned'     => (bool)$p['is_resigned'],
                'is_current'      => $is_current,
                'is_me'           => $is_me,
            ];
        }

        // Daftar Seluruh Pion
        $raw_tokens = $this->db->query(
            "SELECT * FROM ludo_tokens WHERE ludo_game_id = ? ORDER BY color ASC, token_index ASC",
            [$game_id]
        )->result_array();

        $tokens = [];
        foreach ($raw_tokens as $t) {
            $tokens[] = [
                'id'          => (int)$t['id'],
                'user_id'     => (int)$t['user_id'],
                'color'       => $t['color'],
                'token_index' => (int)$t['token_index'],
                'state'       => $t['state'],
                'position'    => (int)$t['position'],
                'steps_moved' => (int)$t['steps_moved'],
            ];
        }

        // Cari opsi langkah valid jika giliran saya dan fase pilih pion
        $is_my_turn = ((int)$game['current_turn_user_id'] === (int)$user_id) && empty($game['winner_user_id']);
        $valid_movable_tokens = [];
        if ($is_my_turn && $game['game_phase'] === 'select_token' && !empty($game['dice_value'])) {
            $valid_movable_tokens = $this->get_movable_token_ids($game_id, (int)$user_id, (int)$game['dice_value']);
        }

        // Log Aktivitas
        $logs = $this->db->query(
            "SELECT ll.*, u.username 
             FROM ludo_logs ll 
             LEFT JOIN users u ON ll.user_id = u.id 
             WHERE ll.ludo_game_id = ? 
             ORDER BY ll.id DESC LIMIT 15",
            [$game_id]
        )->result_array();

        return [
            'game' => [
                'id'                   => (int)$game['id'],
                'room_id'              => (int)$game['room_id'],
                'current_turn_user_id' => (int)$game['current_turn_user_id'],
                'winner_user_id'       => !empty($game['winner_user_id']) ? (int)$game['winner_user_id'] : null,
                'dice_value'           => !empty($game['dice_value']) ? (int)$game['dice_value'] : null,
                'consecutive_sixes'    => (int)$game['consecutive_sixes'],
                'game_phase'           => $game['game_phase'],
                'last_action'          => $game['last_action'],
                'last_action_desc'     => $game['last_action_desc'],
                'state_version'        => (int)$game['state_version'],
            ],
            'is_my_turn'           => $is_my_turn,
            'players'              => $players,
            'tokens'               => $tokens,
            'valid_movable_tokens' => $valid_movable_tokens,
            'logs'                 => $logs,
            'config'               => self::get_board_config(),
        ];
    }

    public function get_state_version($room_id) {
        $row = $this->db->query("SELECT state_version FROM ludo_games WHERE room_id = ?", [$room_id])->result_array();
        return (int)($row[0]['state_version'] ?? 0);
    }

    private function bump_version($game_id, $room_id) {
        $this->db->query("UPDATE ludo_games SET state_version = state_version + 1 WHERE id = ?", [$game_id]);
        $this->db->query("UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?", [$room_id]);
    }

    // ── Filter Bidak yang Bisa Bergerak dengan Angka Dadu ─────────
    public function get_movable_token_ids($game_id, $user_id, $dice_value) {
        $tokens = $this->db->query(
            "SELECT * FROM ludo_tokens WHERE ludo_game_id = ? AND user_id = ?",
            [$game_id, $user_id]
        )->result_array();

        $valid_ids = [];
        foreach ($tokens as $t) {
            // 1. Pion di Markas (Yard) hanya bisa keluar jika dadu 6
            if ($t['state'] === 'yard') {
                if ($dice_value === 6) {
                    $valid_ids[] = (int)$t['id'];
                }
            } elseif ($t['state'] === 'track' || $t['state'] === 'home_run') {
                // 2. Pion di jalur bisa melangkah jika tidak melebihi petak Finish (56 langkah)
                $cur_steps = (int)$t['steps_moved'];
                if (($cur_steps + $dice_value) <= 56) {
                    $valid_ids[] = (int)$t['id'];
                }
            }
            // 3. Pion yang sudah 'finished' tidak bisa digerakkan lagi
        }

        return $valid_ids;
    }

    // ── Aksi: Lempar Dadu D6 ───────────────────────────────────────
    public function roll_dice($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM ludo_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }
        if ($game['game_phase'] !== 'roll') {
            return ['ok' => false, 'message' => 'Anda sudah melempar dadu, pilih pion untuk dijalankan!'];
        }

        $player = $this->db->query("SELECT * FROM ludo_players WHERE ludo_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        if (empty($player) || $player[0]['is_resigned'] || (int)$player[0]['finished_rank'] > 0) {
            return ['ok' => false, 'message' => 'Status pemain tidak valid.'];
        }

        // Kocok Dadu D6 (1 - 6)
        $dice = rand(1, 6);
        $consecutive_sixes = ($dice === 6) ? ((int)$game['consecutive_sixes'] + 1) : 0;

        // Aturan Ludo Standar: 3x Dadu 6 Berurutan -> FOUL! Batal & Ganti Giliran
        if ($consecutive_sixes >= 3) {
            $this->add_log($game_id, $user_id, 'THREE_SIXES', "💥 Dadu 6 tiga kali berturut-turut! Lemparan dibatalkan dan giliran langsung beralih ke lawan.");
            
            $next_player_id = $this->get_next_active_player_id($game_id, $user_id);
            $this->db->query(
                "UPDATE ludo_games SET dice_value = 6, consecutive_sixes = 0, game_phase = 'roll', current_turn_user_id = ?, last_action = 'FOUL_THREE_SIXES', last_action_desc = 'Mendapat angka 6 tiga kali berurutan! Lemparan hangus.' WHERE id = ?",
                [$next_player_id, $game_id]
            );
            $this->bump_version($game_id, $room_id);

            return [
                'ok'           => true,
                'dice'         => 6,
                'three_sixes'  => true,
                'no_moves'     => true,
                'message'      => 'Dadu 6 tiga kali berurutan! Lemparan batal dan giliran beralih.',
            ];
        }

        // Evaluasi pion yang bisa melangkah
        $movable_ids = $this->get_movable_token_ids($game_id, $user_id, $dice);

        // Jika tidak ada pion yang bisa melangkah sama sekali
        if (empty($movable_ids)) {
            $this->add_log($game_id, $user_id, 'ROLL_NO_MOVE', "Dadu bernilai {$dice}. Tidak ada pion yang bisa melangkah.");

            // Jika dadu bukan 6, giliran berpindah ke pemain berikutnya
            // (Jika dadu 6 tapi tidak ada langkah, pemain tetap mendapat lemparan lagi)
            if ($dice === 6) {
                $this->db->query(
                    "UPDATE ludo_games SET dice_value = ?, consecutive_sixes = ?, game_phase = 'roll', last_action = 'ROLL_BONUS', last_action_desc = 'Dadu 6 tapi tidak ada langkah valid. Mendapat giliran lempar ulang!' WHERE id = ?",
                    [$dice, $consecutive_sixes, $game_id]
                );
                $this->bump_version($game_id, $room_id);

                return [
                    'ok'          => true,
                    'dice'        => $dice,
                    'no_moves'    => true,
                    'bonus_roll'  => true,
                    'message'     => "Dadu 6! Tidak ada langkah yang memungkinkan, lempar dadu lagi.",
                ];
            } else {
                $next_player_id = $this->get_next_active_player_id($game_id, $user_id);
                $this->db->query(
                    "UPDATE ludo_games SET dice_value = ?, consecutive_sixes = 0, game_phase = 'roll', current_turn_user_id = ?, last_action = 'NO_MOVES', last_action_desc = 'Dadu {$dice}. Tidak ada langkah valid, giliran beralih.' WHERE id = ?",
                    [$dice, $next_player_id, $game_id]
                );
                $this->bump_version($game_id, $room_id);

                return [
                    'ok'          => true,
                    'dice'        => $dice,
                    'no_moves'    => true,
                    'bonus_roll'  => false,
                    'message'     => "Dadu {$dice}. Tidak ada pion yang bisa melangkah, giliran beralih.",
                ];
            }
        }

        // Ada pion yang bisa digerakkan -> masuk ke fase 'select_token'
        $this->db->query(
            "UPDATE ludo_games SET dice_value = ?, consecutive_sixes = ?, game_phase = 'select_token', last_action = 'ROLL', last_action_desc = 'Dadu {$dice}! Pilih pion yang ingin dijalankan.' WHERE id = ?",
            [$dice, $consecutive_sixes, $game_id]
        );
        $this->bump_version($game_id, $room_id);

        return [
            'ok'             => true,
            'dice'           => $dice,
            'no_moves'       => false,
            'movable_tokens' => $movable_ids,
            'auto_move'      => (count($movable_ids) === 1) ? $movable_ids[0] : null,
            'message'        => "Dadu bernilai {$dice}! Pilih pion Anda untuk melangkah.",
        ];
    }

    // ── Aksi: Jalankan Bidak Terpilih ─────────────────────────────
    public function move_token($room_id, $user_id, $token_id) {
        $game = $this->db->query("SELECT * FROM ludo_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        if ((int)$game['current_turn_user_id'] !== (int)$user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }
        if ($game['game_phase'] !== 'select_token' || empty($game['dice_value'])) {
            return ['ok' => false, 'message' => 'Fase pergerakan pion tidak valid!'];
        }

        $dice = (int)$game['dice_value'];
        $token = $this->db->query(
            "SELECT * FROM ludo_tokens WHERE id = ? AND ludo_game_id = ? AND user_id = ?",
            [$token_id, $game_id, $user_id]
        )->result_array();
        if (empty($token)) {
            return ['ok' => false, 'message' => 'Pion tidak ditemukan atau bukan milik Anda!'];
        }
        $token = $token[0];

        $board_cfg = self::get_board_config();
        $color = $token['color'];
        $start_offset = $board_cfg['color_offsets'][$color];
        $safe_squares = $board_cfg['safe_squares'];

        $captured_token = null;
        $bonus_roll = false;
        $is_finished = false;
        $steps_path = []; // Daftar urutan langkah petak demi petak untuk animasi walking di HP

        // ── SKENARIO 1: Pion Keluar dari Markas (Yard) ──────────────
        if ($token['state'] === 'yard') {
            if ($dice !== 6) {
                return ['ok' => false, 'message' => 'Pion di Markas butuh angka 6 untuk keluar!'];
            }

            $new_state = 'track';
            $new_pos = $start_offset; // Petak Start warna pion
            $new_steps = 0;
            $steps_path[] = ['type' => 'track', 'pos' => $new_pos];

            $this->db->query(
                "UPDATE ludo_tokens SET state = ?, position = ?, steps_moved = ? WHERE id = ?",
                [$new_state, $new_pos, $new_steps, $token['id']]
            );

            // Lemparan dadu bernilai 6 selalu mendapat bonus lemparan lagi!
            $bonus_roll = true;
            $action_desc = "Pion keluar dari Markas ke petak Start! Mendapat bonus lemparan dadu.";
            $this->add_log($game_id, $user_id, 'TOKEN_OUT', "Pion #{$token['token_index']} keluar dari Markas ke petak Start.");
        }
        // ── SKENARIO 2: Pion Bergerak di Track atau Home Run ────────
        else {
            $cur_steps = (int)$token['steps_moved'];
            $target_steps = $cur_steps + $dice;

            if ($target_steps > 56) {
                return ['ok' => false, 'message' => 'Angka dadu terlalu besar untuk masuk ke Segitiga Kemenangan (butuh angka pas)!'];
            }

            // Bangun path langkah satu per satu untuk animasi halus di HP
            for ($s = $cur_steps + 1; $s <= $target_steps; $s++) {
                if ($s < 51) {
                    $steps_path[] = ['type' => 'track', 'pos' => ($start_offset + $s) % 52];
                } elseif ($s >= 51 && $s < 56) {
                    $steps_path[] = ['type' => 'home_run', 'pos' => 100 + ($s - 51)];
                } else {
                    $steps_path[] = ['type' => 'finish', 'pos' => 105];
                }
            }

            // Evaluasi Posisi Akhir
            if ($target_steps === 56) {
                // MASUK FINISH (Segitiga Kemenangan)
                $new_state = 'finished';
                $new_pos = 105;
                $new_steps = 56;
                $is_finished = true;
                $bonus_roll = true; // Masuk finish menghadiahkan 1x lemparan bonus!

                $this->db->query(
                    "UPDATE ludo_tokens SET state = ?, position = ?, steps_moved = ? WHERE id = ?",
                    [$new_state, $new_pos, $new_steps, $token['id']]
                );

                // Tambah jumlah pion finish
                $this->db->query(
                    "UPDATE ludo_players SET tokens_finished = tokens_finished + 1 WHERE ludo_game_id = ? AND user_id = ?",
                    [$game_id, $user_id]
                );

                $action_desc = "Pion #{$token['token_index']} berhasil masuk ke Segitiga Kemenangan! Mendapat bonus lemparan.";
                $this->add_log($game_id, $user_id, 'TOKEN_FINISH', "🎯 Pion berhasil masuk ke Segitiga Kemenangan (Finish)!");
            } elseif ($target_steps >= 51) {
                // MASUK JALUR HOME RUN WARNA KHUSUS
                $new_state = 'home_run';
                $new_pos = 100 + ($target_steps - 51); // 100 .. 104
                $new_steps = $target_steps;

                $this->db->query(
                    "UPDATE ludo_tokens SET state = ?, position = ?, steps_moved = ? WHERE id = ?",
                    [$new_state, $new_pos, $new_steps, $token['id']]
                );

                $bonus_roll = ($dice === 6);
                $action_desc = "Pion melangkah ke jalur aman Home Run menuju Finish.";
                $this->add_log($game_id, $user_id, 'MOVE_HOME', "Pion bergerak di jalur Home Run (+{$dice} langkah).");
            } else {
                // BERJALAN DI JALUR SIRKULAR UTAMA (0 .. 51)
                $new_state = 'track';
                $new_pos = ($start_offset + $target_steps) % 52;
                $new_steps = $target_steps;

                // ── MEKANIK MAKAN PION LAWAN (CAPTURE / KNOCKOUT) ──
                $is_safe_square = in_array($new_pos, $safe_squares);

                if (!$is_safe_square) {
                    // Cek apakah ada pion lawan di petak pendaratan ini
                    $victim = $this->db->query(
                        "SELECT lt.*, lp.color AS opp_color, u.username AS opp_name 
                         FROM ludo_tokens lt
                         JOIN ludo_players lp ON lt.user_id = lp.user_id AND lt.ludo_game_id = lp.ludo_game_id
                         JOIN users u ON lt.user_id = u.id
                         WHERE lt.ludo_game_id = ? AND lt.user_id != ? AND lt.state = 'track' AND lt.position = ?
                         LIMIT 1",
                        [$game_id, $user_id, $new_pos]
                    )->result_array();

                    if (!empty($victim)) {
                        $v = $victim[0];
                        // TENDANG PION LAWAN KEMBALI KE MARKAS (YARD)!
                        $this->db->query(
                            "UPDATE ludo_tokens SET state = 'yard', position = -1, steps_moved = 0 WHERE id = ?",
                            [$v['id']]
                        );

                        $captured_token = [
                            'id'          => (int)$v['id'],
                            'user_id'     => (int)$v['user_id'],
                            'color'       => $v['opp_color'],
                            'token_index' => (int)$v['token_index'],
                            'opp_name'    => $v['opp_name'],
                        ];

                        // Hadiah memakan lawan: BONUS 1X LEMPARAN DADU!
                        $bonus_roll = true;
                        $action_desc = "⚔️ Memakan pion {$v['opp_name']} ({$v['opp_color']}) di petak #{$new_pos}! Lawan ditendang ke Markas. Mendapat lemparan bonus!";
                        $this->add_log($game_id, $user_id, 'CAPTURE', "⚔️ Berhasil MEMAKAN pion {$v['opp_name']} di petak #{$new_pos}! Pion lawan dipulangkan ke Markas.");
                    } else {
                        $bonus_roll = ($dice === 6);
                        $action_desc = "Pion melangkah {$dice} petak ke petak #{$new_pos}.";
                    }
                } else {
                    // Mendarat di Petak Aman (Bintang / Start)
                    $bonus_roll = ($dice === 6);
                    $action_desc = "Pion mendarat di Petak Aman (★ #{$new_pos}).";
                    $this->add_log($game_id, $user_id, 'MOVE_SAFE', "Pion mendarat di Petak Aman #{$new_pos}.");
                }

                $this->db->query(
                    "UPDATE ludo_tokens SET state = ?, position = ?, steps_moved = ? WHERE id = ?",
                    [$new_state, $new_pos, $new_steps, $token['id']]
                );
            }
        }

        // ── Evaluasi Kemenangan Pemain ────────────────────────────────
        $p_info = $this->db->query("SELECT * FROM ludo_players WHERE ludo_game_id = ? AND user_id = ?", [$game_id, $user_id])->result_array();
        $p_info = $p_info[0];
        $all_finished = ((int)$p_info['tokens_finished'] >= 4);

        if ($all_finished && (int)$p_info['finished_rank'] === 0) {
            $current_max_rank = (int)$this->db->query("SELECT MAX(finished_rank) as max_r FROM ludo_players WHERE ludo_game_id = ?", [$game_id])->result_array()[0]['max_r'];
            $my_rank = $current_max_rank + 1;

            $this->db->query("UPDATE ludo_players SET finished_rank = ? WHERE id = ?", [$my_rank, $p_info['id']]);

            $u_info = $this->db->query("SELECT username FROM users WHERE id = ?", [$user_id])->result_array();
            $my_name = $u_info[0]['username'] ?? 'Pemain';
            $this->add_log($game_id, $user_id, 'VICTORY_RANK', "🏆 {$my_name} telah menyelesaikan seluruh 4 pionnya dan meraih JUARA {$my_rank}!");

            if ($my_rank === 1) {
                // Pemenang Pertama!
                $this->db->query("UPDATE ludo_games SET winner_user_id = ? WHERE id = ?", [$user_id, $game_id]);
                $this->db->query("UPDATE users SET ludo_wins = ludo_wins + 1 WHERE id = ?", [$user_id]);
            }
        }

        // Cek sisa pemain yang belum selesai
        $active_unfinished = $this->db->query(
            "SELECT * FROM ludo_players WHERE ludo_game_id = ? AND finished_rank = 0 AND is_resigned = 0",
            [$game_id]
        )->result_array();

        $game_ended = false;
        if (count($active_unfinished) <= 1) {
            // Permainan Selesai Total
            $game_ended = true;
            $this->db->query("UPDATE ludo_games SET game_phase = 'ended' WHERE id = ?", [$game_id]);
            $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            $this->add_log($game_id, $user_id, 'GAME_OVER', "🏁 Seluruh pertandingan Ludo telah selesai!");
        }

        // Tentukan Giliran Selanjutnya
        $next_turn_user_id = (int)$user_id;
        if (!$game_ended) {
            // Jika pemain yang baru jalan sudah selesai (4 pion masuk) atau tidak dapat bonus lempar
            if ($all_finished || !$bonus_roll) {
                $next_turn_user_id = $this->get_next_active_player_id($game_id, $user_id);
            }
        }

        // Update Game Sesi
        $this->db->query(
            "UPDATE ludo_games SET current_turn_user_id = ?, game_phase = ?, last_action = 'MOVE', last_action_desc = ? WHERE id = ?",
            [$next_turn_user_id, $game_ended ? 'ended' : 'roll', $action_desc, $game_id]
        );
        $this->bump_version($game_id, $room_id);

        return [
            'ok'             => true,
            'token_id'       => (int)$token['id'],
            'color'          => $color,
            'token_index'    => (int)$token['token_index'],
            'new_state'      => $new_state,
            'new_pos'        => $new_pos,
            'new_steps'      => $new_steps,
            'steps_path'     => $steps_path,
            'captured_token' => $captured_token,
            'bonus_roll'     => $bonus_roll,
            'is_finished'    => $is_finished,
            'game_ended'     => $game_ended,
            'message'        => $action_desc,
        ];
    }

    // ── Helper Rotasi Giliran Pemain Aktif ─────────────────────────
    private function get_next_active_player_id($game_id, $current_user_id) {
        $players = $this->db->query(
            "SELECT * FROM ludo_players WHERE ludo_game_id = ? ORDER BY turn_order ASC",
            [$game_id]
        )->result_array();

        if (empty($players)) return $current_user_id;

        $total = count($players);
        $cur_idx = 0;
        foreach ($players as $idx => $p) {
            if ((int)$p['user_id'] === (int)$current_user_id) {
                $cur_idx = $idx;
                break;
            }
        }

        // Cari pemain berikutnya yang belum tamat (finished_rank = 0) dan belum menyerah (is_resigned = 0)
        for ($i = 1; $i <= $total; $i++) {
            $candidate = $players[($cur_idx + $i) % $total];
            if ((int)$candidate['finished_rank'] === 0 && (int)$candidate['is_resigned'] === 0) {
                return (int)$candidate['user_id'];
            }
        }

        return $current_user_id;
    }

    // ── Aksi: Menyerah & Keluar ke Lobby ───────────────────────────
    public function surrender($room_id, $user_id) {
        $game = $this->db->query("SELECT * FROM ludo_games WHERE room_id = ?", [$room_id])->result_array();
        if (empty($game)) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        $game = $game[0];
        $game_id = (int)$game['id'];

        $player = $this->db->query(
            "SELECT lp.*, u.username FROM ludo_players lp JOIN users u ON lp.user_id = u.id WHERE lp.ludo_game_id = ? AND lp.user_id = ?",
            [$game_id, $user_id]
        )->result_array();
        if (empty($player)) return ['ok' => false, 'message' => 'Pemain tidak ditemukan.'];
        $player = $player[0];
        $username = $player['username'];

        // 1. Tandai resigned
        $this->db->query("UPDATE ludo_players SET is_resigned = 1 WHERE id = ?", [$player['id']]);

        // 2. Kembalikan semua pion ke markas
        $this->db->query("UPDATE ludo_tokens SET state = 'yard', position = -1, steps_moved = 0 WHERE ludo_game_id = ? AND user_id = ?", [$game_id, $user_id]);

        // 3. Catat log
        $this->add_log($game_id, $user_id, 'SURRENDER', "🏳️ {$username} telah menyerah dan keluar ke Lobby.");

        // 4. Keluarkan dari room_players
        if (!class_exists('Room_model')) {
            require_once dirname(dirname(__DIR__)) . '/lobby/models/Room_model.php';
        }
        $room_model = new Room_model();
        $room_model->leave_room($room_id, $user_id);

        // 5. Cek sisa pemain yang masih aktif
        $active_players = $this->db->query(
            "SELECT lp.*, u.username FROM ludo_players lp JOIN users u ON lp.user_id = u.id WHERE lp.ludo_game_id = ? AND lp.finished_rank = 0 AND lp.is_resigned = 0",
            [$game_id]
        )->result_array();

        if (count($active_players) <= 1) {
            $winner = $active_players[0] ?? null;
            $winner_id = $winner ? (int)$winner['user_id'] : null;

            if ($winner_id) {
                $winner_name = $winner['username'];
                $this->db->query("UPDATE ludo_games SET winner_user_id = ?, game_phase = 'ended' WHERE id = ?", [$winner_id, $game_id]);
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
                $this->db->query("UPDATE users SET ludo_wins = ludo_wins + 1 WHERE id = ?", [$winner_id]);
                $this->add_log($game_id, $winner_id, 'VICTORY', "🏆 {$winner_name} dinyatakan sebagai PEMENANG karena lawan telah menyerah!");
            } else {
                $this->db->query("UPDATE ludo_games SET game_phase = 'ended' WHERE id = ?", [$game_id]);
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            }

            $this->bump_version($game_id, $room_id);
            return [
                'ok'         => true,
                'game_ended' => true,
                'winner_id'  => $winner_id,
                'message'    => 'Anda telah menyerah. Permainan selesai.',
            ];
        }

        // Jika pemain yang menyerah sedang memegang giliran, alihkan giliran
        if ((int)$game['current_turn_user_id'] === (int)$user_id) {
            $next_player_id = $this->get_next_active_player_id($game_id, $user_id);
            $this->db->query("UPDATE ludo_games SET current_turn_user_id = ?, game_phase = 'roll' WHERE id = ?", [$next_player_id, $game_id]);
        }

        $this->bump_version($game_id, $room_id);
        return [
            'ok'         => true,
            'game_ended' => false,
            'message'    => 'Anda telah menyerah. Permainan tetap berlanjut.',
        ];
    }

    private function add_log($game_id, $user_id, $action_type, $message) {
        $this->db->table('ludo_logs')->insert([
            'ludo_game_id' => $game_id,
            'user_id'      => $user_id,
            'action_type'  => $action_type,
            'message'      => $message,
        ]);
    }
}
