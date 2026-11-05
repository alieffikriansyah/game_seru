<?php

class Skenalitik extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('skenalitik/skenalitik_model');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Halaman Arena Papan Skenalitik ────────────────────────────
    public function game($room_code = '') {
        if (empty($room_code)) {
            redirect('lobby');
            return;
        }

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room || $room->game_type !== 'skenalitik' || !in_array($room->status, ['playing', 'finished'])) {
            Session::set_flashdata('error', 'Room Skenalitik tidak ditemukan atau belum dimulai.');
            redirect('lobby');
            return;
        }

        $user_id = Session::get('user_id');
        $state   = $this->skenalitik_model->get_state($room->id, $user_id);
        if (!$state) {
            redirect('lobby');
            return;
        }

        $data['title']     = 'Skenalitik — ' . $room->room_name;
        $data['room']      = $room;
        $data['version']   = $this->skenalitik_model->get_state_version($room->id);
        $data['state']     = $state;
        $data['user_id']   = $user_id;
        $data['username']  = Session::get('username');
        $data['room_code'] = $room_code;

        $this->load->view('skenalitik/v_game', $data);
    }

    // ── Polling State Ringan & Hemat Bandwidth (JSON) ─────────────
    public function poll($room_code = '') {
        $room_code      = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);
        $user_id        = Session::get('user_id');

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) json_response(['ok' => false], 404);

        $server_version = $this->skenalitik_model->get_state_version($room->id);

        if ($client_version >= $server_version) {
            json_response(['updated' => false, 'version' => $server_version]);
        }

        $state = $this->skenalitik_model->get_state($room->id, $user_id);
        if (!$state) json_response(['updated' => false, 'version' => $server_version]);

        json_response(array_merge(['updated' => true, 'version' => $server_version], $state));
    }

    // ── Aksi: Lempar Dadu 2x D6 ───────────────────────────────────
    public function aksi_kocok() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->roll_dice($room->id, $user_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Beli Properti ───────────────────────────────────────
    public function aksi_beli() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->buy_property($room->id, $user_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Lewati Pembelian Properti ───────────────────────────
    public function aksi_lewat() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->skip_buy($room->id, $user_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Upgrade Bangunan ────────────────────────────────────
    public function aksi_upgrade() {
        require_post('lobby');

        try {
            $room_code    = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $tile_id      = (int)($_POST['tile_id'] ?? 0);
            $target_level = (int)($_POST['target_level'] ?? 0);
            $user_id      = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->upgrade_property($room->id, $user_id, $tile_id, $target_level);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Keluar Karantina (Denda / Kartu) ─────────────────────
    public function aksi_karantina() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $method    = trim($this->input->xss_clean($_POST['method'] ?? 'fine'));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->exit_jail($room->id, $user_id, $method);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Selesai Giliran ─────────────────────────────────────
    public function aksi_selesai_giliran() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->end_turn($room->id, $user_id);
            if (!empty($result['ok'])) {
                $result['state']   = $this->skenalitik_model->get_state($room->id, $user_id);
                $result['version'] = $this->skenalitik_model->get_state_version($room->id);
            }

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Aksi: Menyerah dari Permainan ─────────────────────────────
    public function aksi_menyerah() {
        require_post('lobby');

        try {
            $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
            $user_id   = Session::get('user_id');

            $room = $this->room_model->get_room_by_code($room_code);
            if (!$room) json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);

            $result = $this->skenalitik_model->surrender($room->id, $user_id);
            $result['redirect'] = site_url('lobby');

            json_response($result);
        } catch (\Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Middleware ────────────────────────────────────────────────
    public function _middleware($method) {
        // auth_required sudah di __construct
    }
}
