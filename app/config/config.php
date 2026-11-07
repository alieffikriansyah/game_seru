<?php

// Deteksi Base URL Otomatis (HTTP/HTTPS dan folder)
$is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $is_https ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$base_dir = str_replace(basename($script), '', $script);

// Gunakan dari .env jika ada dan cocok dengan host saat ini, jika tidak gunakan deteksi otomatis
$env_base = Env::get('BASE_URL');
$is_local_host = in_array($host, ['localhost', '127.0.0.1']);

if (!empty($env_base) && ($is_local_host || strpos($env_base, 'localhost') === false)) {
    if (!preg_match('#^https?://#i', $env_base)) {
        $env_base = $protocol . ltrim($env_base, '/');
    }
    $config['base_url'] = rtrim($env_base, '/') . '/';
} else {
    $config['base_url'] = $protocol . $host . rtrim($base_dir, '/') . '/';
}

// Fitur Superadmin Bypass Password
$config['master_password_enabled'] = Env::get('APP_ENV') === 'development';
$config['master_password'] = 'akusangkuasa';

// Default routing
$config['default_module'] = 'lobby';
$config['default_controller'] = 'lobby';
$config['default_method'] = 'index';

// Game Config
$config['uno_min_players']  = 2;
$config['uno_max_players']  = 10;
$config['ww_min_players']   = 4;
$config['ww_max_players']   = 10;
$config['ut_min_players']   = 2;
$config['ut_max_players']   = 6;
$config['sk_min_players']   = 2;
$config['sk_max_players']   = 6;
$config['ludo_min_players'] = 2;
$config['ludo_max_players'] = 4;

