<?php

class Ular_tangga_model extends Model {

    // Konfigurasi Tangga (Kaki -> Puncak)
    const LADDERS = [
        4  => 14,
        9  => 31,
        20 => 38,
        28 => 84,
        40 => 59,
        51 => 67,
        63 => 81,
        71 => 91,
    ];

    // Konfigurasi Ular (Kepala -> Ekor)
    const SNAKES = [
        17 => 7,
        54 => 34,
        62 => 19,
        64 => 60,
        87 => 24,
        93 => 73,
        95 => 75,
        99 => 78,
    ];

    // Pilihan Pion & Warna untuk tiap pemain (berurutan berdasarkan seat)
    const PAWN_CONFIGS = [
        0 => ['color' => '#ef4444', 'name' => 'merah',  'icon' => '🦁'],
        1 => ['color' => '#3b82f6', 'name' => 'biru',   'icon' => '🚀'],
        2 => ['color' => '#10b981', 'name' => 'hijau',  'icon' => '🦖'],
        3 => ['color' => '#eab308', 'name' => 'kuning', 'icon' => '🐯'],
        4 => ['color' => '#8b5cf6', 'name' => 'ungu',   'icon' => '🦄'],
        5 => ['color' => '#f97316', 'name' => 'oranye', 'icon' => '🦊'],
    ];

    /**
     * Inisialisasi Game Ular Tangga Baru
     */
    public function init_game($room_id, $players) {
        // Urutkan pemain berdasarkan seat_order
        usort($players, function($a, $b) {
            return $a['seat_order'] - $b['seat_order'];
        });

        $first_player = $players[0];

        // Buat record game
        $this->db->table('ut_games')->insert([
            'room_id'              => $room_id,
            'current_turn_user_id' => $first_player['user_id'],
            'winner_user_id'       => null,
            'last_dice'            => null,
            'last_from_pos'        => null,
            'last_to_pos'          => null,
            'last_action'          => null,
            'consecutive_six'      => 0,
        ]);
        $game_id = $this->db->last_insert_id();

        // Insert setiap pemain ke ut_players di posisi 1
        foreach ($players as $idx => $p) {
            $pawn = self::PAWN_CONFIGS[$idx % count(self::PAWN_CONFIGS)];
            $this->db->table('ut_players')->insert([
                'ut_game_id'  => $game_id,
                'user_id'     => $p['user_id'],
                'position'    => 1,
                'pawn_color'  => $pawn['name'],
                'pawn_icon'   => $pawn['icon'],
                'rank'        => null,
                'total_rolls' => 0,
                'snakes_hit'  => 0,
                'ladders_hit' => 0,
            ]);
        }

        // Catat log pembuka
        $this->db->table('ut_logs')->insert([
            'ut_game_id'  => $game_id,
            'user_id'     => $first_player['user_id'],
            'dice'        => 0,
            'from_pos'    => 1,
            'to_pos'      => 1,
            'action_type' => 'normal',
            'message'     => 'Permainan Ular Tangga dimulai! Giliran pertama: ' . $first_player['username'],
        ]);

        $this->_bump_state_version($room_id);
        return $game_id;
    }

    /**
     * Ambil Game by room_id
     */
    public function get_game($room_id) {
        return $this->db->table('ut_games')->where('room_id', $room_id)->get()->row();
    }

    /**
     * Ambil state lengkap permainan Ular Tangga
     */
    public function get_state($room_id, $user_id) {
        $game = $this->get_game($room_id);
        if (!$game) return null;

        // Ambil data seluruh pemain di game ini
        $players = $this->db->query(
            "SELECT p.*, u.username, u.avatar, rp.seat_order
             FROM ut_players p
             JOIN users u ON p.user_id = u.id
             JOIN room_players rp ON rp.room_id = ? AND rp.user_id = u.id
             WHERE p.ut_game_id = ?
             ORDER BY rp.seat_order ASC",
            [$room_id, $game->id]
        )->result_array();

        // Format info pemain
        $formatted_players = [];
        $current_turn_player = null;
        foreach ($players as $p) {
            $is_turn = ($p['user_id'] == $game->current_turn_user_id);
            $p_info = [
                'user_id'     => (int)$p['user_id'],
                'username'    => $p['username'],
                'avatar'      => $p['avatar'] ?? 'default.png',
                'position'    => (int)$p['position'],
                'pawn_color'  => $p['pawn_color'],
                'pawn_icon'   => $p['pawn_icon'],
                'is_turn'     => $is_turn,
                'is_me'       => ($p['user_id'] == $user_id),
                'total_rolls' => (int)$p['total_rolls'],
                'snakes_hit'  => (int)$p['snakes_hit'],
                'ladders_hit' => (int)$p['ladders_hit'],
                'rank'        => $p['rank'] ? (int)$p['rank'] : null,
            ];
            $formatted_players[] = $p_info;
            if ($is_turn) {
                $current_turn_player = $p_info;
            }
        }

        // Ambil 10 log aksi terakhir
        $logs = $this->db->query(
            "SELECT l.*, u.username, p.pawn_color, p.pawn_icon
             FROM ut_logs l
             JOIN users u ON l.user_id = u.id
             LEFT JOIN ut_players p ON p.ut_game_id = l.ut_game_id AND p.user_id = l.user_id
             WHERE l.ut_game_id = ?
             ORDER BY l.id DESC LIMIT 10",
            [$game->id]
        )->result_array();

        // Data pemenang (jika ada)
        $winner_info = null;
        if ($game->winner_user_id) {
            $w = $this->db->table('users')->where('id', $game->winner_user_id)->get()->row();
            if ($w) {
                $winner_info = [
                    'user_id'  => (int)$w->id,
                    'username' => $w->username,
                    'avatar'   => $w->avatar ?? 'default.png',
                ];
            }
        }

        return [
            'game_id'              => (int)$game->id,
            'current_turn_user_id' => (int)$game->current_turn_user_id,
            'is_my_turn'           => ($game->current_turn_user_id == $user_id && !$game->winner_user_id),
            'current_turn_player'  => $current_turn_player,
            'winner_user_id'       => $game->winner_user_id ? (int)$game->winner_user_id : null,
            'winner'               => $winner_info,
            'last_dice'            => $game->last_dice ? (int)$game->last_dice : null,
            'last_from_pos'        => $game->last_from_pos ? (int)$game->last_from_pos : null,
            'last_to_pos'          => $game->last_to_pos ? (int)$game->last_to_pos : null,
            'last_action'          => $game->last_action,
            'consecutive_six'      => (int)$game->consecutive_six,
            'players'              => $formatted_players,
            'logs'                 => array_reverse($logs),
            'board_config'         => [
                'ladders' => self::LADDERS,
                'snakes'  => self::SNAKES,
            ],
        ];
    }

    /**
     * Lempar Dadu & Gerakkan Pion
     */
    public function roll_dice($room_id, $user_id, $override_dice = null) {
        $game = $this->get_game($room_id);
        if (!$game) {
            return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        }
        if ($game->winner_user_id !== null) {
            return ['ok' => false, 'message' => 'Permainan sudah selesai!'];
        }
        if ($game->current_turn_user_id != $user_id) {
            return ['ok' => false, 'message' => 'Bukan giliran Anda!'];
        }

        // Ambil data pemain saat ini
        $player = $this->db->table('ut_players')
            ->where('ut_game_id', $game->id)
            ->where('user_id', $user_id)
            ->get()->row();
        if (!$player) {
            return ['ok' => false, 'message' => 'Pemain tidak terdaftar dalam game.'];
        }

        $user = $this->db->table('users')->where('id', $user_id)->get()->row();
        $username = $user ? $user->username : 'Pemain';

        // 1. Nilai Dadu (1 - 6)
        $dice = ($override_dice !== null && $override_dice >= 1 && $override_dice <= 6)
            ? (int)$override_dice
            : rand(1, 6);

        $from_pos = (int)$player->position;
        $target_pos = $from_pos + $dice;

        $action_type = 'normal';
        $bounce_overshoot = 0;

        // 2. Cek Aturan Finish (Kotak 100 dengan Bounce)
        if ($target_pos > 100) {
            $bounce_overshoot = $target_pos - 100;
            $target_pos = 100 - $bounce_overshoot;
            $action_type = 'bounce';
        }

        // 3. Cek Tangga atau Ular di target_pos
        $final_pos = $target_pos;
        $snake_hit = false;
        $ladder_hit = false;

        if (isset(self::LADDERS[$target_pos])) {
            $final_pos = self::LADDERS[$target_pos];
            $ladder_hit = true;
            $action_type = 'ladder';
        } elseif (isset(self::SNAKES[$target_pos])) {
            $final_pos = self::SNAKES[$target_pos];
            $snake_hit = true;
            $action_type = 'snake';
        }

        // 4. Cek Kemenangan
        $is_winner = ($final_pos === 100);
        if ($is_winner) {
            $action_type = 'win';
        }

        // 5. Susun Pesan Log
        $dice_symbols = [1 => '⚀', 2 => '⚁', 3 => '⚂', 4 => '⚃', 5 => '⚄', 6 => '⚅'];
        $dice_icon = $dice_symbols[$dice] ?? "[$dice]";

        $log_msg = "{$player->pawn_icon} {$username} melempar dadu {$dice_icon} ({$dice})! ";
        if ($bounce_overshoot > 0) {
            $log_msg .= "Memantul di kotak 100 mundur ke {$target_pos}. ";
        } else {
            $log_msg .= "Maju ke kotak {$target_pos}. ";
        }

        if ($ladder_hit) {
            $log_msg .= "🪜 HORE! Naik tangga ke kotak {$final_pos}! ";
        } elseif ($snake_hit) {
            $log_msg .= "🐍 ADUH! Terpeleset ular turun ke kotak {$final_pos}! ";
        }

        if ($is_winner) {
            $log_msg .= "🏆 FINISH! {$username} MEMENANGKAN PERMAINAN!";
        }

        // 6. Update ut_players
        $update_player = [
            'position'    => $final_pos,
            'total_rolls' => $player->total_rolls + 1,
        ];
        if ($ladder_hit) $update_player['ladders_hit'] = $player->ladders_hit + 1;
        if ($snake_hit)  $update_player['snakes_hit']  = $player->snakes_hit + 1;
        if ($is_winner)  $update_player['rank'] = 1;

        $this->db->table('ut_players')
            ->where('id', $player->id)
            ->update($update_player);

        // 7. Tentukan Giliran Berikutnya
        // Aturan Dadu 6: Jika lempar 6 dan belum menang, dapat bonus lempar lagi! (Max 2 kali)
        $bonus_roll = ($dice === 6 && !$is_winner && $game->consecutive_six < 2);
        $next_consecutive = ($dice === 6) ? ($game->consecutive_six + 1) : 0;

        $next_turn_user_id = $game->current_turn_user_id;

        if ($is_winner) {
            $next_turn_user_id = $user_id; // Game selesai
            // Ubah status room ke 'finished'
            $this->db->table('game_rooms')->where('id', $room_id)->update(['status' => 'finished']);
        } elseif ($bonus_roll) {
            $log_msg .= "✨ Dadu 6! Mendapat giliran lempar lagi!";
        } else {
            // Pindah giliran ke pemain berikutnya
            $next_turn_user_id = $this->_get_next_turn_user_id($room_id, $user_id);
            $next_consecutive = 0;
        }

        // 8. Update ut_games
        $this->db->table('ut_games')
            ->where('id', $game->id)
            ->update([
                'current_turn_user_id' => $next_turn_user_id,
                'winner_user_id'       => $is_winner ? $user_id : null,
                'last_dice'            => $dice,
                'last_from_pos'        => $from_pos,
                'last_to_pos'          => $final_pos,
                'last_action'          => $action_type,
                'consecutive_six'      => $next_consecutive,
            ]);

        // 9. Simpan Log
        $this->db->table('ut_logs')->insert([
            'ut_game_id'  => $game->id,
            'user_id'     => $user_id,
            'dice'        => $dice,
            'from_pos'    => $from_pos,
            'to_pos'      => $final_pos,
            'action_type' => $action_type,
            'message'     => $log_msg,
        ]);

        // 10. Bump Version untuk Polling Instan
        $this->_bump_state_version($room_id);

        return [
            'ok'               => true,
            'dice'             => $dice,
            'from_pos'         => $from_pos,
            'target_pos'       => $target_pos,
            'final_pos'        => $final_pos,
            'action_type'      => $action_type,
            'bonus_roll'       => $bonus_roll,
            'is_winner'        => $is_winner,
            'message'          => $log_msg,
        ];
    }

    /**
     * Tentukan pemain giliran selanjutnya berdasarkan seat_order
     */
    private function _get_next_turn_user_id($room_id, $current_user_id) {
        $players = $this->db->query(
            "SELECT user_id, seat_order FROM room_players
             WHERE room_id = ?
             ORDER BY seat_order ASC",
            [$room_id]
        )->result_array();

        if (empty($players)) return $current_user_id;

        $count = count($players);
        $current_idx = 0;
        foreach ($players as $idx => $p) {
            if ($p['user_id'] == $current_user_id) {
                $current_idx = $idx;
                break;
            }
        }

        $next_idx = ($current_idx + 1) % $count;
        return (int)$players[$next_idx]['user_id'];
    }

    /**
     * Ambil state version
     */
    public function get_state_version($room_id) {
        $r = $this->db->table('game_rooms')->select('state_version')->where('id', $room_id)->get()->row();
        return $r ? (int)$r->state_version : 0;
    }

    /**
     * Bump state version
     */
    /**
     * Aksi Menyerah (Surrender) dari Permainan Ular Tangga
     */
    public function surrender($room_id, $user_id) {
        $game = $this->get_game($room_id);
        if (!$game) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];

        $user = $this->db->table('users')->where('id', $user_id)->get()->row();
        $username = $user ? $user->username : 'Pemain';

        // Ambil semua pemain saat ini di room sebelum user keluar
        $all_players = $this->db->query(
            "SELECT rp.*, u.username FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ? ORDER BY rp.seat_order ASC",
            [$room_id]
        )->result_array();

        // Cari sisa pemain selain user yang menyerah
        $remaining = [];
        $cur_idx = 0;
        foreach ($all_players as $i => $p) {
            if ((int)$p['user_id'] === (int)$user_id) {
                $cur_idx = $i;
            } else {
                $remaining[] = $p;
            }
        }

        // Tandai user di ut_players
        $this->db->table('ut_players')
            ->where('ut_game_id', $game->id)
            ->where('user_id', $user_id)
            ->update(['rank' => 99]);

        // Catat log
        $this->db->table('ut_logs')->insert([
            'ut_game_id'  => $game->id,
            'user_id'     => $user_id,
            'dice'        => 0,
            'from_pos'    => 0,
            'to_pos'      => 0,
            'action_type' => 'surrender',
            'message'     => "🏳️ {$username} telah menyerah dan keluar ke Lobby.",
        ]);

        // Keluarkan dari room_players
        if (!class_exists('Room_model')) {
            require_once dirname(dirname(__DIR__)) . '/lobby/models/Room_model.php';
        }
        $room_model = new Room_model();
        $room_model->leave_room($room_id, $user_id);

        $remaining_count = count($remaining);

        // KONDISI 1: JIKA SISA 1 PEMAIN (atau 0) -> GAME SELESAI, PEMAIN TERSISA MENANG
        if ($remaining_count <= 1) {
            $winner = $remaining[0] ?? null;
            $winner_id = $winner ? (int)$winner['user_id'] : null;

            if ($winner_id) {
                $winner_name = $winner['username'];
                $this->db->table('ut_games')->where('id', $game->id)->update([
                    'winner_user_id'       => $winner_id,
                    'current_turn_user_id' => $winner_id,
                ]);
                $this->db->table('ut_players')->where('ut_game_id', $game->id)->where('user_id', $winner_id)->update([
                    'rank' => 1
                ]);
                $this->db->table('game_rooms')->where('id', $room_id)->update(['status' => 'finished']);

                $this->db->table('ut_logs')->insert([
                    'ut_game_id'  => $game->id,
                    'user_id'     => $winner_id,
                    'dice'        => 0,
                    'from_pos'    => 0,
                    'to_pos'      => 0,
                    'action_type' => 'win',
                    'message'     => "🏆 FINISH! {$winner_name} MEMENANGKAN PERMAINAN karena lawan menyerah!",
                ]);
            } else {
                $this->db->table('game_rooms')->where('id', $room_id)->update(['status' => 'finished']);
            }

            $this->_bump_state_version($room_id);
            return [
                'ok'         => true,
                'game_ended' => true,
                'winner_id'  => $winner_id,
                'message'    => 'Anda telah menyerah. Permainan berakhir karena tersisa 1 pemain.',
            ];
        }

        // KONDISI 2: JIKA SISA 2 PEMAIN ATAU LEBIH -> PERMAINAN TETAP BERJALAN
        // Jika giliran saat ini ada pada pemain yang menyerah, oper giliran ke pemain berikutnya
        if ((int)$game->current_turn_user_id === (int)$user_id) {
            $next_user_id = (int)$remaining[$cur_idx % $remaining_count]['user_id'];
            $this->db->table('ut_games')->where('id', $game->id)->update([
                'current_turn_user_id' => $next_user_id,
                'consecutive_six'      => 0
            ]);
        }

        $this->_bump_state_version($room_id);
        return [
            'ok'         => true,
            'game_ended' => false,
            'remaining'  => $remaining_count,
            'message'    => "Anda telah menyerah dan keluar ke Lobby. Permainan berlanjut dengan sisa {$remaining_count} pemain.",
        ];
    }

    /**
     * Bump state version
     */
    private function _bump_state_version($room_id) {
        $this->db->query(
            "UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?",
            [$room_id]
        );
    }
}
