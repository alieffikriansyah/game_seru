<?php

class Uno extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('uno/uno_model');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Arena Game UNO ────────────────────────────────────────
    public function game($room_code = '') {
        if (empty($room_code)) { redirect('lobby'); return; }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room || $room->game_type !== 'uno' || $room->status !== 'playing') {
            Session::set_flashdata('error', 'Room UNO tidak ditemukan atau belum dimulai.');
            redirect('lobby');
            return;
        }

        $user_id = Session::get('user_id');
        $state   = $this->uno_model->get_state($room->id, $user_id);
        if (!$state) { redirect('lobby'); return; }

        $data['title']      = 'UNO — ' . $room->room_name;
        $data['room']       = $room;
        $data['version']    = $this->uno_model->get_state_version($room->id);
        $data['state']      = $state;
        $data['user_id']    = $user_id;
        $data['username']   = Session::get('username');
        $data['room_code']  = $room_code;
        $this->load->view('uno/v_game', $data);
    }

    // ── Polling State (JSON) ──────────────────────────────────
    public function poll($room_code = '') {
        $room_code      = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);
        $user_id        = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false], 404);

        $server_version = $this->uno_model->get_state_version($room->id);

        if ($client_version >= $server_version) {
            json_response(['updated' => false, 'version' => $server_version]);
        }

        $state = $this->uno_model->get_state($room->id, $user_id);
        if (!$state) json_response(['updated' => false, 'version' => $server_version]);

        json_response(array_merge(['updated' => true, 'version' => $server_version], $state));
    }

    // Alias untuk polling state sesuai spesifikasi
    public function getState($room_code = '') {
        $this->poll($room_code);
    }

    // ── Aksi: Mainkan Kartu ───────────────────────────────────
    public function aksi_mainkan() {
        require_post('lobby');

        $room_code  = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $card_ids   = json_decode($_POST['card_ids'] ?? '[]', true);
        $wild_color = $this->input->xss_clean($_POST['wild_color'] ?? '');
        $user_id    = Session::get('user_id');

        if (!is_array($card_ids) || empty($card_ids)) {
            json_response(['ok' => false, 'message' => 'Pilih kartu yang ingin dimainkan.']);
        }

        // Sanitasi card_ids jadi integer
        $card_ids = array_map('intval', $card_ids);
        $card_ids = array_filter($card_ids, function($id) { return $id > 0; });

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $result = $this->uno_model->play_cards($room->id, $user_id, array_values($card_ids), $wild_color);
        if ($result['ok']) {
            $result['state']   = $this->uno_model->get_state($room->id, $user_id);
            $result['version'] = $this->uno_model->get_state_version($room->id);
        }
        json_response($result);
    }

    // ── Aksi: Ambil Kartu ─────────────────────────────────────
    public function aksi_ambil() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $result = $this->uno_model->draw_card($room->id, $user_id);
        if ($result['ok']) {
            $result['state']   = $this->uno_model->get_state($room->id, $user_id);
            $result['version'] = $this->uno_model->get_state_version($room->id);
        }
        json_response($result);
    }

    // ── Aksi: Teriak UNO ──────────────────────────────────────
    public function aksi_uno() {
        require_post('lobby');
        // Placeholder — penalty UNO disederhanakan dulu
        json_response(['ok' => true, 'message' => 'UNO! 🎴']);
    }

    // ── Aksi: Menyerah dari Permainan ─────────────────────────────
    public function aksi_menyerah() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

        $result = $this->uno_model->surrender($room->id, $user_id);
        $result['redirect'] = site_url('lobby');

        json_response($result);
    }

    // ── Middleware ────────────────────────────────────────────
    public function _middleware($method) {
        // auth_required sudah di __construct
    }
}
