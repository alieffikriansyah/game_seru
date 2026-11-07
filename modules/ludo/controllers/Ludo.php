<?php

class Ludo extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('ludo/ludo_model');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Halaman Arena Papan Ludo ──────────────────────────────────
    public function game($room_code = '') {
        if (empty($room_code)) {
            redirect('lobby');
            return;
        }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room || $room->game_type !== 'ludo' || !in_array($room->status, ['playing', 'finished'])) {
            Session::set_flashdata('error', 'Room Ludo tidak ditemukan atau belum dimulai.');
            redirect('lobby');
            return;
        }

        $user_id = Session::get('user_id');
        $state   = $this->ludo_model->get_state($room->id, $user_id);
        if (!$state) {
            redirect('lobby');
            return;
        }

        $data['title']     = 'Ludo — ' . $room->room_name;
        $data['room']      = $room;
        $data['version']   = $this->ludo_model->get_state_version($room->id);
        $data['state']     = $state;
        $data['user_id']   = $user_id;
        $data['username']  = Session::get('username');
        $data['room_code'] = $room_code;

        $this->load->view('ludo/v_game', $data);
    }

    // ── Polling Realtime State Ringan (JSON) ───────────────────────
    public function poll($room_code = '') {
        $room_code      = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);
        $user_id        = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false], 404);

        $server_version = $this->ludo_model->get_state_version($room->id);

        if ($client_version >= $server_version) {
            json_response(['updated' => false, 'version' => $server_version]);
        }

        $state = $this->ludo_model->get_state($room->id, $user_id);
        if (!$state) json_response(['updated' => false, 'version' => $server_version]);

        json_response(array_merge(['updated' => true, 'version' => $server_version], $state));
    }

    // ── Aksi: Kocok / Lempar Dadu D6 ──────────────────────────────
    public function aksi_kocok() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->ludo_model->roll_dice($room->id, $user_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->ludo_model->get_state($room->id, $user_id);
                $result['version'] = $this->ludo_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Jalankan Bidak / Pion Terpilih ───────────────────────
    public function aksi_gerak_pion() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $token_id  = (int)($_POST['token_id'] ?? 0);
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->ludo_model->move_token($room->id, $user_id, $token_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->ludo_model->get_state($room->id, $user_id);
                $result['version'] = $this->ludo_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Menyerah dari Permainan Ludo ─────────────────────────
    public function aksi_menyerah() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->ludo_model->surrender($room->id, $user_id);
            $result['redirect'] = site_url('lobby');

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }
}
