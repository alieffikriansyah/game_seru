<?php

class Lobby extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('lobby/room_model');
        auth_required();
    }

    // ── Halaman Utama Lobby — Daftar Room ────────────────────
    public function index() {
        global $config;
        $this->room_model->cleanup_old_rooms();

        $rooms = $this->room_model->get_active_rooms();

        $data['title']         = 'Lobby — Game Seru 🎮';
        $data['rooms']         = $rooms;
        $data['username']      = Session::get('username');
        $data['avatar']        = Session::get('avatar');
        $data['flash_error']   = Session::flashdata('error');
        $data['flash_success'] = Session::flashdata('success');
        $data['uno_min']       = $config['uno_min_players'];
        $data['ww_min']        = $config['ww_min_players'];
        $data['ut_min']        = $config['ut_min_players'];
        $data['sk_min']        = $config['sk_min_players'];

        $this->load->view('lobby/v_lobby', $data);
    }

    // ── Buat Room Baru ────────────────────────────────────────
    public function buat() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data['title'] = 'Buat Room — Game Seru 🎮';
            $this->load->view('lobby/v_buat_room', $data);
            return;
        }

        require_post('lobby');

        $room_name = trim($this->input->xss_clean($_POST['room_name'] ?? ''));
        $game_type = trim($_POST['game_type'] ?? '');

        if (empty($room_name)) {
            Session::set_flashdata('error', 'Nama room tidak boleh kosong.');
            redirect('lobby');
            return;
        }
        if (!in_array($game_type, ['uno', 'werewolf', 'ular_tangga', 'skenalitik'])) {
            Session::set_flashdata('error', 'Pilih jenis game yang valid.');
            redirect('lobby');
            return;
        }
        if (strlen($room_name) > 50) {
            Session::set_flashdata('error', 'Nama room maksimal 50 karakter.');
            redirect('lobby');
            return;
        }

        $room_code = $this->room_model->create_room([
            'room_name'    => $room_name,
            'game_type'    => $game_type,
            'host_user_id' => Session::get('user_id'),
        ]);

        redirect('lobby/tunggu/' . $room_code);
    }

    // ── Gabung Room via Kode ──────────────────────────────────
    public function gabung() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');

        if (empty($room_code)) {
            Session::set_flashdata('error', 'Masukkan kode room terlebih dahulu.');
            redirect('lobby');
            return;
        }

        $result = $this->room_model->join_room($room_code, $user_id);

        if (!$result['ok']) {
            Session::set_flashdata('error', $result['message']);
            redirect('lobby');
            return;
        }

        redirect('lobby/tunggu/' . $result['room_code']);
    }

    // ── Waiting Room ──────────────────────────────────────────
    public function tunggu($room_code = '') {
        if (empty($room_code)) {
            redirect('lobby');
            return;
        }

        $room = $this->room_model->get_room_detail($room_code);
        if (!$room) {
            Session::set_flashdata('error', 'Room tidak ditemukan.');
            redirect('lobby');
            return;
        }

        // Jika game sudah dimulai, redirect ke arena
        if ($room['status'] === 'playing') {
            redirect($room['game_type'] . '/game/' . $room_code);
            return;
        }

        if ($room['status'] === 'finished') {
            Session::set_flashdata('error', 'Game sudah berakhir.');
            redirect('lobby');
            return;
        }

        // Pastikan user adalah bagian dari room ini
        $user_id = Session::get('user_id');
        $players = $this->room_model->get_players($room['id']);
        $is_member = false;
        foreach ($players as $p) {
            if ($p['user_id'] == $user_id) { $is_member = true; break; }
        }

        if (!$is_member) {
            // Auto-join jika belum bergabung
            $result = $this->room_model->join_room($room_code, $user_id);
            if (!$result['ok']) {
                Session::set_flashdata('error', $result['message']);
                redirect('lobby');
                return;
            }
            $players = $this->room_model->get_players($room['id']);
        }

        global $config;
        $data['title']     = 'Ruang Tunggu — ' . htmlspecialchars($room['room_name']);
        $data['room']      = $room;
        $data['players']   = $players;
        $data['user_id']   = $user_id;
        $data['username']  = Session::get('username');
        $data['is_host']   = ($room['host_user_id'] == $user_id);
        if ($room['game_type'] === 'uno') {
            $data['min_players'] = $config['uno_min_players'];
            $data['max_players'] = $config['uno_max_players'];
        } elseif ($room['game_type'] === 'ular_tangga') {
            $data['min_players'] = $config['ut_min_players'];
            $data['max_players'] = $config['ut_max_players'];
        } elseif ($room['game_type'] === 'skenalitik') {
            $data['min_players'] = $config['sk_min_players'];
            $data['max_players'] = $config['sk_max_players'];
        } else {
            $data['min_players'] = $config['ww_min_players'];
            $data['max_players'] = $config['ww_max_players'];
        }

        $this->load->view('lobby/v_waiting', $data);
    }

    // ── Toggle Siap ───────────────────────────────────────────
    public function siap() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');
        $room      = $this->room_model->get_room_by_code($room_code);

        if (!$room || $room->status !== 'waiting') {
            json_response(['ok' => false, 'message' => 'Room tidak valid.']);
        }

        $this->room_model->toggle_ready($room->id, $user_id);
        json_response(['ok' => true]);
    }

    // ── Mulai Game (Host Only) ────────────────────────────────
    public function mulai() {
        require_post('lobby');

        global $config;
        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');
        $room      = $this->room_model->get_room_by_code($room_code);

        if (!$room) {
            json_response(['ok' => false, 'message' => 'Room tidak ditemukan.']);
        }
        if ($room->host_user_id != $user_id) {
            json_response(['ok' => false, 'message' => 'Hanya host yang bisa memulai game.']);
        }
        if ($room->status !== 'waiting') {
            json_response(['ok' => false, 'message' => 'Game sudah dimulai.']);
        }

        $players = $this->room_model->get_players($room->id);
        $player_count = count($players);

        if ($room->game_type === 'uno') {
            $min = $config['uno_min_players'];
        } elseif ($room->game_type === 'ular_tangga') {
            $min = $config['ut_min_players'];
        } elseif ($room->game_type === 'skenalitik') {
            $min = $config['sk_min_players'];
        } else {
            $min = $config['ww_min_players'];
        }
        if ($player_count < $min) {
            json_response(['ok' => false, 'message' => "Minimal {$min} pemain untuk memulai game {$room->game_type}."]);
        }

        // Cek semua pemain selain host sudah ready
        foreach ($players as $p) {
            if ($p['user_id'] != $user_id && !$p['is_ready']) {
                json_response(['ok' => false, 'message' => 'Masih ada pemain yang belum siap!']);
            }
        }

        // Mulai game
        $this->room_model->start_game($room->id);

        // Inisialisasi game engine sesuai jenis
        if ($room->game_type === 'uno') {
            $this->load->model('uno/uno_model');
            $this->uno_model->init_game($room->id, $players);
        } elseif ($room->game_type === 'ular_tangga') {
            $this->load->model('ular_tangga/ular_tangga_model');
            $this->ular_tangga_model->init_game($room->id, $players);
        } elseif ($room->game_type === 'skenalitik') {
            $this->load->model('skenalitik/skenalitik_model');
            $this->skenalitik_model->init_game($room->id, $players);
        } else {
            $this->load->model('werewolf/werewolf_model');
            $this->werewolf_model->init_game($room->id, $players);
        }

        json_response([
            'ok'       => true,
            'redirect' => site_url($room->game_type . '/game/' . $room->room_code)
        ]);
    }

    // ── Keluar dari Room ──────────────────────────────────────
    public function keluar() {
        require_post('lobby');

        $room_code = strtoupper(trim($this->input->xss_clean($_POST['room_code'] ?? '')));
        $user_id   = Session::get('user_id');
        $room      = $this->room_model->get_room_by_code($room_code);

        if ($room && $room->status === 'waiting') {
            $this->room_model->leave_room($room->id, $user_id);
        }

        if (is_ajax()) {
            json_response(['ok' => true]);
        }
        redirect('lobby');
    }

    // ── Polling Waiting Room (JSON) ───────────────────────────
    public function poll_waiting($room_code = '') {
        $room_code = strtoupper(trim($room_code));
        $client_version = (int)($_GET['v'] ?? 0);

        $room = $this->room_model->get_room_by_code($room_code);
        if (!$room) {
            json_response(['ok' => false, 'message' => 'Room tidak ditemukan.'], 404);
        }

        // Jika game sudah dimulai, suruh client redirect
        if ($room->status === 'playing') {
            json_response([
                'ok'       => true,
                'started'  => true,
                'redirect' => site_url($room->game_type . '/game/' . $room->room_code),
            ]);
        }

        if ($room->status === 'finished') {
            json_response(['ok' => true, 'finished' => true, 'redirect' => site_url('lobby')]);
        }

        $server_version = (int)$room->state_version;

        if ($client_version >= $server_version) {
            json_response(['updated' => false, 'version' => $server_version]);
        }

        $players = $this->room_model->get_players($room->id);
        $all_ready = $this->room_model->all_ready($room->id);

        json_response([
            'updated'    => true,
            'version'    => $server_version,
            'host_id'    => (int)$room->host_user_id,
            'all_ready'  => $all_ready,
            'player_count' => count($players),
            'players'    => array_map(function($p) {
                return [
                    'user_id'    => (int)$p['user_id'],
                    'username'   => $p['username'],
                    'avatar'     => $p['avatar'],
                    'is_ready'   => (bool)$p['is_ready'],
                    'seat_order' => (int)$p['seat_order'],
                ];
            }, $players),
        ]);
    }

    // ── Middleware ────────────────────────────────────────────
    public function _middleware($method) {
        // auth_required sudah di __construct
    }
}
