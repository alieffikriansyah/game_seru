<?php

// ============================================================
// Game Helper — Helper global untuk platform Game Seru
// ============================================================

/**
 * Kirim JSON response dan hentikan eksekusi
 */
if (!function_exists('json_response')) {
    function json_response($data = [], $code = 200) {
        $captured_err = '';
        while (ob_get_level() > 0) {
            $chunk = ob_get_clean();
            if (!empty($chunk)) {
                $captured_err .= $chunk;
            }
        }
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        if (!empty($captured_err) && is_array($data) && !isset($data['php_notice'])) {
            $data['php_notice'] = strip_tags(trim($captured_err));
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Cek apakah request adalah AJAX / Fetch API
 */
if (!function_exists('is_ajax')) {
    function is_ajax() {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            isset($_SERVER['HTTP_ACCEPT']) &&
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        );
    }
}

/**
 * Pastikan user sudah login, jika tidak redirect ke halaman auth
 */
if (!function_exists('auth_required')) {
    function auth_required() {
        if (!Session::get('user_id')) {
            if (is_ajax()) {
                json_response(['error' => 'Kamu harus login terlebih dahulu.', 'redirect' => site_url('auth')], 401);
            }
            Session::set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }
    }
}

/**
 * Pastikan request adalah POST
 */
if (!function_exists('require_post')) {
    function require_post($redirect_to = '') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (is_ajax()) {
                json_response(['error' => 'Method tidak diizinkan.'], 405);
            }
            redirect($redirect_to ?: 'lobby');
        }
    }
}

/**
 * Generate kode room unik 6 karakter (huruf kapital + angka)
 */
if (!function_exists('generate_room_code')) {
    function generate_room_code($length = 6) {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // hindari ambiguous chars
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}

/**
 * Ambil data dari $_POST dengan sanitasi XSS
 */
if (!function_exists('post')) {
    function post($key, $default = '') {
        $CI =& Controller::get_instance();
        $val = $_POST[$key] ?? $default;
        return $CI->input->xss_clean(trim($val));
    }
}

/**
 * Ambil data dari $_GET dengan sanitasi
 */
if (!function_exists('get_param')) {
    function get_param($key, $default = '') {
        $val = $_GET[$key] ?? $default;
        return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Format waktu tersisa (detik) menjadi string Indonesia
 */
if (!function_exists('format_waktu_tersisa')) {
    function format_waktu_tersisa($detik) {
        if ($detik <= 0) return '0 detik';
        if ($detik < 60) return $detik . ' detik';
        $menit = floor($detik / 60);
        $sisa = $detik % 60;
        return $menit . ' menit ' . ($sisa > 0 ? $sisa . ' detik' : '');
    }
}

/**
 * Dapatkan warna kartu UNO dalam emoji/simbol
 */
if (!function_exists('uno_color_symbol')) {
    function uno_color_symbol($color) {
        $map = [
            'merah'  => '🔴',
            'kuning' => '🟡',
            'hijau'  => '🟢',
            'biru'   => '🔵',
            'wild'   => '🌈',
        ];
        return $map[$color] ?? '⚪';
    }
}

/**
 * Dapatkan label fase Werewolf dalam Bahasa Indonesia
 */
if (!function_exists('ww_phase_label')) {
    function ww_phase_label($phase) {
        $map = [
            'malam'            => '🌙 Malam',
            'pagi_pengumuman'  => '🌅 Pagi — Pengumuman',
            'siang_diskusi'    => '☀️ Siang — Diskusi',
            'siang_voting'     => '⚖️ Siang — Voting',
            'selesai'          => '🏁 Permainan Selesai',
        ];
        return $map[$phase] ?? $phase;
    }
}

/**
 * Dapatkan durasi fase Werewolf dalam detik
 */
if (!function_exists('ww_phase_duration')) {
    function ww_phase_duration($phase) {
        $map = [
            'malam'           => 40,
            'pagi_pengumuman' => 10,
            'siang_diskusi'   => 90,
            'siang_voting'    => 30,
        ];
        return $map[$phase] ?? 30;
    }
}

/**
 * Dapatkan fase berikutnya dalam siklus Werewolf
 */
if (!function_exists('ww_next_phase')) {
    function ww_next_phase($current_phase) {
        $cycle = ['malam', 'pagi_pengumuman', 'siang_diskusi', 'siang_voting'];
        $idx = array_search($current_phase, $cycle);
        if ($idx === false) return 'malam';
        return $cycle[($idx + 1) % count($cycle)];
    }
}

/**
 * Distribusi peran Werewolf berdasarkan jumlah pemain
 */
if (!function_exists('ww_role_distribution')) {
    function ww_role_distribution($jumlah_pemain) {
        $dist = [
            4  => ['werewolf' => 1, 'penerawang' => 0, 'pelindung' => 0],
            5  => ['werewolf' => 1, 'penerawang' => 1, 'pelindung' => 0],
            6  => ['werewolf' => 1, 'penerawang' => 1, 'pelindung' => 1],
            7  => ['werewolf' => 2, 'penerawang' => 1, 'pelindung' => 1],
            8  => ['werewolf' => 2, 'penerawang' => 1, 'pelindung' => 1],
            9  => ['werewolf' => 3, 'penerawang' => 1, 'pelindung' => 1],
            10 => ['werewolf' => 3, 'penerawang' => 1, 'pelindung' => 1],
        ];
        $n = min(10, max(4, $jumlah_pemain));
        $roles = $dist[$n];
        $roles['warga'] = $n - $roles['werewolf'] - $roles['penerawang'] - $roles['pelindung'];
        return $roles;
    }
}

/**
 * Sanitasi pesan chat (izinkan emoji, larang HTML)
 */
if (!function_exists('sanitize_chat')) {
    function sanitize_chat($msg) {
        $msg = trim($msg);
        $msg = strip_tags($msg);
        $msg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
        return mb_substr($msg, 0, 500, 'UTF-8'); // max 500 karakter
    }
}
