<?php

class Uno_model extends Model {

    public function __construct() {
        parent::__construct();
    }

    // ═══════════════════════════════════════════════════════
    // INIT GAME
    // ═══════════════════════════════════════════════════════

    /**
     * Inisialisasi game UNO baru: buat 108 kartu, kocok, bagi ke pemain
     */
    public function init_game($room_id, $players) {
        $deck = $this->_generate_deck();
        shuffle($deck);

        // Tentukan kartu pertama (non-wild, non-aksi) untuk discard pile
        $first_idx = 0;
        foreach ($deck as $idx => $card) {
            if ($card['color'] !== 'wild' && is_numeric($card['value'])) {
                $first_idx = $idx;
                break;
            }
        }
        $first_card = $deck[$first_idx];
        array_splice($deck, $first_idx, 1);

        // Tentukan pemain pertama (seat_order 1)
        usort($players, function($a, $b) { return $a['seat_order'] - $b['seat_order']; });
        $first_player = $players[0];

        // Insert uno_games
        $this->db->table('uno_games')->insert([
            'room_id'              => $room_id,
            'current_turn_user_id' => $first_player['user_id'],
            'direction'            => 1,
            'active_color'         => $first_card['color'],
            'active_value'         => $first_card['value'],
            'draw_stack'           => 0,
            'winner_user_id'       => null,
        ]);
        $game_id = $this->db->last_insert_id();

        // Insert kartu di discard pile dulu
        $this->db->table('uno_cards')->insert([
            'uno_game_id'   => $game_id,
            'card_color'    => $first_card['color'],
            'card_value'    => $first_card['value'],
            'location'      => 'discard',
            'owner_user_id' => null,
            'discard_order' => 1,
        ]);

        // Bagi 7 kartu ke tiap pemain
        $player_count = count($players);
        $hand_size = 7;
        $deal_idx = 0;

        foreach ($players as $p) {
            for ($i = 0; $i < $hand_size; $i++) {
                $card = $deck[$deal_idx++];
                $this->db->table('uno_cards')->insert([
                    'uno_game_id'   => $game_id,
                    'card_color'    => $card['color'],
                    'card_value'    => $card['value'],
                    'location'      => 'hand',
                    'owner_user_id' => $p['user_id'],
                    'discard_order' => 0,
                ]);
            }
        }

        // Sisa kartu = deck
        for ($i = $deal_idx; $i < count($deck); $i++) {
            $card = $deck[$i];
            $this->db->table('uno_cards')->insert([
                'uno_game_id'   => $game_id,
                'card_color'    => $card['color'],
                'card_value'    => $card['value'],
                'location'      => 'deck',
                'owner_user_id' => null,
                'discard_order' => 0,
            ]);
        }

        return $game_id;
    }

    /**
     * Generate 108 kartu UNO standar
     */
    private function _generate_deck() {
        $colors = ['merah', 'kuning', 'hijau', 'biru'];
        $values_single = ['0'];
        $values_double = ['1','2','3','4','5','6','7','8','9','skip','reverse','tarik2'];
        $deck = [];

        foreach ($colors as $color) {
            // 1x kartu 0
            $deck[] = ['color' => $color, 'value' => '0'];
            // 2x kartu 1-9, skip, reverse, +2
            foreach ($values_double as $val) {
                $deck[] = ['color' => $color, 'value' => $val];
                $deck[] = ['color' => $color, 'value' => $val];
            }
        }

        // 4x Wild + 4x Wild+4
        for ($i = 0; $i < 4; $i++) {
            $deck[] = ['color' => 'wild', 'value' => 'wild'];
            $deck[] = ['color' => 'wild', 'value' => 'wild4'];
        }

        return $deck;
    }

    // ═══════════════════════════════════════════════════════
    // GET STATE (Aman — tidak bocorkan kartu lawan)
    // ═══════════════════════════════════════════════════════

    public function get_state($room_id, $user_id) {
        // Ambil game
        $game = $this->db->table('uno_games')->where('room_id', $room_id)->get()->row();
        if (!$game) return null;

        // Kartu teratas
        $top_card = $this->db->query(
            "SELECT card_color, card_value FROM uno_cards
             WHERE uno_game_id = ? AND location = 'discard'
             ORDER BY discard_order DESC LIMIT 1",
            [$game->id]
        )->row();

        // Kartu di tangan pemain sendiri
        $my_hand = $this->db->query(
            "SELECT id, card_color, card_value FROM uno_cards
             WHERE uno_game_id = ? AND location = 'hand' AND owner_user_id = ?
             ORDER BY card_color, card_value",
            [$game->id, $user_id]
        )->result_array();

        // Jumlah kartu deck
        $deck_count = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM uno_cards WHERE uno_game_id = ? AND location = 'deck'",
            [$game->id]
        )->row()->cnt;

        // Info lawan (hanya username + jumlah kartu)
        $opponents = $this->db->query(
            "SELECT u.id AS user_id, u.username, u.avatar,
                    COUNT(uc.id) AS card_count
             FROM room_players rp
             JOIN users u ON rp.user_id = u.id
             LEFT JOIN uno_cards uc ON uc.owner_user_id = u.id AND uc.uno_game_id = ? AND uc.location = 'hand'
             WHERE rp.room_id = ? AND u.id != ?
             GROUP BY u.id, u.username, u.avatar
             ORDER BY rp.seat_order",
            [$game->id, $room_id, $user_id]
        )->result_array();

        // Seat order untuk giliran
        $seat_map = $this->db->query(
            "SELECT u.id AS user_id, u.username, rp.seat_order
             FROM room_players rp JOIN users u ON rp.user_id = u.id
             WHERE rp.room_id = ?
             ORDER BY rp.seat_order",
            [$room_id]
        )->result_array();

        // Info pemenang jika game sudah selesai
        $winner_name = null;
        if ($game->winner_user_id) {
            $w = $this->db->table('users')->where('id', $game->winner_user_id)->get()->row();
            $winner_name = $w ? $w->username : 'Pemain';
        }

        return [
            'game_id'               => (int)$game->id,
            'current_turn_user_id'  => (int)$game->current_turn_user_id,
            'direction'             => (int)$game->direction,
            'active_color'          => $game->active_color,
            'active_value'          => $game->active_value,
            'draw_stack'            => (int)$game->draw_stack,
            'winner_user_id'        => $game->winner_user_id ? (int)$game->winner_user_id : null,
            'winner_username'       => $winner_name,
            'top_card'              => $top_card ? [
                'color' => $top_card->card_color,
                'value' => $top_card->card_value,
            ] : null,
            'deck_count'            => (int)$deck_count,
            'my_hand'               => $my_hand,
            'my_user_id'            => (int)$user_id,
            'is_my_turn'            => (int)$game->current_turn_user_id === (int)$user_id,
            'opponents'             => $opponents,
            'seat_map'              => $seat_map,
        ];
    }

    // ═══════════════════════════════════════════════════════
    // PLAY CARDS (Multi-Drop)
    // ═══════════════════════════════════════════════════════

    /**
     * Mainkan satu atau lebih kartu
     * @return ['ok' => bool, 'message' => string]
     */
    public function play_cards($room_id, $user_id, $card_ids, $wild_color = null) {
        $game = $this->db->table('uno_games')->where('room_id', $room_id)->get()->row();
        if (!$game) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        if ((int)$game->current_turn_user_id !== (int)$user_id) return ['ok' => false, 'message' => 'Bukan giliranmu!'];
        if ($game->winner_user_id) return ['ok' => false, 'message' => 'Game sudah selesai.'];

        // Ambil kartu yang dipilih (milik user ini, di tangan)
        $placeholders = implode(',', array_fill(0, count($card_ids), '?'));
        $params = array_merge([$game->id, $user_id], $card_ids);
        $selected = $this->db->query(
            "SELECT * FROM uno_cards WHERE uno_game_id = ? AND owner_user_id = ? AND location = 'hand' AND id IN ($placeholders)",
            $params
        )->result_array();

        if (count($selected) !== count($card_ids)) {
            return ['ok' => false, 'message' => 'Kartu tidak valid atau bukan milikmu.'];
        }

        // Urutkan sesuai urutan card_ids yang dikirim client
        $ordered = [];
        foreach ($card_ids as $cid) {
            foreach ($selected as $s) {
                if ($s['id'] == $cid) { $ordered[] = $s; break; }
            }
        }

        // Validasi draw_stack (Aturan Counter/Stacking: +4 tidak bisa dilawan +2, tapi +2 bisa dilawan +4)
        if ($game->draw_stack > 0) {
            $is_stack_plus4 = ($game->active_value === 'wild4');

            foreach ($ordered as $card) {
                if ($is_stack_plus4) {
                    // Kartu +4 TIDAK BISA dilawan dengan +2! Hanya bisa dilawan sesama +4
                    if ($card['card_value'] !== 'wild4') {
                        return [
                            'ok' => false,
                            'message' => "Kartu +4 tidak bisa dilawan dengan +2! Kamu harus lawan dengan +4 atau ambil {$game->draw_stack} kartu."
                        ];
                    }
                } else {
                    // Kartu +2 BISA dilawan dengan sesama +2 ATAU dilawan dengan +4
                    if ($card['card_value'] !== 'tarik2' && $card['card_value'] !== 'wild4') {
                        return [
                            'ok' => false,
                            'message' => "Ada serangan tarik +{$game->draw_stack}! Kamu harus lawan dengan +2 atau +4, atau ambil kartu."
                        ];
                    }
                }
            }
        }

        // Validasi multi-drop
        $validation = $this->_validate_multi_drop(
            $game->active_color,
            $game->active_value,
            $ordered
        );
        if (!$validation['ok']) return $validation;

        // Eksekusi: pindahkan kartu ke discard pile
        $max_discard = $this->db->query(
            "SELECT COALESCE(MAX(discard_order), 0) AS max_ord FROM uno_cards WHERE uno_game_id = ? AND location = 'discard'",
            [$game->id]
        )->row()->max_ord;

        $last_card  = end($ordered);
        $new_color  = $last_card['card_color'];
        $new_value  = $last_card['card_value'];
        $new_draw_stack = (int)$game->draw_stack;

        foreach ($ordered as $i => $card) {
            $this->db->query(
                "UPDATE uno_cards SET location='discard', owner_user_id=NULL, discard_order=? WHERE id=?",
                [$max_discard + $i + 1, $card['id']]
            );
        }

        // Wild: set warna yang dipilih
        if ($new_color === 'wild') {
            if (!in_array($wild_color, ['merah','kuning','hijau','biru'])) {
                return ['ok' => false, 'message' => 'Pilih warna untuk kartu Wild!'];
            }
            $new_color = $wild_color;
        }

        // Hitung efek kartu terakhir
        $skip_turns = 0;
        $direction  = (int)$game->direction;

        if ($new_value === 'reverse') {
            $direction *= -1;
            if ($this->_get_player_count($room_id) === 2) $skip_turns = 1;
        } elseif ($new_value === 'skip') {
            $skip_turns = count($ordered); // setiap kartu skip = skip 1 orang
        } elseif ($new_value === 'tarik2') {
            $new_draw_stack += 2 * count(array_filter($ordered, function($c) { return $c['card_value'] === 'tarik2'; }));
        } elseif ($new_value === 'wild4') {
            $new_draw_stack += 4 * count(array_filter($ordered, function($c) { return $c['card_value'] === 'wild4'; }));
        }

        // Cek apakah pemain menang (tangan kosong)
        $remaining = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM uno_cards WHERE uno_game_id = ? AND location='hand' AND owner_user_id=?",
            [$game->id, $user_id]
        )->row()->cnt;

        $winner_id = ($remaining == 0) ? $user_id : null;

        // Tentukan giliran berikutnya
        $next_user_id = $this->_next_turn($room_id, $user_id, $direction, $skip_turns);

        // Update uno_games
        $this->db->query(
            "UPDATE uno_games SET current_turn_user_id=?, direction=?, active_color=?, active_value=?, draw_stack=?, winner_user_id=? WHERE id=?",
            [$next_user_id, $direction, $new_color, $new_value, $new_draw_stack, $winner_id, $game->id]
        );

        // Bump state_version
        $this->_bump_state($room_id);

        // Jika menang, update statistik
        $winner_name = null;
        if ($winner_id) {
            $this->db->query("UPDATE users SET uno_wins = uno_wins + 1 WHERE id = ?", [$winner_id]);
            $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            $w = $this->db->table('users')->where('id', $winner_id)->get()->row();
            $winner_name = $w ? $w->username : 'Pemain';
        }

        return ['ok' => true, 'winner_id' => $winner_id ? (int)$winner_id : null, 'winner_name' => $winner_name];
    }

    /**
     * Validasi aturan multi-drop
     */
    private function _validate_multi_drop($active_color, $active_value, $cards) {
        if (empty($cards)) return ['ok' => false, 'message' => 'Tidak ada kartu yang dipilih.'];

        $first = $cards[0];
        $f_color = $first['card_color'];
        $f_value = $first['card_value'];

        // Kartu pertama harus cocok dengan meja
        $color_match = ($f_color === $active_color || $f_color === 'wild');
        $value_match = ($f_value === $active_value);
        if (!$color_match && !$value_match) {
            return ['ok' => false, 'message' => 'Kartu tidak cocok dengan kartu di meja!'];
        }

        // Wild hanya bisa 1 kartu per giliran
        if (($f_color === 'wild' || in_array('wild', array_column($cards, 'card_color'))) && count($cards) > 1) {
            return ['ok' => false, 'message' => 'Kartu Wild hanya bisa dimainkan satu per satu.'];
        }

        // Multi-drop: nomor yang sama atau pola kartu yang sama bisa dikeluarkan barengan meski warnanya beda!
        if (count($cards) > 1) {
            for ($i = 1; $i < count($cards); $i++) {
                if ($cards[$i]['card_value'] !== $f_value) {
                    return ['ok' => false, 'message' => 'Semua kartu yang dikeluarkan barengan harus memiliki nomor atau pola yang sama!'];
                }
            }
        }

        return ['ok' => true];
    }

    // ═══════════════════════════════════════════════════════
    // DRAW CARD
    // ═══════════════════════════════════════════════════════

    /**
     * Pemain ambil kartu dari deck
     * @return ['ok' => bool, 'cards' => array, 'can_play' => bool]
     */
    public function draw_card($room_id, $user_id) {
        $game = $this->db->table('uno_games')->where('room_id', $room_id)->get()->row();
        if (!$game) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];
        if ((int)$game->current_turn_user_id !== (int)$user_id) return ['ok' => false, 'message' => 'Bukan giliranmu!'];

        $draw_count = max(1, (int)$game->draw_stack);
        $drawn = [];

        for ($i = 0; $i < $draw_count; $i++) {
            $this->_ensure_deck($game->id);

            $card = $this->db->query(
                "SELECT * FROM uno_cards WHERE uno_game_id = ? AND location = 'deck' ORDER BY RAND() LIMIT 1",
                [$game->id]
            )->row();

            if (!$card) break; // deck habis

            $this->db->query(
                "UPDATE uno_cards SET location='hand', owner_user_id=? WHERE id=?",
                [$user_id, $card->id]
            );
            $drawn[] = ['id' => $card->id, 'color' => $card->card_color, 'value' => $card->card_value];
        }

        // Reset draw_stack & pindah giliran
        $direction = (int)$game->direction;
        $next_user_id = $this->_next_turn($room_id, $user_id, $direction, 0);

        $this->db->query(
            "UPDATE uno_games SET draw_stack=0, current_turn_user_id=? WHERE id=?",
            [$next_user_id, $game->id]
        );

        $this->_bump_state($room_id);

        // Cek apakah kartu yang baru diambil bisa dimainkan (jika hanya 1 kartu)
        $can_play = false;
        if ($game->draw_stack == 0 && count($drawn) === 1) {
            $d = $drawn[0];
            $validation = $this->_validate_multi_drop($game->active_color, $game->active_value, [
                ['card_color' => $d['color'], 'card_value' => $d['value'], 'id' => $d['id']]
            ]);
            $can_play = $validation['ok'];
        }

        return ['ok' => true, 'drawn' => $drawn, 'can_play' => $can_play, 'draw_count' => $draw_count];
    }

    /**
     * Pastikan deck tidak kosong — reshuffle discard pile
     */
    private function _ensure_deck($game_id) {
        $deck_count = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM uno_cards WHERE uno_game_id=? AND location='deck'",
            [$game_id]
        )->row()->cnt;

        if ($deck_count > 0) return;

        // Ambil semua kartu di discard kecuali yang teratas
        $top = $this->db->query(
            "SELECT id FROM uno_cards WHERE uno_game_id=? AND location='discard' ORDER BY discard_order DESC LIMIT 1",
            [$game_id]
        )->row();

        if (!$top) return;

        // Pindahkan semua discard (kecuali top) ke deck
        $this->db->query(
            "UPDATE uno_cards SET location='deck', discard_order=0 WHERE uno_game_id=? AND location='discard' AND id != ?",
            [$game_id, $top->id]
        );
    }

    // ═══════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════

    private function _next_turn($room_id, $current_user_id, $direction, $skip = 0) {
        $players = $this->db->query(
            "SELECT user_id, seat_order FROM room_players WHERE room_id=? ORDER BY seat_order",
            [$room_id]
        )->result_array();

        $n = count($players);
        if ($n === 0) return $current_user_id;

        $current_idx = 0;
        foreach ($players as $i => $p) {
            if ($p['user_id'] == $current_user_id) { $current_idx = $i; break; }
        }

        $steps = 1 + $skip;
        $next_idx = ($current_idx + ($direction === 1 ? $steps : -$steps) % $n + $n) % $n;

        return $players[$next_idx]['user_id'];
    }

    private function _get_player_count($room_id) {
        return (int)$this->db->query(
            "SELECT COUNT(*) AS cnt FROM room_players WHERE room_id=?",
            [$room_id]
        )->row()->cnt;
    }

    private function _bump_state($room_id) {
        $this->db->query(
            "UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?",
            [$room_id]
        );
    }

    public function get_game($room_id) {
        return $this->db->table('uno_games')->where('room_id', $room_id)->get()->row();
    }

    public function get_state_version($room_id) {
        $r = $this->db->table('game_rooms')->select('state_version')->where('id', $room_id)->get()->row();
        return $r ? (int)$r->state_version : 0;
    }

    /**
     * Aksi Menyerah (Surrender) dari Permainan UNO
     */
    public function surrender($room_id, $user_id) {
        $game = $this->get_game($room_id);
        if (!$game) return ['ok' => false, 'message' => 'Game tidak ditemukan.'];

        $user = $this->db->table('users')->where('id', $user_id)->get()->row();
        $username = $user ? $user->username : 'Pemain';

        // 1. Ambil daftar pemain yang sedang ada di room sebelum keluar
        $all_players = $this->db->query(
            "SELECT rp.*, u.username FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ? ORDER BY rp.seat_order ASC",
            [$room_id]
        )->result_array();

        $remaining = [];
        $cur_idx = 0;
        foreach ($all_players as $i => $p) {
            if ((int)$p['user_id'] === (int)$user_id) {
                $cur_idx = $i;
            } else {
                $remaining[] = $p;
            }
        }

        // 2. Kembalikan semua kartu yang dipegang user ke deck
        $this->db->query(
            "UPDATE uno_cards SET location = 'deck', owner_user_id = NULL WHERE uno_game_id = ? AND location = 'hand' AND owner_user_id = ?",
            [$game->id, $user_id]
        );

        // 3. Keluarkan dari room_players
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
                $this->db->query("UPDATE uno_games SET winner_user_id = ? WHERE id = ?", [$winner_id, $game->id]);
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
                $this->db->query("UPDATE users SET uno_wins = uno_wins + 1 WHERE id = ?", [$winner_id]);
            } else {
                $this->db->query("UPDATE game_rooms SET status = 'finished' WHERE id = ?", [$room_id]);
            }

            $this->_bump_state($room_id);
            return [
                'ok'         => true,
                'game_ended' => true,
                'winner_id'  => $winner_id,
                'message'    => 'Anda telah menyerah. Permainan berakhir karena hanya tersisa 1 pemain.',
            ];
        }

        // KONDISI 2: JIKA SISA 2 PEMAIN ATAU LEBIH -> PERMAINAN DILANJUTKAN
        // Jika giliran saat ini ada pada pemain yang menyerah, oper giliran ke pemain berikutnya
        if ((int)$game->current_turn_user_id === (int)$user_id) {
            $next_user_id = (int)$remaining[$cur_idx % $remaining_count]['user_id'];
            $this->db->query("UPDATE uno_games SET current_turn_user_id = ? WHERE id = ?", [$next_user_id, $game->id]);
        }

        $this->_bump_state($room_id);
        return [
            'ok'         => true,
            'game_ended' => false,
            'remaining'  => $remaining_count,
            'message'    => "Anda telah menyerah dan keluar ke Lobby. Permainan berlanjut dengan sisa {$remaining_count} pemain.",
        ];
    }
}
