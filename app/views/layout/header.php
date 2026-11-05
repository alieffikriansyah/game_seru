<?php
$name        = Session::get('name', 'Pengguna');
$current_uri = trim($_SERVER['REQUEST_URI'], '/');
$script_name = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$current_route = trim(str_replace($script_name, '', $current_uri), '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — Coretan' : 'Coretan' ?></title>
    <meta name="description" content="Papan tulis digital dan catatan pintar berbasis PWA">
    <meta name="theme-color" content="#7c3aed">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Game Seru">

    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                    colors: {
                        ink:    { DEFAULT: '#1a1a2e', light: '#252545' },
                        violet: { 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9' },
                        coral:  { DEFAULT: '#f97316', light: '#fb923c' },
                        cream:  { DEFAULT: '#fef9f0' }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn:  { from: { opacity: '0' }, to: { opacity: '1' } },
                        slideUp: { from: { transform: 'translateY(10px)', opacity: '0' }, to: { transform: 'translateY(0)', opacity: '1' } },
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.3); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(124,58,237,0.6); }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }

        /* Sidebar transition */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }

        /* Active glow */
        .nav-active { box-shadow: 0 4px 15px rgba(124,58,237,0.4); }

        /* Glassmorphism card */
        .glass { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-[#f5f3ff] text-slate-800 min-h-screen flex font-sans antialiased">

<!-- =========================================================
     SIDEBAR
     ========================================================= -->
<aside id="sidebar" class="w-64 shrink-0 bg-[#1a1a2e] flex flex-col min-h-screen fixed top-0 left-0 z-30 shadow-2xl shadow-black/40 -translate-x-full lg:translate-x-0" style="height:100vh;overflow-y:auto;">

    <!-- Brand -->
    <div class="px-5 py-5 border-b border-white/10 flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl overflow-hidden shrink-0 shadow-lg shadow-violet-500/30">
            <img src="<?= base_url('assets/logo.png') ?>" alt="Coretan Logo" class="w-full h-full object-cover">
        </div>
        <div>
            <div class="font-extrabold text-base text-white leading-tight tracking-tight">Coretan</div>
            <div class="text-[11px] text-violet-400 font-semibold">Papan Tulis Digital</div>
        </div>
    </div>

    <!-- User chip -->
    <div class="px-4 py-3 border-b border-white/10">
        <div class="flex items-center gap-2.5 bg-white/5 px-3 py-2.5 rounded-xl">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-coral flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-inner">
                <?= strtoupper(mb_substr($name, 0, 1)) ?>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold text-white truncate"><?= htmlspecialchars($name) ?></div>
                <div class="text-[10px] text-white/40 font-medium">Pengguna Aktif</div>
            </div>
        </div>
    </div>

    <!-- Nav menu -->
    <?php
        $CI =& Controller::get_instance();
        $CI->load->helper('menu');
        echo render_sidebar_menu('user', $current_route);
    ?>

    <!-- Logout -->
    <div class="p-3 border-t border-white/10 mt-auto">
        <a href="<?= site_url('auth/logout') ?>" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-white/50 hover:text-rose-400 hover:bg-rose-500/10 font-medium transition-all duration-150">
            <i class="fa fa-right-from-bracket w-4 text-center text-[13px]"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Sidebar overlay (mobile/tablet) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->
<div class="flex-1 flex flex-col min-h-screen lg:ml-64">

    <!-- Top bar -->
    <header class="bg-white/80 backdrop-blur-md border-b border-violet-100/60 sticky top-0 z-10 px-4 sm:px-6 py-3 flex items-center justify-between gap-3 shadow-sm shadow-violet-100/50">
        <!-- Hamburger + page title -->
        <div class="flex items-center gap-3">
            <button id="hamburger-btn" onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 hover:bg-violet-100 transition-colors">
                <i class="fa fa-bars text-sm"></i>
            </button>
            <div>
                <h1 class="text-sm font-bold text-slate-800 truncate">
                    <?= isset($title) ? htmlspecialchars($title) : 'Dashboard' ?>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Date -->
            <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                <i class="fa fa-calendar-days text-violet-400"></i>
                <span><?= date('d M Y') ?></span>
            </div>
            <!-- User avatar (mobile) -->
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-coral flex items-center justify-center text-white font-bold text-xs shadow-sm">
                <?= strtoupper(mb_substr($name, 0, 1)) ?>
            </div>
        </div>
    </header>

    <!-- Page body -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto animate-fade-in">

        <?php if ($success = Session::flashdata('success')): ?>
        <div class="mb-5 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-sm shadow-sm animate-slide-up">
            <i class="fa fa-circle-check text-emerald-500"></i> <?= $success ?>
        </div>
        <?php endif; ?>

        <?php if ($warning = Session::flashdata('warning')): ?>
        <div class="mb-5 px-4 py-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-3 text-sm shadow-sm animate-slide-up">
            <i class="fa fa-triangle-exclamation text-amber-500"></i> <?= $warning ?>
        </div>
        <?php endif; ?>

        <?php if ($error = Session::flashdata('error')): ?>
        <div class="mb-5 px-4 py-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 text-sm shadow-sm animate-slide-up">
            <i class="fa fa-circle-xmark text-rose-500"></i> <?= $error ?>
        </div>
        <?php endif; ?>

        <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('sidebar-overlay');
            s.classList.toggle('-translate-x-full');
            o.classList.toggle('hidden');
        }

        // SweetAlert toast helper
        window.showToast = function(title, icon = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ toast:true, position:'top-end', icon, title, showConfirmButton:false, timer:3000, timerProgressBar:true, customClass:{ popup:'rounded-2xl' } });
            }
        };

        // Register PWA Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(reg => console.log('[PWA] SW registered:', reg.scope))
                    .catch(err => console.warn('[PWA] SW failed:', err));
            });
        }
        </script>
