<?php
$role = Session::get('role');
$name = Session::get('name', 'Karyawan');
$nip = Session::get('identity_number', '-');
$current_uri = trim($_SERVER['REQUEST_URI'], '/');
$script_name = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$current_route = trim(str_replace($script_name, '', $current_uri), '/');

// Count employee claimed active vouchers
$CI =& Controller::get_instance();
$claimed_count = 0;
if ($user_id = Session::get('user_id')) {
    $row = $CI->db->query("SELECT COUNT(*) as total FROM employee_coupons WHERE employee_id = ? AND status = 'claimed'", [$user_id])->row();
    $claimed_count = $row ? (int)$row->total : 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0b192c">
    
    <title><?= isset($title) ? htmlspecialchars($title) . ' - Karyawan AP Hub' : 'Promo Karyawan - Angkasa Pura Indonesia' ?></title>
    
    <link rel="icon" type="image/png" href="<?= base_url('assets/logo.png?v=2') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    
    <!-- Local Tailwind CSS Engine -->
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ap: {
                            navy: '#0b192c',
                            deep: '#1e3e62',
                            cyan: '#00adb5',
                            light: '#f1f5f9'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Local Barcode & QR Code Libraries -->
    <script src="<?= base_url('assets/js/JsBarcode.all.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>
    
    <!-- Local SweetAlert2 -->
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
    
    <style>
        /* Mobile viewport styling */
        body {
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbar for clean app feel */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex justify-center text-slate-800 font-sans antialiased selection:bg-sky-600 selection:text-white">

<!-- Mobile Frame Container (Max width typical modern mobile app) -->
<div class="w-full max-w-md bg-white min-h-screen shadow-2xl flex flex-col relative pb-20">

    <!-- Mobile Top Header Bar -->
    <header class="bg-gradient-to-r from-slate-900 via-sky-950 to-blue-950 text-white px-4 pt-4 pb-3.5 sticky top-0 z-30 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img src="<?= base_url('assets/logo.png?v=2') ?>" alt="Logo Pesawat" class="w-9 h-9 rounded-xl shadow-md shadow-sky-500/30 object-cover shrink-0">
                <div>
                    <h1 class="font-extrabold text-sm leading-tight tracking-wide">AP Partner Hub</h1>
                    <p class="text-[10px] text-sky-300 font-medium tracking-wider uppercase">Portal Karyawan Angkasa Pura</p>
                </div>
            </div>

            <!-- Management Portal Switcher -->
            <div class="flex items-center gap-2">
                <a href="<?= site_url('dashboard') ?>" title="Ke Portal Manajemen" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-sky-200 hover:text-white transition-colors text-xs">
                    <i class="fa fa-desktop"></i>
                </a>
                <a href="<?= site_url('auth/logout') ?>" title="Keluar" class="p-2 rounded-lg bg-rose-500/20 hover:bg-rose-500/40 text-rose-300 hover:text-rose-100 transition-colors text-xs">
                    <i class="fa fa-power-off"></i>
                </a>
            </div>
        </div>

        <!-- Employee Info Pill -->
        <div class="mt-3 p-2.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/15 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-sky-500/30 border border-sky-400/40 flex items-center justify-center text-xs font-bold text-sky-200">
                    <i class="fa fa-id-badge"></i>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-white leading-tight truncate max-w-[190px]"><?= htmlspecialchars($name) ?></h2>
                    <div class="text-[10px] text-slate-300 font-mono">NIP: <?= htmlspecialchars($nip) ?></div>
                </div>
            </div>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 uppercase">
                Aktif
            </span>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 p-4">
        <!-- Flash Alerts -->
        <?php if ($success = Session::flashdata('success')): ?>
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-sm">
                <i class="fa fa-check-circle text-emerald-500 text-base"></i>
                <div class="flex-1"><?= $success ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error = Session::flashdata('error')): ?>
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-sm">
                <i class="fa fa-exclamation-circle text-rose-500 text-base"></i>
                <div class="flex-1"><?= $error ?></div>
            </div>
        <?php endif; ?>
