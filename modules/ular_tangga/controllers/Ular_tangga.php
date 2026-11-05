<?php

class Ular_tangga extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('ular_tangga/ular_tangga_model');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Arena Game Ular Tangga ────────────────────────────────
    public function game($room_code = '') {
        if (empty($room_code)) {
            redirect('lobby');
            return;
        }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room || $room->game_type !== 'ular_tangga' || $room->status !== 'playing') {
            Session::set_flashdata('error', 'Room Ular Tangga tidak ditemukan atau belum dimulai.');
            redirect('lobby');
            return;
        }

        $user_id = Session::get('user_id');
        $state   = $this->ular_tangga_model->get_state($room->id, $user_id);
        if (!$state) {
            redirect('lobby');
            return;
        }

        $data['title']     = 'Ular Tangga — ' . $room->room_name;
        $data['room']      = $room;
        $data['version']   = $this->ular_tangga_model->get_state_version($room->id);
        $data['state']     = $state;
        $data['user_id']   = $user_id;
        $data['username']  = Session::get('username');
        $data['room_code'] = $room_code;
        $this->load->view('ular_tangga/v_game', $data);
    }

    // ── Polling State (JSON) ──────────────────────────────────
    public function poll($room_code = '') {
        $room_code      = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);
        $user_id        = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false], 404);

        $server_version = $this->ular_tangga_model->get_state_version($room->id);

        if ($client_version >= $server_version) {
            json_response(['updated' => false, 'version' => $server_version]);
        }

        $state = $this->ular_tangga_model->get_state($room->id, $user_id);
        if (!$state) json_response(['updated' => false, 'version' => $server_version]);

        json_response(array_merge(['updated' => true, 'version' => $server_version], $state));
    }

    // ── Aksi: Lempar / Kocok Dadu ─────────────────────────────
    public function aksi_kocok() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) {
            json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);
        }

        $result = $this->ular_tangga_model->roll_dice($room->id, $user_id);
        if ($result['ok']) {
            $result['state']   = $this->ular_tangga_model->get_state($room->id, $user_id);
            $result['version'] = $this->ular_tangga_model->get_state_version($room->id);
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

        $result = $this->ular_tangga_model->surrender($room->id, $user_id);
        $result['redirect'] = site_url('lobby');

        json_response($result);
    }

    // ── Middleware ────────────────────────────────────────────
    public function _middleware($method) {
        // auth_required sudah di __construct
    }
}
