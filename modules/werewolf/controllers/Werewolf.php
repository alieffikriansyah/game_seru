<?php

class Werewolf extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('werewolf/werewolf_model');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Arena Game Werewolf ───────────────────────────────────
    public function game($room_code = '') {
        if (empty($room_code)) { redirect('lobby'); return; }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room || $room->game_type !== 'werewolf' || $room->status !== 'playing') {
            Session::set_flashdata('error', 'Room Werewolf tidak ditemukan atau belum dimulai.');
            redirect('lobby');
            return;
        }

        $user_id = Session::get('user_id');
        $state   = $this->werewolf_model->get_state($room->id, $user_id);
        if (!$state) { redirect('lobby'); return; }

        // Ambil chat awal (50 pesan terakhir)
        $chats = $this->werewolf_model->get_chats($state['game_id'], $user_id, 0);

        $data['title']     = 'Werewolf — ' . $room->room_name;
        $data['room']      = $room;
        $data['state']     = $state;
        $data['chats']     = $chats;
        $data['user_id']   = $user_id;
        $data['username']  = Session::get('username');
        $data['room_code'] = $room_code;
        $this->load->view('werewolf/v_game', $data);
    }

    // ── Polling State ─────────────────────────────────────────
    public function poll($room_code = '') {
        $room_code      = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);
        $last_chat_id   = (int)($_GET['chat_id'] ?? 0);
        $user_id        = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false], 404);

        $server_version = $this->werewolf_model->get_state_version($room->id);

        // Selalu cek & advance fase (meski versi sama)
        $game = $this->werewolf_model->get_game($room->id);
        if ($game) {
            $this->werewolf_model->check_and_advance_phase($game->id, $room->id);
            $server_version = $this->werewolf_model->get_state_version($room->id);
        }

        // Ambil chat baru (selalu dikembalikan jika ada)
        $new_chats = $this->werewolf_model->get_chats($game->id, $user_id, $last_chat_id);

        if ($client_version >= $server_version && empty($new_chats)) {
            json_response(['updated' => false, 'version' => $server_version, 'chats' => []]);
        }

        $state = $this->werewolf_model->get_state($room->id, $user_id);
        if (!$state) json_response(['updated' => false, 'version' => $server_version, 'chats' => []]);

        json_response(array_merge(
            ['updated' => true],
            $state,
            ['new_chats' => $new_chats]
        ));
    }

    // ── Aksi Malam ───────────────────────────────────────────
    public function aksi_malam() {
        require_post('lobby');

        $room_code   = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $target_id   = (int)($_POST['target_user_id'] ?? 0);
        $action_type = $this->input->xss_clean($_POST['action_type'] ?? '');
        $user_id     = Session::get('user_id');

        $allowed_actions = ['mangsa', 'terawang', 'lindungi'];
        if (!in_array($action_type, $allowed_actions)) {
            json_response(['ok' => false, 'message' => 'Jenis aksi tidak valid.']);
        }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $game = $this->werewolf_model->get_game($room->id);
        if (!$game || $game->current_phase !== 'malam') {
            json_response(['ok' => false, 'message' => 'Bukan fase malam.']);
        }

        // Validasi role sesuai aksi
        $state = $this->werewolf_model->get_state($room->id, $user_id);
        $role  = $state['my_role'] ?? null;

        $role_action_map = [
            'werewolf'   => 'mangsa',
            'penerawang' => 'terawang',
            'pelindung'  => 'lindungi',
        ];
        if (!$role || !isset($role_action_map[$role]) || $role_action_map[$role] !== $action_type) {
            json_response(['ok' => false, 'message' => 'Kamu tidak punya kemampuan ini.']);
        }

        $result = $this->werewolf_model->submit_action(
            $game->id, $user_id, $target_id, $action_type, $game->day_count, 'malam'
        );
        json_response($result);
    }

    // ── Aksi Vote ─────────────────────────────────────────────
    public function aksi_vote() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $target_id = (int)($_POST['target_user_id'] ?? 0);
        $user_id   = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $game = $this->werewolf_model->get_game($room->id);
        if (!$game || $game->current_phase !== 'siang_voting') {
            json_response(['ok' => false, 'message' => 'Bukan fase voting.']);
        }

        $state = $this->werewolf_model->get_state($room->id, $user_id);
        if (!$state['my_is_alive']) {
            json_response(['ok' => false, 'message' => 'Pemain yang sudah tewas tidak bisa voting.']);
        }

        $result = $this->werewolf_model->submit_action(
            $game->id, $user_id, $target_id, 'vote_gantung', $game->day_count, 'siang_voting'
        );
        json_response($result);
    }

    // ── Kirim Chat ────────────────────────────────────────────
    public function kirim_chat() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $channel   = $this->input->xss_clean($_POST['channel'] ?? 'publik');
        $message   = trim($_POST['message'] ?? '');
        $user_id   = Session::get('user_id');

        if (!in_array($channel, ['publik', 'werewolf', 'arwah'])) {
            json_response(['ok' => false, 'message' => 'Channel tidak valid.']);
        }
        if (empty($message)) {
            json_response(['ok' => false, 'message' => 'Pesan tidak boleh kosong.']);
        }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $game = $this->werewolf_model->get_game($room->id);
        if (!$game) json_response(['ok' => false, 'message' => 'Game tidak ditemukan.']);

        $result = $this->werewolf_model->send_chat($game->id, $user_id, $channel, $message);

        if ($result['ok']) {
            // Bump state agar polling client lain mendapat update
            $this->db->query("UPDATE game_rooms SET state_version = state_version + 1 WHERE id = ?", [$room->id]);
        }

        json_response($result);
    }

    // ── Aksi: Menyerah dari Permainan ─────────────────────────────
    public function aksi_menyerah() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $result = $this->werewolf_model->surrender($room->id, $user_id);
        $result['redirect'] = site_url('lobby');

        json_response($result);
    }

    // ── Middleware ────────────────────────────────────────────
    public function _middleware($method) {
        // auth_required sudah di __construct
    }
}
