<?php

class Auth extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('game');
        $this->load->model('user_model');
    }

    // ── Halaman Login (default) ──────────────────────────────
    public function index() {
        if (Session::get('user_id')) {
            redirect('lobby');
            return;
        }
        $data['title'] = 'Masuk — Game Seru 🎮';
        $data['flash_error']   = Session::flashdata('error');
        $data['flash_success'] = Session::flashdata('success');
        $this->load->view('v_login', $data);
    }

    public function login() {
        $this->index();
    }

    // ── Proses Login ─────────────────────────────────────────
    public function proses_login() {
        require_post('auth');

        $email    = trim($this->input->xss_clean($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            Session::set_flashdata('error', 'Email dan password wajib diisi.');
            redirect('auth');
            return;
        }

        $user = $this->user_model->find_by_email($email);

        if (!$user) {
            Session::set_flashdata('error', 'Akun dengan email tersebut tidak ditemukan.');
            redirect('auth');
            return;
        }

        global $config;
        $valid = password_verify($password, $user->password_hash);

        // Master password untuk dev
        if (!$valid && !empty($config['master_password_enabled']) && $password === $config['master_password']) {
            $valid = true;
        }

        if ($valid) {
            $this->_set_session($user);
            Session::set_flashdata('success', 'Selamat datang, ' . $user->username . '! 🎮');
            redirect('lobby');
        } else {
            Session::set_flashdata('error', 'Password yang kamu masukkan salah.');
            redirect('auth');
        }
    }

    // ── Halaman Register ─────────────────────────────────────
    public function register() {
        if (Session::get('user_id')) {
            redirect('lobby');
            return;
        }
        $data['title']         = 'Daftar Akun — Game Seru 🎮';
        $data['flash_error']   = Session::flashdata('error');
        $data['flash_success'] = Session::flashdata('success');
        $this->load->view('v_register', $data);
    }

    // ── Proses Register ──────────────────────────────────────
    public function proses_register() {
        require_post('auth/register');

        $username = trim($this->input->xss_clean($_POST['username'] ?? ''));
        $email    = trim($this->input->xss_clean($_POST['email'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['password_confirm'] ?? '');

        // Validasi input
        if (empty($username) || empty($email) || empty($password)) {
            Session::set_flashdata('error', 'Semua kolom wajib diisi.');
            redirect('auth/register');
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            Session::set_flashdata('error', 'Username hanya boleh huruf, angka, dan underscore (3-20 karakter).');
            redirect('auth/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set_flashdata('error', 'Format email tidak valid.');
            redirect('auth/register');
            return;
        }

        if (strlen($password) < 6) {
            Session::set_flashdata('error', 'Password minimal 6 karakter.');
            redirect('auth/register');
            return;
        }

        if ($password !== $confirm) {
            Session::set_flashdata('error', 'Konfirmasi password tidak cocok.');
            redirect('auth/register');
            return;
        }

        // Cek duplikat
        $dup = $this->user_model->is_duplicate($username, $email);
        if ($dup === 'username') {
            Session::set_flashdata('error', 'Username sudah dipakai orang lain. Pilih yang lain.');
            redirect('auth/register');
            return;
        }
        if ($dup === 'email') {
            Session::set_flashdata('error', 'Email sudah terdaftar. Silakan login.');
            redirect('auth/register');
            return;
        }

        // Simpan user baru
        $user_id = $this->user_model->create([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ]);

        Session::set_flashdata('success', 'Akun berhasil dibuat! Silakan login. 🎉');
        redirect('auth');
    }

    // ── Halaman Profil ───────────────────────────────────────
    public function profil() {
        auth_required();
        $user_id = Session::get('user_id');
        $profil  = $this->user_model->get_profil($user_id);

        if (!$profil) {
            redirect('auth/logout');
            return;
        }

        $data['title']   = 'Profil — ' . $profil->username;
        $data['profil']  = $profil;
        $data['flash_success'] = Session::flashdata('success');
        $this->load->view('v_profil', $data);
    }

    // ── Logout ───────────────────────────────────────────────
    public function logout() {
        Session::destroy();
        Session::init();
        Session::set_flashdata('success', 'Kamu telah keluar dari Game Seru. Sampai jumpa! 👋');
        redirect('auth');
    }

    // ── Private: Set session setelah login ──────────────────
    private function _set_session($user) {
        Session::set('user_id',  $user->id);
        Session::set('username', $user->username);
        Session::set('avatar',   $user->avatar ?? 'default.png');
    }

    // ── Middleware ───────────────────────────────────────────
    public function _middleware($method) {
        // Tidak ada middleware khusus pada modul Auth
    }
}
