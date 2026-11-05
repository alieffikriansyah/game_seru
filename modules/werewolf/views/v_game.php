<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="theme-color" content="#0d0d1a">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/game.css') ?>">
    <style>
        html, body {
            height: 100dvh;
            overflow: hidden;
            touch-action: pan-y;
        }
        .ww-arena {
            display: flex; flex-direction: column;
            height: 100dvh;
            max-width: 480px; margin: 0 auto;
            transition: background 1.2s ease;
            position: relative;
            overflow: hidden;
        }

        /* ── Werewolf Atmosphere Layer ── */
        .ww-atmosphere {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        /* Malam Environment */
        .ww-arena.malam { background: linear-gradient(175deg, #020208 0%, #0c061d 45%, #05030e 100%); }
        .ww-arena.malam .night-env { opacity: 1; }
        .ww-arena.malam .day-env   { opacity: 0; }
        .ww-arena.malam .pagi-env  { opacity: 0; }

        /* Pagi Environment */
        .ww-arena.pagi { background: linear-gradient(175deg, #1e1136 0%, #7c2d12 40%, #ea580c 75%, #1e1b4b 100%); }
        .ww-arena.pagi .night-env { opacity: 0; }
        .ww-arena.pagi .day-env   { opacity: 0; }
        .ww-arena.pagi .pagi-env  { opacity: 1; }

        /* Siang Environment */
        .ww-arena.siang { background: linear-gradient(175deg, #0369a1 0%, #0284c7 35%, #0c1a30 100%); }
        .ww-arena.siang .night-env { opacity: 0; }
        .ww-arena.siang .day-env   { opacity: 1; }
        .ww-arena.siang .pagi-env  { opacity: 0; }

        .env-layer {
            position: absolute;
            inset: 0;
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        /* 🌙 Night Animation */
        .night-env .moon-wrap {
            position: absolute;
            top: 55px;
            right: 25px;
            width: 64px;
            height: 64px;
        }
        .night-env .moon-disc {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #ffffff 0%, #fef08a 40%, #e2e8f0 75%, #94a3b8 100%);
            box-shadow: 0 0 25px rgba(254, 240, 138, 0.6), 0 0 60px rgba(254, 240, 138, 0.3);
            animation: moonPulse 4s infinite alternate ease-in-out;
        }
        @keyframes moonPulse {
            0% { transform: scale(1); box-shadow: 0 0 25px rgba(254, 240, 138, 0.5), 0 0 60px rgba(254, 240, 138, 0.25); }
            100% { transform: scale(1.04); box-shadow: 0 0 35px rgba(254, 240, 138, 0.8), 0 0 80px rgba(254, 240, 138, 0.45); }
        }

        /* Star particles */
        .star-sky {
            position: absolute;
            top: 0; left: 0; right: 0; height: 180px;
        }
        .star-dot {
            position: absolute;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 6px #ffffff;
            animation: twinkleStar infinite alternate ease-in-out;
        }
        @keyframes twinkleStar {
            0% { opacity: 0.2; transform: scale(0.7); }
            100% { opacity: 1; transform: scale(1.3); }
        }

        /* Mist / Fog animation */
        .mist-layer {
            position: absolute;
            bottom: 60px;
            left: -50%;
            width: 200%;
            height: 120px;
            background: radial-gradient(ellipse at center, rgba(139, 92, 246, 0.12) 0%, rgba(15, 23, 42, 0) 70%);
            filter: blur(12px);
            animation: fogDrift 18s infinite linear alternate;
        }
        .mist-layer.mist-2 {
            bottom: 120px;
            animation: fogDrift2 24s infinite linear alternate-reverse;
            background: radial-gradient(ellipse at center, rgba(239, 68, 68, 0.08) 0%, rgba(15, 23, 42, 0) 65%);
        }
        @keyframes fogDrift {
            0% { transform: translateX(0); }
            100% { transform: translateX(25%); }
        }
        @keyframes fogDrift2 {
            0% { transform: translateX(0); }
            100% { transform: translateX(-20%); }
        }

        /* ☀️ Day & Pagi Animation */
        .day-env .sun-wrap, .pagi-env .sun-wrap {
            position: absolute;
            top: 50px;
            right: 28px;
            width: 60px;
            height: 60px;
        }
        .sun-core {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: radial-gradient(circle at center, #ffffff 0%, #fef08a 35%, #f59e0b 70%, #d97706 100%);
            box-shadow: 0 0 35px rgba(245, 158, 11, 0.8), 0 0 70px rgba(251, 191, 36, 0.5);
            animation: sunPulse 3s infinite alternate ease-in-out;
            position: relative;
            z-index: 2;
        }
        @keyframes sunPulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.06); }
        }
        .sun-rays {
            position: absolute;
            top: -20px; left: -20px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(251, 191, 36, 0.4) 0deg, transparent 30deg, rgba(251, 191, 36, 0.4) 60deg, transparent 90deg, rgba(251, 191, 36, 0.4) 120deg, transparent 150deg, rgba(251, 191, 36, 0.4) 180deg, transparent 210deg, rgba(251, 191, 36, 0.4) 240deg, transparent 270deg, rgba(251, 191, 36, 0.4) 300deg, transparent 330deg, rgba(251, 191, 36, 0.4) 360deg);
            animation: rotateRays 25s infinite linear;
            z-index: 1;
        }
        @keyframes rotateRays {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Floating Clouds */
        .cloud-wrap {
            position: absolute;
            top: 20px; left: 0; right: 0; height: 160px;
            overflow: hidden;
        }
        .cloud-item {
            position: absolute;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50px;
            filter: blur(3px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.15);
            animation: cloudMove linear infinite;
        }
        .cloud-item::before, .cloud-item::after {
            content: '';
            position: absolute;
            background: inherit;
            border-radius: 50%;
        }
        .cloud-item.c1 {
            width: 110px; height: 32px; top: 35px; left: -120px;
            animation-duration: 26s;
        }
        .cloud-item.c1::before {
            width: 44px; height: 44px; top: -18px; left: 20px;
        }
        .cloud-item.c1::after {
            width: 34px; height: 34px; top: -14px; left: 52px;
        }
        .cloud-item.c2 {
            width: 140px; height: 38px; top: 85px; left: -160px;
            animation-duration: 38s; animation-delay: 8s;
        }
        .cloud-item.c2::before {
            width: 52px; height: 52px; top: -22px; left: 28px;
        }
        .cloud-item.c2::after {
            width: 40px; height: 40px; top: -16px; left: 70px;
        }
        .cloud-item.c3 {
            width: 90px; height: 28px; top: 125px; left: -100px;
            animation-duration: 32s; animation-delay: 15s;
            opacity: 0.7;
        }
        .cloud-item.c3::before {
            width: 36px; height: 36px; top: -14px; left: 18px;
        }
        @keyframes cloudMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(650px); }
        }

        /* Morning Birds */
        .morning-birds {
            position: absolute;
            top: 70px;
            left: -40px;
            font-size: 0.9rem;
            animation: birdsFly 16s infinite linear;
        }
        @keyframes birdsFly {
            0% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(250px) translateY(-18px); }
            100% { transform: translateX(550px) translateY(-5px); }
        }

        /* ── Header Fase ── */
        .phase-header {
            flex-shrink: 0;
            padding: 0.75rem 1rem;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            position: relative;
            z-index: 10;
        }
        .phase-row { display: flex; align-items: center; justify-content: space-between; }
        .phase-label { font-family: 'Cinzel', serif; font-size: 1rem; font-weight: 700; color: var(--text-primary); }
        .day-badge { font-size: 0.75rem; color: var(--text-muted); }

        /* Timer */
        .timer-area { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.4rem; }
        .timer-bar { flex: 1; height: 4px; border-radius: 2px; background: var(--border-color); overflow: hidden; }
        .timer-fill { height: 100%; border-radius: 2px; transition: width 1s linear, background 1s; background: #22c55e; }

        /* My Role Badge */
        .my-role-badge {
            margin-top: 0.5rem;
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.3rem 0.875rem;
            border-radius: var(--radius-full);
            font-size: 0.8rem; font-weight: 700;
        }
        .my-role-badge.warga    { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #86efac; }
        .my-role-badge.werewolf { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        .my-role-badge.penerawang { background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #93c5fd; }
        .my-role-badge.pelindung  { background: rgba(234,179,8,0.15); border: 1px solid rgba(234,179,8,0.3); color: #fde047; }

        /* ── Player Grid ── */
        .players-grid {
            flex-shrink: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            gap: 0.5rem;
            padding: 0.875rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(0,0,0,0.3);
        }
        .player-tile {
            display: flex; flex-direction: column; align-items: center; gap: 0.25rem;
            padding: 0.5rem 0.25rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: rgba(255,255,255,0.03);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .player-tile.alive { opacity: 1; }
        .player-tile.dead  { opacity: 0.35; filter: grayscale(1); cursor: default; }
        .player-tile.selected-target { border-color: var(--accent-primary); background: rgba(139,92,246,0.15); box-shadow: 0 0 12px var(--accent-glow); }
        .player-tile.voted { border-color: #ef4444; background: rgba(239,68,68,0.1); }
        .player-tile.is-me { border-color: rgba(255,255,255,0.25); }
        .pt-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--card-bg-2); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 2px solid transparent; }
        .player-tile.alive .pt-avatar { border-color: rgba(34,197,94,0.4); }
        .player-tile.dead  .pt-avatar { border-color: rgba(100,100,100,0.3); }
        .pt-name { font-size: 0.62rem; font-weight: 600; color: var(--text-muted); text-align: center; max-width: 66px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pt-status { font-size: 0.65rem; }
        .dead-icon { position: absolute; top: -5px; right: -5px; font-size: 0.875rem; }

        /* ── Chat Area ── */
        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        /* Channel Tabs */
        .channel-tabs {
            flex-shrink: 0;
            display: flex;
            gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            background: rgba(0,0,0,0.4);
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .channel-tabs::-webkit-scrollbar { display: none; }
        .ch-tab {
            flex-shrink: 0;
            padding: 0.3rem 0.875rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem; font-weight: 600;
            cursor: pointer; border: 1px solid var(--border-color);
            background: var(--card-bg); color: var(--text-muted);
            transition: all 0.15s;
        }
        .ch-tab.active { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; }
        .ch-tab.werewolf-tab { border-color: rgba(239,68,68,0.3); color: #fca5a5; }
        .ch-tab.werewolf-tab.active { background: rgba(239,68,68,0.8); border-color: #ef4444; }
        .ch-tab.arwah-tab { border-color: rgba(107,114,128,0.3); color: #9ca3af; }
        .ch-tab.arwah-tab.active { background: rgba(107,114,128,0.6); }

        /* Messages */
        .messages-wrap {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0.875rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .msg { display: flex; flex-direction: column; max-width: 88%; }
        .msg.me { align-self: flex-end; }
        .msg.them { align-self: flex-start; }
        .msg.narrator { align-self: center; max-width: 100%; }
        .msg-sender { font-size: 0.68rem; color: var(--text-muted); margin-bottom: 2px; padding: 0 0.25rem; }
        .msg.me .msg-sender { text-align: right; }
        .msg-bubble {
            padding: 0.5rem 0.875rem;
            border-radius: 12px;
            font-size: 0.875rem;
            line-height: 1.5;
            word-break: break-word;
        }
        .msg.me .msg-bubble { background: var(--gradient-purple); color: #fff; border-bottom-right-radius: 4px; }
        .msg.them .msg-bubble { background: rgba(255,255,255,0.07); border: 1px solid var(--border-color); color: var(--text-primary); border-bottom-left-radius: 4px; }
        .msg.narrator .msg-bubble { background: rgba(255,200,0,0.08); border: 1px solid rgba(255,200,0,0.2); color: #fde047; text-align: center; border-radius: 10px; font-size: 0.8rem; font-style: italic; }
        .msg.werewolf-msg .msg-bubble { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5; }
        .msg.arwah-msg .msg-bubble { background: rgba(107,114,128,0.08); border: 1px solid rgba(107,114,128,0.2); color: #9ca3af; font-style: italic; }

        /* ── Input Area ── */
        .input-area {
            flex-shrink: 0;
            padding: 0.625rem 0.875rem;
            background: rgba(0,0,0,0.5);
            border-top: 1px solid var(--border-color);
            display: flex; gap: 0.5rem; align-items: flex-end;
            padding-bottom: max(22px, calc(env(safe-area-inset-bottom, 0px) + 14px));
        }
        .chat-input {
            flex: 1;
            min-height: 40px; max-height: 100px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 0.5rem 1rem;
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            outline: none;
            resize: none;
            -webkit-appearance: none;
        }
        .chat-input:focus { border-color: var(--accent-primary); }
        .send-btn {
            width: 40px; height: 40px;
            background: var(--gradient-purple);
            border: none; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1rem; flex-shrink: 0;
            transition: transform 0.15s;
        }
        .send-btn:active { transform: scale(0.93); }

        /* Aksi Footer */
        .action-footer {
            flex-shrink: 0;
            padding: 0.625rem 0.875rem max(22px, calc(env(safe-area-inset-bottom, 0px) + 14px));
            border-top: 1px solid rgba(255,255,255,0.05);
            background: rgba(0,0,0,0.4);
            display: none; /* hanya tampil saat ada aksi yang tersedia */
        }
        .action-footer.visible { display: flex; gap: 0.625rem; flex-wrap: wrap; }
        .action-btn {
            flex: 1; min-width: 120px; min-height: 40px;
            border-radius: var(--radius-md);
            font-size: 0.8rem; font-weight: 700;
            cursor: pointer; border: none;
            transition: all 0.15s;
        }
        .action-btn:active { transform: scale(0.97); }
        .action-btn.mangsa  { background: rgba(239,68,68,0.2); border: 1px solid rgba(239,68,68,0.4); color: #fca5a5; }
        .action-btn.terawang { background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.4); color: #93c5fd; }
        .action-btn.lindungi { background: rgba(234,179,8,0.2); border: 1px solid rgba(234,179,8,0.4); color: #fde047; }
        .action-btn.vote    { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.35); color: #fca5a5; }
        .action-btn.done    { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.35); color: #86efac; cursor: default; }

        /* Modal Pilih Target */
        .target-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;
            margin: 1.25rem 0;
        }
        .target-item {
            display: flex; flex-direction: column; align-items: center; gap: 0.375rem;
            padding: 0.875rem 0.25rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            cursor: pointer; transition: all 0.15s;
        }
        .target-item:active { transform: scale(0.96); }
        .target-item:hover { border-color: var(--accent-primary); background: rgba(139,92,246,0.1); }
        .target-avatar { font-size: 1.75rem; }
        .target-name { font-size: 0.72rem; font-weight: 600; text-align: center; color: var(--text-muted); }

        /* End Game Modal */
        #modal-endgame { display: none; }
        .endgame-box { text-align: center; }
        .endgame-icon { font-size: 4rem; margin-bottom: 0.875rem; }
        .endgame-title { font-family: 'Cinzel', serif; font-size: 1.75rem; font-weight: 900; margin-bottom: 0.5rem; }
        .endgame-sub { color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.25rem; }

        /* ── Modal Panduan Aturan Main Werewolf ── */
        .modal-rule-box {
            background: #141126;
            border: 1px solid rgba(168, 85, 247, 0.35);
            border-radius: 16px;
            width: min(92vw, 460px);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8), 0 0 30px rgba(168, 85, 247, 0.2);
            color: #ffffff;
        }
        .modal-rule-header {
            padding: 0.875rem 1rem;
            background: rgba(255, 255, 255, 0.04);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-rule-header h3 { font-size: 1.05rem; font-weight: 800; color: #f1f5f9; display: flex; align-items: center; gap: 6px; font-family: 'Cinzel', serif; }
        .rule-tab-nav {
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0 0.5rem;
            gap: 0.25rem;
        }
        .rule-tab-nav::-webkit-scrollbar { display: none; }
        .rule-tab-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.6rem 0.75rem;
            cursor: pointer;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
            transition: all 0.15s;
        }
        .rule-tab-btn.active {
            color: #c084fc;
            border-bottom-color: #a855f7;
        }
        .rule-tab-body {
            padding: 1rem;
            overflow-y: auto;
            flex: 1;
            font-size: 0.8rem;
            line-height: 1.55;
            color: #cbd5e1;
        }
        .rule-tab-body h4 {
            color: #f8fafc;
            font-size: 0.92rem;
            margin-bottom: 0.5rem;
        }
        .rule-tab-body ul, .rule-tab-body ol {
            padding-left: 1.25rem;
            margin-bottom: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .rule-tab-body b { color: #f1f5f9; }
        .modal-rule-footer {
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .chk-rule-label {
            font-size: 0.72rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
<?php
    $s = $state;
    $phase = $s['current_phase'];
    $is_night = ($phase === 'malam');
    $is_pagi  = ($phase === 'pagi_pengumuman');
    $arena_theme = $is_night ? 'malam' : ($is_pagi ? 'pagi' : 'siang');
    $role_icons = ['warga' => '🏘️', 'werewolf' => '🐺', 'penerawang' => '🔮', 'pelindung' => '🛡️'];
    $my_role = $s['my_role'];
    $is_alive = $s['my_is_alive'];
    $last_chat_id = !empty($chats) ? max(array_column($chats, 'id')) : 0;
?>

<div class="ww-arena <?= $arena_theme ?>" id="ww-arena">

    <!-- ── Atmospheric Visual Animation Layer (Pagi & Malam) ── -->
    <div class="ww-atmosphere" id="ww-atmosphere">
        <!-- Malam: Bulan Purnama, Bintang Berkerlip, Kabut Misterius -->
        <div class="env-layer night-env">
            <div class="star-sky">
                <div class="star-dot" style="top:12px;left:18%;width:2px;height:2px;animation-duration:2.1s;"></div>
                <div class="star-dot" style="top:28px;left:35%;width:3px;height:3px;animation-duration:3.2s;animation-delay:0.5s;"></div>
                <div class="star-dot" style="top:15px;left:52%;width:2px;height:2px;animation-duration:1.8s;animation-delay:1.2s;"></div>
                <div class="star-dot" style="top:42px;left:68%;width:3px;height:3px;animation-duration:2.8s;animation-delay:0.7s;"></div>
                <div class="star-dot" style="top:22px;left:82%;width:2px;height:2px;animation-duration:2.4s;animation-delay:1.5s;"></div>
                <div class="star-dot" style="top:60px;left:25%;width:2px;height:2px;animation-duration:3.5s;animation-delay:0.3s;"></div>
                <div class="star-dot" style="top:75px;left:58%;width:3px;height:3px;animation-duration:2.2s;animation-delay:1.8s;"></div>
            </div>
            <div class="moon-wrap">
                <div class="moon-disc"></div>
            </div>
            <div class="mist-layer"></div>
            <div class="mist-layer mist-2"></div>
        </div>

        <!-- Pagi: Fajar Merah Emas, Burung Terbang, Embun Pagi -->
        <div class="env-layer pagi-env">
            <div class="sun-wrap" style="top:68px;">
                <div class="sun-core" style="background:radial-gradient(circle at center, #fff 0%, #fed7aa 35%, #f97316 70%, #c2410c 100%);box-shadow:0 0 35px rgba(249,115,22,0.8),0 0 70px rgba(234,88,12,0.5);"></div>
                <div class="sun-rays" style="background:conic-gradient(from 0deg, rgba(249,115,22,0.4) 0deg, transparent 30deg, rgba(249,115,22,0.4) 60deg, transparent 90deg, rgba(249,115,22,0.4) 120deg, transparent 150deg, rgba(249,115,22,0.4) 180deg, transparent 210deg, rgba(249,115,22,0.4) 240deg, transparent 270deg, rgba(249,115,22,0.4) 300deg, transparent 330deg, rgba(249,115,22,0.4) 360deg);"></div>
            </div>
            <div class="cloud-wrap">
                <div class="cloud-item c1" style="background:rgba(254,215,170,0.3);"></div>
                <div class="cloud-item c2" style="background:rgba(253,186,116,0.25);"></div>
            </div>
            <div class="morning-birds">🕊️ 🕊️</div>
        </div>

        <!-- Siang: Matahari Terik, Awan Berarak, Langit Biru Cerah -->
        <div class="env-layer day-env">
            <div class="sun-wrap">
                <div class="sun-core"></div>
                <div class="sun-rays"></div>
            </div>
            <div class="cloud-wrap">
                <div class="cloud-item c1"></div>
                <div class="cloud-item c2"></div>
                <div class="cloud-item c3"></div>
            </div>
        </div>
    </div>

    <!-- ── Top Bar Header Werewolf ── -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.4rem 0.875rem;background:rgba(10,10,20,0.95);border-bottom:1px solid rgba(255,255,255,0.08);flex-shrink:0;position:relative;z-index:20;">
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <a href="javascript:void(0)" onclick="openSurrenderModal()" style="color:#ffffff;text-decoration:none;font-size:0.9rem;padding:3px 8px;border-radius:50%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);" title="Menyerah / Keluar ke Lobby">←</a>
            <span style="font-weight:900;font-size:0.95rem;color:#a855f7;letter-spacing:0.5px;">🐺 WEREWOLF</span>
            <span style="font-size:0.65rem;color:#f59e0b;background:rgba(245,158,11,0.12);padding:1px 6px;border-radius:4px;font-weight:700;"><?= htmlspecialchars($room_code) ?></span>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;">
            <button type="button" onclick="openRuleModal()" style="background:rgba(168,85,247,0.2);border:1px solid rgba(168,85,247,0.4);color:#d8b4fe;font-weight:700;font-size:0.75rem;padding:4px 10px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:4px;">
                📖 Aturan
            </button>
            <button type="button" onclick="openSurrenderModal()" style="background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;font-weight:700;font-size:0.75rem;padding:4px 10px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:4px;">
                🏳️ Menyerah
            </button>
        </div>
    </div>

    <!-- ── Phase Header ── -->
    <div class="phase-header">
        <div class="phase-row">
            <div class="phase-label" id="phase-label"><?= ww_phase_label($phase) ?></div>
            <div class="day-badge">Hari ke-<?= $s['day_count'] ?></div>
        </div>
        <div class="timer-area">
            <div class="timer-bar">
                <div class="timer-fill" id="timer-fill" style="width:100%;"></div>
            </div>
            <div class="timer-ring" id="timer-display">
                ⏱ <?= $s['seconds_left'] ?>s
            </div>
        </div>
        <div>
            <span class="my-role-badge <?= $my_role ?>">
                <?= $role_icons[$my_role] ?? '❓' ?> Peranmu: <?= ucfirst($my_role) ?>
                <?= !$is_alive ? ' (🪦 Tewas)' : '' ?>
            </span>
        </div>

        <?php if ($s['seer_result']): ?>
        <div style="margin-top:0.5rem;padding:0.5rem 0.75rem;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:var(--radius-md);font-size:0.8rem;color:#93c5fd;">
            🔮 Kamu menerawang <strong><?= htmlspecialchars($s['seer_result']['username']) ?></strong>
            → Mereka adalah <strong><?= $s['seer_result']['role'] ?></strong>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Player Grid ── -->
    <div class="players-grid" id="player-grid">
        <?php foreach ($s['players'] as $p): ?>
        <?php $is_me = ($p['user_id'] == $user_id); ?>
        <div class="player-tile <?= $p['is_alive'] ? 'alive' : 'dead' ?> <?= $is_me ? 'is-me' : '' ?>"
             id="ptile-<?= $p['user_id'] ?>"
             data-uid="<?= $p['user_id'] ?>"
             data-alive="<?= $p['is_alive'] ? '1' : '0' ?>"
             onclick="selectTarget(this, <?= $p['user_id'] ?>, '<?= htmlspecialchars($p['username']) ?>')">
            <div class="pt-avatar">🎮</div>
            <div class="pt-name"><?= htmlspecialchars($p['username']) ?><?= $is_me ? ' (Kamu)' : '' ?></div>
            <div class="pt-status"><?= $p['is_alive'] ? '💚' : '💀' ?></div>
            <?php if (!$p['is_alive']): ?>
            <div class="dead-icon">💀</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Chat Section ── -->
    <div class="chat-section">

        <!-- Channel Tabs -->
        <div class="channel-tabs">
            <button class="ch-tab active" id="tab-publik" onclick="switchChannel('publik')">🌐 Publik</button>
            <?php if ($my_role === 'werewolf'): ?>
            <button class="ch-tab werewolf-tab" id="tab-werewolf" onclick="switchChannel('werewolf')">🐺 Serigala</button>
            <?php endif; ?>
            <?php if (!$is_alive): ?>
            <button class="ch-tab arwah-tab" id="tab-arwah" onclick="switchChannel('arwah')">👻 Arwah</button>
            <?php endif; ?>
        </div>

        <!-- Messages -->
        <div class="messages-wrap" id="messages-wrap">
            <?php foreach ($chats as $chat): ?>
            <?php renderChatMsg($chat, $user_id); ?>
            <?php endforeach; ?>
        </div>

        <!-- Input -->
        <div class="input-area">
            <textarea id="chat-input" class="chat-input" placeholder="Tulis pesan..." rows="1"
                      <?= (!$is_alive && $activeChannel ?? 'publik' === 'publik') ? 'disabled placeholder="Kamu sudah tewas — beralih ke channel Arwah"' : '' ?>
                      onkeypress="handleChatKey(event)"
                      oninput="autoResize(this)"></textarea>
            <button class="send-btn" onclick="sendChat()">➤</button>
        </div>
    </div>

    <!-- ── Action Footer (Malam & Voting) ── -->
    <div class="action-footer" id="action-footer">
        <?php if ($my_role === 'werewolf' && $phase === 'malam'): ?>
        <button class="action-btn mangsa" id="btn-mangsa" onclick="showTargetModal('mangsa')">🐺 Pilih Mangsa</button>
        <?php elseif ($my_role === 'penerawang' && $phase === 'malam'): ?>
        <button class="action-btn terawang" id="btn-terawang" onclick="showTargetModal('terawang')">🔮 Terawang Pemain</button>
        <?php elseif ($my_role === 'pelindung' && $phase === 'malam'): ?>
        <button class="action-btn lindungi" id="btn-lindungi" onclick="showTargetModal('lindungi')">🛡️ Lindungi Pemain</button>
        <?php endif; ?>

        <?php if ($phase === 'siang_voting' && $is_alive): ?>
        <button class="action-btn vote" id="btn-vote" onclick="showTargetModal('vote_gantung')">⚖️ Vote Gantung</button>
        <?php endif; ?>
    </div>

</div>

<!-- Modal: Pilih Target -->
<div class="modal-overlay" id="modal-target" style="display:none;" onclick="if(event.target===this)closeModal('modal-target')">
    <div class="modal-box">
        <h3 id="modal-target-title">Pilih Target</h3>
        <p style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;" id="modal-target-sub"></p>
        <div class="target-grid" id="target-grid"></div>
        <button class="btn btn-secondary btn-full" onclick="closeModal('modal-target')">Batal</button>
    </div>
</div>

<!-- Modal: End Game -->
<div class="modal-overlay" id="modal-endgame">
    <div class="modal-box">
        <div class="endgame-box" id="endgame-box"></div>
    </div>
</div>

<!-- Toast -->
<div id="toast-container"></div>

<?php
function renderChatMsg($chat, $my_uid) {
    $is_narrator = ($chat['sender_id'] === null || $chat['sender_id'] == 0);
    $is_me       = (!$is_narrator && $chat['sender_id'] == $my_uid);
    $channel     = $chat['channel'] ?? 'publik';

    $cls = 'msg';
    if ($is_narrator) $cls .= ' narrator';
    elseif ($is_me) $cls .= ' me';
    else $cls .= ' them';

    if ($channel === 'werewolf') $cls .= ' werewolf-msg';
    if ($channel === 'arwah') $cls .= ' arwah-msg';

    echo "<div class=\"{$cls}\" data-chat-id=\"{$chat['id']}\">";
    if (!$is_narrator && !$is_me) {
        echo "<div class=\"msg-sender\">" . htmlspecialchars($chat['sender_name'] ?? 'Anonim') . "</div>";
    }
    $ch_icon = '';
    if ($channel === 'werewolf') $ch_icon = '🐺 ';
    if ($channel === 'arwah')   $ch_icon = '👻 ';
    if ($is_narrator) $ch_icon = '📜 ';
    echo "<div class=\"msg-bubble\">{$ch_icon}" . nl2br(htmlspecialchars($chat['message'] ?? '')) . "</div>";
    echo "</div>";
}
?>

<script>
const BASE_URL   = '<?= site_url() ?>';
const ROOM_CODE  = '<?= $room_code ?>';
const MY_USER_ID = <?= (int)$user_id ?>;

let clientVersion  = <?= (int)$state['state_version'] ?>;
let lastChatId     = <?= (int)$last_chat_id ?>;
let activeChannel  = 'publik';
let polling        = true;
const PHASE_DURATIONS = { malam: 40, pagi_pengumuman: 10, siang_diskusi: 90, siang_voting: 30 };
let currentPhase   = '<?= $phase ?>';
let secondsLeft    = <?= (int)$state['seconds_left'] ?>;
let maxSeconds     = PHASE_DURATIONS['<?= $phase ?>'] || 40;
let selectedTarget = null;
let pendingAction  = null;
let myRole         = '<?= $my_role ?>';
let myIsAlive      = <?= $is_alive ? 'true' : 'false' ?>;
let gameOver       = <?= $state['winner_team'] ? 'true' : 'false' ?>;

// ── Channel Switch ─────────────────────────────────────────────
function switchChannel(ch) {
    activeChannel = ch;
    document.querySelectorAll('.ch-tab').forEach(t => t.classList.remove('active'));
    const tabEl = document.getElementById('tab-' + ch);
    if (tabEl) tabEl.classList.add('active');

    // Filter messages by channel
    document.querySelectorAll('.messages-wrap .msg').forEach(msg => {
        const channel = msg.dataset.channel || 'publik';
        // Always show narrator in all channels; filter by selected channel
        const isNarrator = msg.classList.contains('narrator');
        msg.style.display = (isNarrator || channel === ch) ? 'flex' : 'none';
    });
}

// ── Chat Input ─────────────────────────────────────────────────
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}
function handleChatKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendChat();
    }
}

async function sendChat() {
    const input = document.getElementById('chat-input');
    const msg   = input.value.trim();
    if (!msg) return;
    input.value = '';
    input.style.height = 'auto';

    try {
        const res = await fetch(BASE_URL + 'werewolf/kirim_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ room_code: ROOM_CODE, channel: activeChannel, message: msg }),
        });
        const data = await res.json();
        if (!data.ok) showToast(data.message || 'Gagal kirim pesan.', 'error');
    } catch (e) {
        showToast('Koneksi bermasalah.', 'error');
    }
}

// ── Target Pilih ──────────────────────────────────────────────
function selectTarget(el, uid, uname) {
    if (!el.dataset.alive || el.dataset.alive === '0') return; // sudah mati
    if (uid === MY_USER_ID) return; // tidak bisa pilih diri sendiri

    document.querySelectorAll('.player-tile').forEach(t => t.classList.remove('selected-target'));
    el.classList.add('selected-target');
    selectedTarget = { uid, uname };
}

const ACTION_CONFIG = {
    mangsa      : { title: '🐺 Pilih Mangsamu', sub: 'Werewolf akan menyerang pemain ini malam ini.', btn: 'Serang!', cls: 'mangsa' },
    terawang    : { title: '🔮 Terawang Siapa?', sub: 'Kamu akan mengetahui peran rahasia pemain ini.', btn: 'Terawang!', cls: 'terawang' },
    lindungi    : { title: '🛡️ Lindungi Siapa?', sub: 'Pemain ini akan terlindungi dari serangan malam ini.', btn: 'Lindungi!', cls: 'lindungi' },
    vote_gantung: { title: '⚖️ Vote Gantung', sub: 'Siapa yang kamu curigai sebagai Werewolf?', btn: 'Vote!', cls: 'vote' },
};

function showTargetModal(action) {
    pendingAction = action;
    const cfg = ACTION_CONFIG[action];
    document.getElementById('modal-target-title').textContent = cfg.title;
    document.getElementById('modal-target-sub').textContent   = cfg.sub;

    const grid = document.getElementById('target-grid');
    grid.innerHTML = '';

    // Isi grid dengan pemain yang masih hidup (selain diri sendiri)
    document.querySelectorAll('.player-tile.alive').forEach(tile => {
        const uid   = parseInt(tile.dataset.uid);
        const uname = tile.querySelector('.pt-name').textContent.replace(' (Kamu)', '');
        if (uid === MY_USER_ID) return;

        const div = document.createElement('div');
        div.className = 'target-item';
        div.innerHTML = `<div class="target-avatar">🎮</div><div class="target-name">${esc(uname)}</div>`;
        div.onclick = () => { confirmTargetAction(uid, uname); };
        grid.appendChild(div);
    });

    document.getElementById('modal-target').style.display = 'flex';
}

async function confirmTargetAction(uid, uname) {
    closeModal('modal-target');
    const isVote = pendingAction === 'vote_gantung';
    const endpoint = isVote ? 'werewolf/aksi_vote' : 'werewolf/aksi_malam';
    const body = new URLSearchParams({ room_code: ROOM_CODE, target_user_id: uid });
    if (!isVote) body.append('action_type', pendingAction);

    try {
        const res = await fetch(BASE_URL + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body,
        });
        const data = await res.json();
        if (data.ok) {
            const label = isVote ? `⚖️ Kamu vote ${uname}` : `✅ Aksi dikunci: ${uname}`;
            showToast(label, 'success');
            updateActionFooter(true);

            // Visual marker pada tile target
            document.querySelectorAll('.player-tile').forEach(t => {
                t.classList.remove('voted', 'selected-target');
                if (parseInt(t.dataset.uid) === uid) {
                    t.classList.add(isVote ? 'voted' : 'selected-target');
                }
            });
        } else {
            showToast(data.message || 'Gagal melakukan aksi.', 'error');
        }
    } catch (e) {
        showToast('Koneksi bermasalah.', 'error');
    }
}

function updateActionFooter(done = false) {
    const footer = document.getElementById('action-footer');
    if (!footer) return;

    const hasAction = (
        (myRole === 'werewolf'   && currentPhase === 'malam') ||
        (myRole === 'penerawang' && currentPhase === 'malam') ||
        (myRole === 'pelindung'  && currentPhase === 'malam') ||
        (currentPhase === 'siang_voting' && myIsAlive)
    );

    footer.className = hasAction ? 'action-footer visible' : 'action-footer';
}

// ── Polling ────────────────────────────────────────────────────
async function pollState() {
    if (!polling || gameOver) return;
    try {
        const url = `${BASE_URL}werewolf/poll/${ROOM_CODE}?v=${clientVersion}&chat_id=${lastChatId}`;
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        // Append pesan baru
        if (data.new_chats && data.new_chats.length > 0) {
            appendChats(data.new_chats);
            lastChatId = data.new_chats[data.new_chats.length - 1].id;
        }

        if (data.updated) {
            clientVersion = data.state_version;
            updatePhaseUI(data);
            updatePlayerGrid(data.players);
            if (data.winner_team) {
                gameOver = true;
                polling  = false;
                showEndGame(data.winner_team);
                return;
            }
        }

        // Update countdown
        if (typeof data.seconds_left === 'number') {
            secondsLeft = data.seconds_left;
            maxSeconds  = PHASE_DURATIONS[currentPhase] || 40;
            updateTimer();
        }
    } catch (e) { /* silent */ }

    if (polling && !gameOver) setTimeout(pollState, 1500);
}

function updatePhaseUI(state) {
    currentPhase = state.current_phase;
    let phaseTheme = 'malam';
    if (currentPhase === 'pagi_pengumuman') {
        phaseTheme = 'pagi';
    } else if (currentPhase.startsWith('siang')) {
        phaseTheme = 'siang';
    }

    document.getElementById('ww-arena').className = 'ww-arena ' + phaseTheme;
    document.getElementById('phase-label').textContent = state.phase_label || '';

    // Action footer
    updateActionFooter();

    // Timer maks baru
    maxSeconds = PHASE_DURATIONS[state.current_phase] || 40;
    if (state.seconds_left !== undefined) {
        secondsLeft = state.seconds_left;
        updateTimer();
    }
}

// ── Timer ──────────────────────────────────────────────────────
function updateTimer() {
    const fill = document.getElementById('timer-fill');
    const disp = document.getElementById('timer-display');
    const pct  = maxSeconds > 0 ? (secondsLeft / maxSeconds) * 100 : 0;
    fill.style.width = pct + '%';
    fill.style.background = secondsLeft <= 10 ? '#ef4444' : secondsLeft <= 20 ? '#f97316' : '#22c55e';
    if (disp) disp.textContent = '⏱ ' + secondsLeft + 's';
}

// Countdown lokal
setInterval(() => {
    if (secondsLeft > 0) {
        secondsLeft--;
        updateTimer();
    }
}, 1000);

// ── Append Chats ───────────────────────────────────────────────
function appendChats(chats) {
    const wrap = document.getElementById('messages-wrap');
    chats.forEach(chat => {
        const isNarrator = !chat.sender_id;
        const isMe       = !isNarrator && chat.sender_id === MY_USER_ID;
        const channel    = chat.channel || 'publik';

        let cls = 'msg';
        if (isNarrator) cls += ' narrator';
        else if (isMe) cls += ' me';
        else cls += ' them';
        if (channel === 'werewolf') cls += ' werewolf-msg';
        if (channel === 'arwah')   cls += ' arwah-msg';

        const div = document.createElement('div');
        div.className = cls;
        div.dataset.chatId  = chat.id;
        div.dataset.channel = channel;

        if (!isNarrator && !isMe) {
            div.innerHTML += `<div class="msg-sender">${esc(chat.sender_name || 'Anonim')}</div>`;
        }

        const icon = isNarrator ? '📜 ' : channel === 'werewolf' ? '🐺 ' : channel === 'arwah' ? '👻 ' : '';
        div.innerHTML += `<div class="msg-bubble">${icon}${esc(chat.message).replace(/\n/g,'<br>')}</div>`;

        // Visibilitas sesuai channel aktif
        const visible = isNarrator || channel === activeChannel;
        div.style.display = visible ? 'flex' : 'none';

        wrap.appendChild(div);
    });

    // Auto-scroll ke bawah
    wrap.scrollTop = wrap.scrollHeight;
}

function updatePlayerGrid(players) {
    if (!players) return;
    players.forEach(p => {
        const tile = document.getElementById('ptile-' + p.user_id);
        if (!tile) return;
        if (!p.is_alive) {
            tile.classList.remove('alive');
            tile.classList.add('dead');
            tile.dataset.alive = '0';
            const existing = tile.querySelector('.dead-icon');
            if (!existing) tile.innerHTML += '<div class="dead-icon">💀</div>';
            const statusEl = tile.querySelector('.pt-status');
            if (statusEl) statusEl.textContent = '💀';
        }
    });
}

// ── End Game ───────────────────────────────────────────────────
function showEndGame(winnerTeam) {
    const isWin = (winnerTeam === 'warga' && myRole !== 'werewolf') ||
                  (winnerTeam === 'werewolf' && myRole === 'werewolf');
    const icons = { warga: '🏘️', werewolf: '🐺' };
    const el = document.getElementById('endgame-box');
    el.innerHTML = `
        <div class="endgame-icon">${isWin ? '🏆' : '💀'}</div>
        <div class="endgame-title" style="color:${isWin ? '#f59e0b' : '#ef4444'}">${isWin ? 'Timmu Menang!' : 'Timmu Kalah'}</div>
        <div class="endgame-sub">Tim ${icons[winnerTeam] || ''} <strong>${winnerTeam}</strong> memenangkan permainan!</div>
        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">Kembali ke lobby dalam <span id="cd2">5</span> detik...</p>
        <a href="${BASE_URL}lobby" class="btn btn-primary btn-full">🏠 Kembali ke Lobby</a>
    `;
    document.getElementById('modal-endgame').style.display = 'flex';

    let s = 5;
    const t = setInterval(() => {
        s--; const el2 = document.getElementById('cd2');
        if (el2) el2.textContent = s;
        if (s <= 0) { clearInterval(t); window.location.href = BASE_URL + 'lobby'; }
    }, 1000);
}

// ── Utils ──────────────────────────────────────────────────────
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function showToast(msg, type='info', d=3000) {
    const w = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast toast-' + type; t.textContent = msg; w.appendChild(t);
    setTimeout(() => { t.style.animation='toast-out 0.3s ease forwards'; setTimeout(()=>t.remove(),300); }, d);
}

// ── Surrender Handlers ──────────────────────────────────
function openSurrenderModal() {
    const m = document.getElementById('modal-surrender');
    if (m) m.style.display = 'flex';
}
function closeSurrenderModal() {
    const m = document.getElementById('modal-surrender');
    if (m) m.style.display = 'none';
}
async function confirmSurrender() {
    const btn = document.getElementById('btn-confirm-surrender');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Memproses...'; }
    try {
        const formData = new FormData();
        formData.append('room_code', ROOM_CODE);
        const res = await fetch('<?= site_url('werewolf/aksi_menyerah') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.ok) {
            window.location.href = data.redirect || '<?= site_url('lobby') ?>';
        } else {
            alert(data.message || 'Gagal menyerah.');
            if (btn) { btn.disabled = false; btn.textContent = '🏳️ Ya, Menyerah'; }
        }
    } catch(e) {
        window.location.href = '<?= site_url('lobby') ?>';
    }
}

// ── Aturan Main Modal Handlers ────────────────────────────────
function openRuleModal() {
    const m = document.getElementById('modal-rule-guide');
    if (m) m.style.display = 'flex';
}
function closeRuleModal() {
    const chk = document.getElementById('chk-never-show-ww');
    if (chk && chk.checked) {
        localStorage.setItem('ww_rule_seen', '1');
    }
    const m = document.getElementById('modal-rule-guide');
    if (m) m.style.display = 'none';
}
function switchRuleTab(tabId, btn) {
    document.querySelectorAll('#modal-rule-guide .rule-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('#modal-rule-guide .rule-tab-btn').forEach(b => b.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');
}

// ── Init ───────────────────────────────────────────────────────
updateActionFooter();
updateTimer();

// Scroll chat ke bawah
const mw = document.getElementById('messages-wrap');
if (mw) mw.scrollTop = mw.scrollHeight;

if (gameOver) {
    showEndGame('<?= $state['winner_team'] ?? '' ?>');
} else {
    if (!localStorage.getItem('ww_rule_seen')) {
        setTimeout(openRuleModal, 400);
    }
    setTimeout(pollState, 1500);
}
</script>

<!-- Modal: Panduan Aturan Main Werewolf -->
<div class="modal-overlay" id="modal-rule-guide" style="display:none;" onclick="if(event.target===this)closeRuleModal()">
    <div class="modal-rule-box">
        <div class="modal-rule-header">
            <h3>📖 Panduan & Aturan Werewolf Seru</h3>
            <button type="button" class="btn" style="background:none;border:none;color:#94a3b8;font-size:1.2rem;cursor:pointer;" onclick="closeRuleModal()">✕</button>
        </div>

        <div class="rule-tab-nav">
            <button type="button" class="rule-tab-btn active" onclick="switchRuleTab('tab-ww-tujuan', this)">🎯 Latar & Tujuan</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ww-peran', this)">🎭 Peran / Roles</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ww-fase', this)">🌓 Siklus Fase</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ww-etika', this)">🏳️ Menyerah & Tips</button>
        </div>

        <div class="rule-tab-body">
            <!-- TAB 1: LATAR & TUJUAN -->
            <div id="tab-ww-tujuan" class="rule-tab-pane">
                <h4>🎯 Latar Belakang & 2 Kubu Berseteru</h4>
                <p>Desa Seru yang damai telah disusupi oleh serigala jadi-jadian (Werewolf)! Permainan ini terbagi menjadi <b>2 kubu besar</b> yang saling bermusuhan:</p>
                <br>
                <ul>
                    <li>🏘️ <b>Kubu Warga (Good Guys):</b>
                        Terdiri dari Warga Biasa, Penerawang (Seer), dan Pelindung (Guard).
                        <br><b>Tujuan:</b> Menemukan dan menggantung seluruh Serigala melalui musyawarah & voting siang hari!
                    </li>
                    <li>🐺 <b>Kubu Serigala (Werewolf):</b>
                        Menyamar di antara warga desa pada siang hari.
                        <br><b>Tujuan:</b> Memangsa warga setiap malam hingga jumlah Serigala sama dengan atau melebihi sisa warga yang masih hidup!
                    </li>
                </ul>
            </div>

            <!-- TAB 2: PERAN KARAKTER -->
            <div id="tab-ww-peran" class="rule-tab-pane" style="display:none;">
                <h4>🎭 Mengenal Peran Karakter (Roles)</h4>
                <ul>
                    <li>🐺 <b>Werewolf (Serigala):</b>
                        Setiap malam bermusyawarah di chat rahasia Serigala dan memilih 1 korban mangsa untuk diterkam.
                    </li>
                    <li>🔮 <b>Penerawang (Seer):</b>
                        Warga berkemampuan mistis. Setiap malam dapat menerawang 1 orang pemain untuk mengetahui identitas peran aslinya.
                    </li>
                    <li>🛡️ <b>Pelindung (Guard):</b>
                        Setiap malam dapat memilih 1 pemain untuk dilindungi dari terkaman Serigala. Jika Serigala menyerang pemain tersebut, korban selamat!
                    </li>
                    <li>🏘️ <b>Warga Desa (Villager):</b>
                        Tidak memiliki kekuatan sihir di malam hari, namun memiliki kekuatan analisa, logika, dan hak suara suara voting di siang hari!
                    </li>
                    <li>👻 <b>Arwah Pemain Tewas:</b>
                        Pemain yang gugur tetap dapat menonton jalannya game dan mengobrol di channel khusus arwah gentayangan.
                    </li>
                </ul>
            </div>

            <!-- TAB 3: SIKLUS FASE -->
            <div id="tab-ww-fase" class="rule-tab-pane" style="display:none;">
                <h4>🌓 Siklus Siang & Malam yang Menegangkan</h4>
                <ol>
                    <li>🌙 <b>Fase Malam (Night):</b>
                        Seluruh desa tertidur. Serigala memilih mangsa, Penerawang menerawang peran, dan Pelindung menjaga targetnya.
                    </li>
                    <li>🌅 <b>Fase Pagi — Pengumuman (Dawn):</b>
                        Fajar menyingsing. Narator desa mengumumkan apakah ada korban terkaman semalam atau berhasil dilindungi.
                    </li>
                    <li>☀️ <b>Fase Siang — Diskusi (Day Discussion):</b>
                        Semua warga yang masih hidup bebas berdebat, mencurigai, membela diri, dan mengumpulkan alibi di chat publik!
                    </li>
                    <li>⚖️ <b>Fase Siang — Voting Gantung (Day Voting):</b>
                        Waktu pengambilan keputusan! Semua pemain hidup memberikan suaranya untuk menggantung pemain yang paling dicurigai sebagai Werewolf.
                    </li>
                </ol>
            </div>

            <!-- TAB 4: MENYERAH & TIPS -->
            <div id="tab-ww-etika" class="rule-tab-pane" style="display:none;">
                <h4>🏳️ Fitur Menyerah & Strategi Cerdas</h4>
                <ul>
                    <li>🤫 <b>Jaga Kerahasiaan:</b> Jangan mudah membocorkan peranmu begitu saja, serigala mengincarmu!</li>
                    <li>🔍 <b>Perhatikan Pola Bicara:</b> Pemain yang terlalu agresif menuduh atau terlalu diam sering kali menyembunyikan sesuatu.</li>
                    <li>🏳️ <b>Fitur Menyerah:</b>
                        Jika kamu harus undur diri, gunakan tombol <b>🏳️ Menyerah</b>:
                        <ul>
                            <li>Kamu akan gugur dan kembali ke Lobby.</li>
                            <li>Pemain lain tetap dapat melanjutkan permainan hingga tuntas.</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="modal-rule-footer">
            <label class="chk-rule-label">
                <input type="checkbox" id="chk-never-show-ww"> Jangan tampilkan otomatis lagi
            </label>
            <button type="button" class="btn btn-primary" style="padding:8px 16px; font-weight:800; border-radius:8px; background:linear-gradient(135deg, #a855f7, #7c3aed); color:#fff; border:none; cursor:pointer;" onclick="closeRuleModal()">
                🚀 Mengerti, Siap Bertahan!
            </button>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi Menyerah -->
<div class="modal-overlay" id="modal-surrender" style="display:none;" onclick="if(event.target===this)closeSurrenderModal()">
    <div class="modal-box" style="text-align:center;padding:1.5rem;max-width:380px;">
        <div style="font-size:3rem;margin-bottom:0.5rem;">🏳️</div>
        <div style="font-size:1.2rem;font-weight:900;color:#ef4444;margin-bottom:0.5rem;">MENYERAH DARI PERMAINAN?</div>
        <div style="font-size:0.8rem;color:#cbd5e1;line-height:1.45;margin-bottom:1.25rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:10px;text-align:left;">
            <p style="margin-bottom:6px;font-weight:700;color:#fca5a5;">⚠️ Konfirmasi keluar desa:</p>
            <ul style="padding-left:16px;margin:0;display:flex;flex-direction:column;gap:4px;">
                <li>Kamu akan keluar dari permainan dan kembali ke Lobby.</li>
                <li>Statusmu dianggap gugur/menyerah di Desa Seru.</li>
                <li>Pemain lain akan melanjutkan permainan.</li>
            </ul>
        </div>
        <div style="display:flex;gap:0.75rem;justify-content:center;">
            <button type="button" class="btn" style="flex:1;background:rgba(255,255,255,0.1);color:#fff;border:none;border-radius:8px;padding:8px;cursor:pointer;" onclick="closeSurrenderModal()">
                ❌ Batal
            </button>
            <button type="button" id="btn-confirm-surrender" class="btn" style="flex:1;background:#ef4444;color:#fff;border:none;border-radius:8px;padding:8px;font-weight:700;cursor:pointer;" onclick="confirmSurrender()">
                🏳️ Ya, Menyerah
            </button>
        </div>
    </div>
</div>
</body>
</html>
