<?php
    $room       = $room;
    $state      = $state;
    $user_id    = (int)$user_id;
    $username   = $username;
    $room_code  = $room_code;
    $is_my_turn = (bool)$state['is_my_turn'];
    $winner     = $state['winner'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Game Seru">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/gambargame.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/gambargame.png') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #f8fafc;
            --bg-board: #ffffff;
            --tile-light: #ffffff;
            --tile-dark: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --accent-green: #059669;
            --accent-purple: #7c3aed;
            --accent-gold: #d97706;
            --accent-red: #dc2626;
            --accent-blue: #2563eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            touch-action: manipulation;
            user-select: none;
            -webkit-user-select: none;
        }

        /* ── Header ─────────────────────────────────────────── */
        .game-header {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.875rem;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
            z-index: 50;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--text-main);
            text-decoration: none;
            font-size: 1rem;
            border: 1px solid #cbd5e1;
            transition: background 0.15s;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }
        .header-title-wrap h1 {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .room-code-tag {
            font-size: 0.65rem;
            color: var(--accent-green);
            background: rgba(5, 150, 105, 0.1);
            border: 1px solid rgba(5, 150, 105, 0.2);
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .icon-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: var(--text-main);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .icon-btn:hover {
            background: #e2e8f0;
        }

        /* ── Player Strip (Top Leaderboard) ─────────────────── */
        .players-strip {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.875rem;
            overflow-x: auto;
            scrollbar-width: none;
            background: #f1f5f9;
            border-bottom: 1px solid var(--border-color);
        }
        .players-strip::-webkit-scrollbar { display: none; }

        .player-chip {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .player-chip.is-turn {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.3);
            transform: scale(1.03);
        }
        .player-chip.is-me {
            border-color: rgba(5, 150, 105, 0.6);
        }
        .chip-icon {
            font-size: 1.1rem;
            line-height: 1;
        }
        .chip-info {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
        .chip-name {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-main);
            max-width: 75px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chip-pos {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 800;
        }
        .chip-pos span {
            color: #d97706;
        }

        /* ── Main Arena (Board & Overlay) ───────────────────── */
        .arena-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.5rem;
            overflow: hidden;
            position: relative;
        }

        .board-container {
            width: min(94vw, calc(100dvh - 240px), 460px);
            height: min(94vw, calc(100dvh - 240px), 460px);
            max-width: 460px;
            max-height: 460px;
            position: relative;
            background: var(--bg-board);
            border-radius: 14px;
            border: 2px solid #cbd5e1;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .board-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            grid-template-rows: repeat(10, 1fr);
            width: 100%;
            height: 100%;
        }

        .tile {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 2px 3px;
            font-size: 0.58rem;
            font-weight: 800;
            color: #475569;
            border: 0.5px solid #e2e8f0;
            transition: background 0.2s ease;
        }
        .tile.bg-even {
            background: #ffffff;
        }
        .tile.bg-odd {
            background: #f8fafc;
        }
        .tile.start-tile {
            background: radial-gradient(circle at center, rgba(16, 185, 129, 0.22), rgba(16, 185, 129, 0.06));
            border-color: #10b981;
            color: #047857;
        }
        .tile.finish-tile {
            background: radial-gradient(circle at center, rgba(245, 158, 11, 0.35), rgba(245, 158, 11, 0.12));
            border-color: #f59e0b;
            color: #b45309 !important;
            animation: pulse-finish 2s infinite ease-in-out;
        }
        @keyframes pulse-finish {
            0%, 100% { box-shadow: inset 0 0 8px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: inset 0 0 16px rgba(245, 158, 11, 0.6); }
        }

        .tile-num {
            line-height: 1;
            z-index: 2;
        }
        .tile.ladder-base {
            background: rgba(16, 185, 129, 0.18);
            border-color: rgba(16, 185, 129, 0.4);
        }
        .tile.snake-head {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.4);
        }
        .tile-badge-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            font-size: 0.52rem;
            line-height: 1;
            z-index: 2;
            opacity: 0.85;
        }

        /* SVG Connectors Layer */
        .board-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        /* Pawns Layer */
        .pawns-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 20;
        }

        .pawn-token {
            position: absolute;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border: 2px solid #ffffff;
            transition: left 0.18s cubic-bezier(0.25, 1, 0.5, 1), top 0.18s cubic-bezier(0.25, 1, 0.5, 1), transform 0.2s ease;
            transform: translate(-50%, -50%);
            z-index: 25;
        }
        .pawn-token.pawn-hopping {
            animation: pawnHopJump 0.22s ease-in-out infinite alternate !important;
            z-index: 45 !important;
        }
        @keyframes pawnHopJump {
            0% { transform: translate(-50%, -50%) scale(1, 1); }
            50% { transform: translate(-50%, -105%) scale(1.15, 1.25); }
            100% { transform: translate(-50%, -50%) scale(1.2, 0.85); }
        }
        .pawn-token.sliding {
            transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1), top 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 45 !important;
        }
        .tile.tile-step-glow {
            background: rgba(245, 158, 11, 0.4) !important;
            box-shadow: inset 0 0 10px rgba(245, 158, 11, 0.7), 0 0 8px rgba(245, 158, 11, 0.5) !important;
            border-color: #f59e0b !important;
            transform: scale(1.08);
            z-index: 15;
            transition: all 0.15s ease;
        }
        .tile.tile-landed-glow {
            background: rgba(16, 185, 129, 0.45) !important;
            box-shadow: inset 0 0 14px rgba(16, 185, 129, 0.8), 0 0 10px rgba(16, 185, 129, 0.6) !important;
            border-color: #10b981 !important;
            transform: scale(1.14);
            z-index: 15;
            transition: all 0.25s ease;
        }
        .board-container.board-landing-thud {
            animation: boardShake 0.25s ease-out;
        }
        @keyframes boardShake {
            0% { transform: scale(1); }
            40% { transform: scale(0.985) translateY(3px); }
            70% { transform: scale(1.008) translateY(-1px); }
            100% { transform: scale(1) translateY(0); }
        }

        /* ── Bottom Controls ────────────────────────────────── */
        .controls-area {
            flex-shrink: 0;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04);
            padding: 0.625rem 0.875rem max(24px, calc(env(safe-area-inset-bottom, 0px) + 16px));
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 40;
        }

        .turn-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
        }
        .turn-banner.my-turn {
            border-color: #10b981;
            background: #ecfdf5;
        }
        .turn-text {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-main);
        }
        .turn-text.my-turn-text {
            color: #047857;
            animation: pulse-glow-text 1.5s infinite;
        }
        @keyframes pulse-glow-text {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.75; }
        }
        .log-peek {
            font-size: 0.72rem;
            color: #64748b;
            max-width: 170px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Roll Row */
        .action-row {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        /* 3D Dice */
        .dice-wrap {
            width: 48px;
            height: 48px;
            perspective: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .dice-cube {
            width: 40px;
            height: 40px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.3, 1);
            transform: rotateX(-20deg) rotateY(-25deg);
        }
        .dice-cube.rolling {
            animation: rollDiceAnim 0.7s infinite linear;
        }
        @keyframes rollDiceAnim {
            0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
            100% { transform: rotateX(720deg) rotateY(1080deg) rotateZ(360deg); }
        }

        .dice-face {
            position: absolute;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffffff, #f1f5f9);
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.08);
        }
        .dice-face.front  { transform: translateZ(20px); }
        .dice-face.back   { transform: rotateY(180deg) translateZ(20px); }
        .dice-face.right  { transform: rotateY(90deg) translateZ(20px); }
        .dice-face.left   { transform: rotateY(-90deg) translateZ(20px); }
        .dice-face.top    { transform: rotateX(90deg) translateZ(20px); }
        .dice-face.bottom { transform: rotateX(-90deg) translateZ(20px); }

        .btn-roll {
            flex: 1;
            min-height: 48px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.15s ease;
        }
        .btn-roll:active:not(:disabled) {
            transform: scale(0.97);
        }
        .btn-roll:disabled {
            background: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
            border: 1px dashed #cbd5e1;
        }
        .btn-roll.can-roll {
            animation: pulse-btn-roll 1.8s infinite;
        }
        @keyframes pulse-btn-roll {
            0%, 100% { box-shadow: 0 4px 18px rgba(16, 185, 129, 0.35); }
            50% { box-shadow: 0 6px 26px rgba(16, 185, 129, 0.65), 0 0 14px rgba(52, 211, 153, 0.5); }
        }

        .btn-log {
            width: 44px;
            height: 48px;
            border-radius: 12px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.15s;
        }
        .btn-log:hover {
            background: #e2e8f0;
        }

        /* ── Modal Log Riwayat & Rule Guide ──────────────────── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 100;
        }
        .modal-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            color: var(--text-main);
        }
        .modal-header {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 { font-size: 1rem; font-weight: 800; color: var(--text-main); }
        .modal-close-btn {
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.25rem;
            cursor: pointer;
        }
        .log-list {
            padding: 0.875rem 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .log-item {
            font-size: 0.78rem;
            padding: 0.5rem 0.625rem;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid var(--accent-purple);
            line-height: 1.35;
            color: #334155;
        }
        .log-item.ladder { border-left-color: var(--accent-green); }
        .log-item.snake  { border-left-color: var(--accent-red); }
        .log-item.win    { border-left-color: var(--accent-gold); }

        /* ── Modal Rule Guide ── */
        .modal-rule-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            width: min(92vw, 460px);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
            color: var(--text-main);
        }
        .modal-rule-header {
            padding: 0.875rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-rule-header h3 { font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 6px; }
        .rule-tab-nav {
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 0.5rem;
            gap: 0.25rem;
        }
        .rule-tab-nav::-webkit-scrollbar { display: none; }
        .rule-tab-btn {
            background: none;
            border: none;
            color: #64748b;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.6rem 0.75rem;
            cursor: pointer;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
            transition: all 0.15s;
        }
        .rule-tab-btn.active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
        }
        .rule-tab-body {
            padding: 1rem;
            overflow-y: auto;
            flex: 1;
            font-size: 0.8rem;
            line-height: 1.55;
            color: #334155;
        }
        .rule-tab-body h4 {
            color: #0f172a;
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
        .rule-tab-body b { color: #0f172a; }
        .modal-rule-footer {
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .chk-rule-label {
            font-size: 0.72rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
        }

        /* ── Modal Menang / Kalah ───────────────────────────── */
        #modal-winner .modal-box {
            text-align: center;
            padding: 1.75rem 1.25rem;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 2px solid rgba(245, 158, 11, 0.4);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.18);
            transition: all 0.3s ease;
        }
        .winner-trophy {
            font-size: 4rem;
            margin-bottom: 0.5rem;
            animation: bounceTrophy 0.8s infinite alternate ease-in-out;
        }
        @keyframes bounceTrophy {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(-10px) scale(1.1); }
        }
        .winner-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #b45309;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }
        .winner-badge {
            margin: 6px auto 12px;
            display: inline-block;
            padding: 4px 14px;
            border-radius: 99px;
            font-size: 0.82rem;
            font-weight: 800;
        }
        .winner-sub {
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 1.25rem;
            line-height: 1.45;
        }

        /* ── Toasts ─────────────────────────────────────────── */
        #toast-container {
            position: fixed;
            top: 4.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            pointer-events: none;
            width: 90%;
            max-width: 380px;
        }
        .game-toast {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            animation: fadeInToast 0.2s ease;
            text-align: center;
        }
        @keyframes fadeInToast {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- ── Header ─────────────────────────────────────────── -->
    <header class="game-header">
        <div class="header-left">
            <a href="javascript:void(0)" onclick="openSurrenderModal()" class="btn-back" title="Menyerah / Keluar ke Lobby">←</a>
            <div class="header-title-wrap">
                <h1>🎲 ULAR TANGGA</h1>
                <span class="room-code-tag"><?= $room_code ?></span>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-rule" onclick="openRuleModal()" title="Panduan Aturan Main" style="background:rgba(124,58,237,0.1);border:1px solid rgba(124,58,237,0.3);color:#7c3aed;padding:4px 10px;border-radius:8px;font-weight:700;font-size:0.75rem;cursor:pointer;display:flex;align-items:center;gap:4px;">📖 Aturan</button>
            <button class="btn-surrender" onclick="openSurrenderModal()" title="Menyerah dan keluar ke Lobby" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#dc2626;padding:4px 10px;border-radius:8px;font-weight:700;font-size:0.75rem;cursor:pointer;display:flex;align-items:center;gap:4px;">🏳️ Menyerah</button>
            <button class="icon-btn" onclick="toggleMute()" id="btn-sound" title="Sound On/Off">🔊</button>
            <button class="icon-btn" onclick="pollState()" title="Sinkronkan Ulang">🔄</button>
        </div>
    </header>

    <!-- ── Player Strip ───────────────────────────────────── -->
    <div class="players-strip" id="players-strip">
        <?php foreach ($state['players'] as $p): ?>
        <div class="player-chip <?= $p['is_turn'] ? 'is-turn' : '' ?> <?= $p['is_me'] ? 'is-me' : '' ?>" id="chip-<?= $p['user_id'] ?>">
            <div class="chip-icon"><?= $p['pawn_icon'] ?></div>
            <div class="chip-info">
                <span class="chip-name"><?= htmlspecialchars($p['username']) ?><?= $p['is_me'] ? ' (Kamu)' : '' ?></span>
                <span class="chip-pos">Kotak <span id="pos-text-<?= $p['user_id'] ?>"><?= $p['position'] ?></span></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Arena Board (Responsive 10x10) ─────────────────── -->
    <main class="arena-main">
        <div class="board-container" id="board-container">
            <!-- Grid 10x10 (100 Kotak) -->
            <div class="board-grid" id="board-grid"></div>

            <!-- SVG Tangga & Ular -->
            <svg class="board-svg" id="board-svg" viewBox="0 0 100 100" preserveAspectRatio="none"></svg>

            <!-- Layer Pion Pemain -->
            <div class="pawns-layer" id="pawns-layer"></div>
        </div>
    </main>

    <!-- ── Bottom Controls ────────────────────────────────── -->
    <footer class="controls-area">
        <!-- Turn Status -->
        <div class="turn-banner <?= $is_my_turn ? 'my-turn' : '' ?>" id="turn-banner">
            <div class="turn-text <?= $is_my_turn ? 'my-turn-text' : '' ?>" id="turn-text">
                <?= $is_my_turn ? '✨ Giliran Kamu! Lempar dadumu!' : '⏳ Menunggu ' . htmlspecialchars($state['current_turn_player']['username'] ?? 'Pemain') . '...' ?>
            </div>
            <div class="log-peek" id="log-peek">
                <?= !empty($state['logs']) ? htmlspecialchars(end($state['logs'])['message']) : 'Permainan siap dimulai!' ?>
            </div>
        </div>

        <!-- Dice Roll Row -->
        <div class="action-row">
            <!-- 3D Dice Graphic -->
            <div class="dice-wrap" onclick="if(isMyTurn)rollDice()">
                <div class="dice-cube" id="dice-cube">
                    <div class="dice-face front"  id="df-front">⚀</div>
                    <div class="dice-face back"   id="df-back">⚅</div>
                    <div class="dice-face right"  id="df-right">⚂</div>
                    <div class="dice-face left"   id="df-left">⚃</div>
                    <div class="dice-face top"    id="df-top">⚁</div>
                    <div class="dice-face bottom" id="df-bottom">⚄</div>
                </div>
            </div>

            <!-- Roll Action Button -->
            <button class="btn-roll <?= $is_my_turn ? 'can-roll' : '' ?>" id="btn-roll" onclick="rollDice()" <?= !$is_my_turn ? 'disabled' : '' ?>>
                <span>🎲 LEMPAR DADU</span>
            </button>

            <!-- Log Button -->
            <button class="btn-log" onclick="showModal('modal-log')" title="Lihat Riwayat Gerakan">📜</button>
        </div>
    </footer>

    <!-- ── Modal: Log Riwayat Gerakan ─────────────────────── -->
    <div class="modal-overlay" id="modal-log" onclick="if(event.target===this)closeModal('modal-log')">
        <div class="modal-box">
            <div class="modal-header">
                <h3>📜 Riwayat Gerakan</h3>
                <button class="modal-close-btn" onclick="closeModal('modal-log')">✕</button>
            </div>
            <div class="log-list" id="modal-log-list">
                <?php foreach (array_reverse($state['logs']) as $l): ?>
                <div class="log-item <?= $l['action_type'] ?>">
                    <?= htmlspecialchars($l['message']) ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── Modal: Konfirmasi Menyerah ─────────────────────── -->
    <div class="modal-overlay" id="modal-surrender" style="display:none;" onclick="if(event.target===this)closeSurrenderModal()">
        <div class="modal-box" style="text-align:center;padding:1.5rem;max-width:380px;">
            <div style="font-size:3rem;margin-bottom:0.5rem;">🏳️</div>
            <div style="font-size:1.2rem;font-weight:900;color:#ef4444;margin-bottom:0.5rem;">MENYERAH DARI PERMAINAN?</div>
            <div style="font-size:0.8rem;color:#cbd5e1;line-height:1.45;margin-bottom:1.25rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:10px;text-align:left;">
                <p style="margin-bottom:6px;font-weight:700;color:#fca5a5;">⚠️ Konfirmasi keluar:</p>
                <ul style="padding-left:16px;margin:0;display:flex;flex-direction:column;gap:4px;">
                    <li>Kamu akan keluar dari permainan dan kembali ke Lobby.</li>
                    <li><b>Main 2 orang:</b> Lawan langsung menang seketika.</li>
                    <li><b>Main 3+ orang:</b> Pemain lain akan melanjutkan permainan.</li>
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

    <!-- ── Modal: Pemenang Game ───────────────────────────── -->
    <?php
        $is_me_win = ($winner && (int)$winner['user_id'] === $user_id);
    ?>
    <div class="modal-overlay" id="modal-winner" style="<?= $winner ? 'display:flex;' : 'display:none;' ?>">
        <div class="modal-box" id="winner-modal-box" style="<?= $is_me_win ? 'border-color:rgba(245, 158, 11, 0.5);box-shadow:0 0 35px rgba(245, 158, 11, 0.35);' : 'border-color:rgba(239, 68, 68, 0.5);box-shadow:0 0 35px rgba(239, 68, 68, 0.3);' ?>">
            <div class="winner-trophy" id="winner-modal-icon"><?= $is_me_win ? '🏆' : '💀' ?></div>
            <div class="winner-title" id="winner-modal-title" style="color:<?= $is_me_win ? '#fef08a' : '#ef4444' ?>;">
                <?= $is_me_win ? '🎉 KAMU MENANG!' : '💀 KAMU KALAH!' ?>
            </div>
            <div class="winner-badge" id="winner-modal-badge" style="background:<?= $is_me_win ? 'rgba(245, 158, 11, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; color:<?= $is_me_win ? '#fbbf24' : '#fca5a5' ?>; border:1px solid <?= $is_me_win ? 'rgba(245, 158, 11, 0.4)' : 'rgba(239, 68, 68, 0.4)' ?>;">
                <?= $is_me_win ? '👑 JUARA 1 ULAR TANGGA' : ('👑 Pemenang: ' . htmlspecialchars($winner['username'] ?? 'Pemain Lain')) ?>
            </div>
            <div class="winner-sub" id="winner-modal-sub">
                <?= $is_me_win 
                    ? 'Hebat! Kamu berhasil mencapai kotak 100 pertama kali dan menaklukkan seluruh papan! 🥳' 
                    : (htmlspecialchars($winner['username'] ?? 'Pemain Lain') . ' telah berhasil mencapai kotak 100 terlebih dahulu. Tetap semangat, balas kekalahanmu di ronde berikutnya! 💪') ?>
            </div>
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:8px 12px; margin-bottom:1rem;">
                <p style="font-size:0.75rem; color:#94a3b8; margin:0;">Otomatis kembali ke Lobby dalam <span id="winner-countdown" style="font-weight:800; color:#fff;">5</span> detik...</p>
            </div>
            <div style="display:flex;gap:0.6rem;">
                <a href="<?= site_url('lobby') ?>" class="btn btn-primary" style="flex:1;text-decoration:none;display:flex;align-items:center;justify-content:center;padding:10px;font-weight:800;border-radius:10px;">
                    🏠 Kembali ke Lobby Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- ── Modal: Panduan Aturan Main Ular Tangga ───────────── -->
    <div class="modal-overlay" id="modal-rule-guide" style="display:none;" onclick="if(event.target===this)closeRuleModal()">
        <div class="modal-rule-box">
            <div class="modal-rule-header">
                <h3>📖 Aturan Main Ular Tangga Seru</h3>
                <button type="button" class="modal-close-btn" onclick="closeRuleModal()">✕</button>
            </div>

            <div class="rule-tab-nav">
                <button type="button" class="rule-tab-btn active" onclick="switchRuleTab('tab-ut-tujuan', this)">🎯 Tujuan</button>
                <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ut-tangga', this)">🪜 Tangga & Ular</button>
                <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ut-dadu', this)">🎲 Dadu 6 & Bounce</button>
                <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-ut-menyerah', this)">🏳️ Menyerah & Menang</button>
            </div>

            <div class="rule-tab-body">
                <!-- TAB 1: TUJUAN -->
                <div id="tab-ut-tujuan" class="rule-tab-pane">
                    <h4>🎯 Tujuan Permainan</h4>
                    <p>Ular Tangga adalah permainan papan klasik 10x10 (100 petak). Tujuan utamanya adalah menjadi pemain pertama yang berhasil <b>mencapai tepat di Kotak 100</b>!</p>
                    <br>
                    <ul>
                        <li><b>Posisi Awal:</b> Semua pemain memulai bidak dari Kotak 1 (🚩 Mulai).</li>
                        <li><b>Jalur Papan:</b> Jalur melangkah berbentuk zigzag: Baris 1 (1 ke 10 ke kanan), Baris 2 (11 ke 20 ke kiri), Baris 3 (21 ke 30 ke kanan) dan seterusnya hingga Kotak 100 di puncak kiri atas.</li>
                        <li><b>Giliran:</b> Permainan bergiliran memutar. Tekan tombol <b>🎲 LEMPAR DADU</b> saat tiba giliranmu!</li>
                    </ul>
                </div>

                <!-- TAB 2: TANGGA & ULAR -->
                <div id="tab-ut-tangga" class="rule-tab-pane" style="display:none;">
                    <h4>🪜 Tangga & 🐍 Ular Pembawa Hoki/Sial</h4>
                    <ul>
                        <li>🪜 <b>Naik Tangga (Keberuntungan):</b>
                            Jika bidakmu mendarat tepat di <b>pangkal (bawah) tangga</b>, bidakmu akan langsung mendaki naik ke puncak tangga! Ini mempercepat jalanmu menuju kemenangan!
                        </li>
                        <li>🐍 <b>Turun Ular (Jebakan):</b>
                            Jika bidakmu mendarat tepat di <b>kepala ular</b>, bidakmu akan tertelan dan merosot turun ke ujung ekor ular. Hati-hati jangan sampai tergelincir!
                        </li>
                        <li>✨ <b>Rantai Kejadian:</b> Satu petak hanya memicu satu kali aksi tangga/ular per giliran melangkah.</li>
                    </ul>
                </div>

                <!-- TAB 3: DADU 6 & BOUNCE -->
                <div id="tab-ut-dadu" class="rule-tab-pane" style="display:none;">
                    <h4>🎲 Dadu Angka 6 & Aturan Memantul (Bounce)</h4>
                    <ul>
                        <li>🎉 <b>Bonus Lemparan Dadu 6:</b>
                            Jika kamu mendapatkan angka <b>6</b> saat melempar dadu, setelah bidakmu selesai melangkah, kamu berhak <b>melempar dadu lagi 1 kali (Bonus Turn)</b>!
                        </li>
                        <li>↩️ <b>Aturan Memantul (Bounce di Kotak 100):</b>
                            Untuk memenangkan permainan, kamu wajib mendarat <b>tepat pas di Kotak 100</b>.
                            <ul>
                                <li>Contoh: Jika posisimu di Kotak 98 dan kamu melempar dadu dapat <b>4</b>:</li>
                                <li>Bidakmu melangkah 2 langkah ke Kotak 100, lalu memantul mundur 2 langkah ke <b>Kotak 98</b>!</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <!-- TAB 4: MENYERAH & MENANG -->
                <div id="tab-ut-menyerah" class="rule-tab-pane" style="display:none;">
                    <h4>🏳️ Aturan Menyerah & Penentuan Juara</h4>
                    <ul>
                        <li>🏆 <b>Juara 1:</b> Pemain yang pertama kali mendarat tepat di Kotak 100 dinyatakan sebagai <b>Juara 1 Ular Tangga</b> dan permainan selesai!</li>
                        <li>🏳️ <b>Fitur Menyerah:</b>
                            Setiap saat kamu bisa menekan tombol <b>🏳️ Menyerah</b>:
                            <ul>
                                <li><b>Main 2 orang:</b> Jika kamu menyerah, lawanmu otomatis langsung menang seketika.</li>
                                <li><b>Main 3+ orang:</b> Jika kamu menyerah, kamu keluar ke Lobby dan pemain lainnya tetap melanjutkan pertandingan.</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="modal-rule-footer">
                <label class="chk-rule-label">
                    <input type="checkbox" id="chk-never-show-ut"> Jangan tampilkan otomatis lagi
                </label>
                <button type="button" class="btn btn-primary" style="padding:8px 16px; font-weight:800; border-radius:8px; background:linear-gradient(135deg, #10b981, #059669); color:#fff; border:none; cursor:pointer;" onclick="closeRuleModal()">
                    🚀 Mengerti, Gas Main!
                </button>
            </div>
        </div>
    </div>

    <!-- ── Toast Container ────────────────────────────────── -->
    <div id="toast-container"></div>

    <script>
    const BASE_URL   = '<?= site_url() ?>';
    const ROOM_CODE  = '<?= $room_code ?>';
    const MY_USER_ID = <?= $user_id ?>;
    const MY_USERNAME = '<?= addslashes($username) ?>';

    let clientVersion = <?= (int)$version ?>;
    let isMyTurn      = <?= $is_my_turn ? 'true' : 'false' ?>;
    let gameOver      = <?= $winner ? 'true' : 'false' ?>;
    let isRolling     = false;
    let isWalking     = false;
    let soundEnabled  = true;

    // Track posisi bidak setiap pemain untuk animasi melangkah per petak
    let playerPositions = {};
    <?php foreach ($state['players'] as $p): ?>
    playerPositions[<?= (int)$p['user_id'] ?>] = <?= (int)($p['position'] ?: 1) ?>;
    <?php endforeach; ?>

    // Konfigurasi Papan
    const LADDERS = <?= json_encode($state['board_config']['ladders']) ?>;
    const SNAKES  = <?= json_encode($state['board_config']['snakes']) ?>;

    const DICE_SYMBOLS = { 1: '⚀', 2: '⚁', 3: '⚂', 4: '⚃', 5: '⚄', 6: '⚅' };
    const DICE_ROTATIONS = {
        1: { x: 0,   y: 0 },
        2: { x: -90, y: 0 },
        3: { x: 0,   y: -90 },
        4: { x: 0,   y: 90 },
        5: { x: 90,  y: 0 },
        6: { x: 0,   y: 180 }
    };

    let playersData = <?= json_encode($state['players']) ?>;

    // ── Inisialisasi Papan 10x10 Zigzag ───────────────────────
    function buildBoard() {
        const grid = document.getElementById('board-grid');
        grid.innerHTML = '';

        // Baris 10 (atas: 100-91) s/d Baris 1 (bawah: 1-10)
        for (let row = 9; row >= 0; row--) {
            const isLeftToRight = (row % 2 === 0);
            for (let col = 0; col < 10; col++) {
                const actualCol = isLeftToRight ? col : (9 - col);
                const tileNum = (row * 10) + actualCol + 1;

                const div = document.createElement('div');
                div.className = `tile ${tileNum % 2 === 0 ? 'bg-even' : 'bg-odd'}`;
                div.id = `tile-${tileNum}`;
                div.dataset.tile = tileNum;

                if (tileNum === 1) div.classList.add('start-tile');
                if (tileNum === 100) div.classList.add('finish-tile');

                let badge = '';
                if (LADDERS[tileNum]) {
                    div.classList.add('ladder-base');
                    badge = `🪜${LADDERS[tileNum]}`;
                } else if (SNAKES[tileNum]) {
                    div.classList.add('snake-head');
                    badge = `🐍${SNAKES[tileNum]}`;
                } else if (tileNum === 100) {
                    badge = '🏆';
                } else if (tileNum === 1) {
                    badge = '🚩';
                }

                div.innerHTML = `
                    <span class="tile-num">${tileNum}</span>
                    ${badge ? `<span class="tile-badge-indicator">${badge}</span>` : ''}
                `;
                grid.appendChild(div);
            }
        }

        drawSnakesAndLadders();
        renderPawns();
    }

    // ── Gambar Tangga & Ular di SVG ──────────────────────────
    function getTileCenterPercent(num) {
        num = Math.max(1, Math.min(100, parseInt(num || 1)));
        const rowFromBottom = Math.floor((num - 1) / 10);
        const colInRow = (num - 1) % 10;
        const isLeftToRight = (rowFromBottom % 2 === 0);
        const col = isLeftToRight ? colInRow : (9 - colInRow);
        const row = 9 - rowFromBottom; // invert for top-to-bottom SVG coordinates

        const x = (col * 10) + 5;
        const y = (row * 10) + 5;
        return { x, y };
    }

    function drawSnakesAndLadders() {
        const svg = document.getElementById('board-svg');
        svg.innerHTML = `
            <defs>
                <linearGradient id="grad-ladder" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#34d399" />
                    <stop offset="100%" stop-color="#059669" />
                </linearGradient>
                <linearGradient id="grad-snake" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#ef4444" />
                    <stop offset="50%" stop-color="#f59e0b" />
                    <stop offset="100%" stop-color="#dc2626" />
                </linearGradient>
                <filter id="glow-snake" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="1" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
            </defs>
        `;

        // 1. Gambar Tangga (Ladders)
        for (const [startStr, endNum] of Object.entries(LADDERS)) {
            const startNum = parseInt(startStr);
            const p1 = getTileCenterPercent(startNum);
            const p2 = getTileCenterPercent(endNum);

            // Hitung vector tegak lurus untuk dua rel tangga
            const dx = p2.x - p1.x;
            const dy = p2.y - p1.y;
            const len = Math.hypot(dx, dy) || 1;
            const offset = 1.6; // jarak antar rel
            const nx = (-dy / len) * offset;
            const ny = (dx / len) * offset;

            // Rel Kiri & Kanan
            const rail1 = `<line x1="${p1.x - nx}" y1="${p1.y - ny}" x2="${p2.x - nx}" y2="${p2.y - ny}" stroke="url(#grad-ladder)" stroke-width="0.9" stroke-linecap="round" />`;
            const rail2 = `<line x1="${p1.x + nx}" y1="${p1.y + ny}" x2="${p2.x + nx}" y2="${p2.y + ny}" stroke="url(#grad-ladder)" stroke-width="0.9" stroke-linecap="round" />`;

            // Anak Tangga (Rungs)
            const numRungs = Math.max(3, Math.floor(len / 4.5));
            let rungs = '';
            for (let i = 1; i < numRungs; i++) {
                const t = i / numRungs;
                const rx = p1.x + dx * t;
                const ry = p1.y + dy * t;
                rungs += `<line x1="${rx - nx}" y1="${ry - ny}" x2="${rx + nx}" y2="${ry + ny}" stroke="#6ee7b7" stroke-width="0.75" />`;
            }

            svg.innerHTML += rail1 + rail2 + rungs;
        }

        // 2. Gambar Ular (Snakes)
        for (const [headStr, tailNum] of Object.entries(SNAKES)) {
            const headNum = parseInt(headStr);
            const pHead = getTileCenterPercent(headNum);
            const pTail = getTileCenterPercent(tailNum);

            const dx = pTail.x - pHead.x;
            const dy = pTail.y - pHead.y;
            const midX = (pHead.x + pTail.x) / 2 + (headNum % 2 === 0 ? 6 : -6);
            const midY = (pHead.y + pTail.y) / 2;

            // Kurva quadratic path
            const pathData = `M ${pHead.x} ${pHead.y} Q ${midX} ${midY} ${pTail.x} ${pTail.y}`;
            const snakeBody = `<path d="${pathData}" fill="none" stroke="url(#grad-snake)" stroke-width="1.8" stroke-linecap="round" filter="url(#glow-snake)" stroke-dasharray="3, 1" />`;

            // Kepala Ular
            const headCircle = `<circle cx="${pHead.x}" cy="${pHead.y}" r="2" fill="#ef4444" stroke="#ffffff" stroke-width="0.6" />`;
            // Ekor Ular
            const tailCircle = `<circle cx="${pTail.x}" cy="${pTail.y}" r="1" fill="#f59e0b" />`;

            svg.innerHTML += snakeBody + headCircle + tailCircle;
        }
    }

    // ── Render Pion Pemain (Tanpa Menghapus DOM secara Brutal) ────────
    function renderPawns() {
        const layer = document.getElementById('pawns-layer');
        if (!layer) return;

        // Kelompokkan pemain berdasarkan posisi kotak untuk mencegah tumpang tindih
        const posMap = {};
        const activeUserIds = new Set();

        playersData.forEach(p => {
            activeUserIds.add(p.user_id);
            const pos = playerPositions[p.user_id] !== undefined ? playerPositions[p.user_id] : (p.position || 1);
            if (!posMap[pos]) posMap[pos] = [];
            posMap[pos].push(p);
        });

        // Hapus token pemain yang sudah keluar / menyerah
        const existingTokens = layer.querySelectorAll('.pawn-token');
        existingTokens.forEach(t => {
            const uid = parseInt(t.dataset.userId);
            if (uid && !activeUserIds.has(uid)) {
                t.remove();
            }
        });

        for (const [pos, plist] of Object.entries(posMap)) {
            const center = getTileCenterPercent(parseInt(pos));
            const count = plist.length;

            plist.forEach((p, idx) => {
                let offX = 0;
                let offY = 0;
                if (count === 2) {
                    offX = (idx === 0) ? -2.2 : 2.2;
                } else if (count > 2) {
                    const angle = (idx / count) * 2 * Math.PI;
                    offX = Math.cos(angle) * 2.5;
                    offY = Math.sin(angle) * 2.5;
                }

                const left = center.x + offX;
                const top  = center.y + offY;

                let token = document.getElementById(`pawn-user-${p.user_id}`);
                if (!token) {
                    token = document.createElement('div');
                    token.className = 'pawn-token';
                    token.id = `pawn-user-${p.user_id}`;
                    token.dataset.userId = p.user_id;
                    layer.appendChild(token);
                }

                if (!token.classList.contains('pawn-hopping') && !token.classList.contains('sliding')) {
                    token.style.left = `${left}%`;
                    token.style.top  = `${top}%`;
                }

                const bgColor = p.pawn_color === 'merah' ? '#dc2626' : (p.pawn_color === 'biru' ? '#2563eb' : (p.pawn_color === 'hijau' ? '#16a34a' : (p.pawn_color === 'kuning' ? '#ca8a04' : (p.pawn_color === 'ungu' ? '#7c3aed' : '#ea580c'))));
                token.style.background = bgColor;
                token.textContent = p.pawn_icon || '♟️';
                token.title = `${p.username} (Kotak ${pos})`;
            });
        }
    }

    // ── Engine Animasi Melangkah Per Petak (Hopping Walk) ─────────────
    function calculatePath(fromPos, targetPos, actionType) {
        let path = [];
        fromPos = parseInt(fromPos || 1);
        targetPos = parseInt(targetPos || 1);

        if (actionType === 'bounce') {
            for (let p = fromPos + 1; p <= 100; p++) {
                path.push(p);
            }
            for (let p = 99; p >= targetPos; p--) {
                path.push(p);
            }
        } else {
            if (targetPos > fromPos) {
                for (let p = fromPos + 1; p <= targetPos; p++) {
                    path.push(p);
                }
            } else if (targetPos < fromPos) {
                for (let p = fromPos - 1; p >= targetPos; p--) {
                    path.push(p);
                }
            }
        }
        return path;
    }

    function animateWalk(userId, fromPos, targetPos, actionType, finalPos, onComplete) {
        fromPos = parseInt(fromPos || 1);
        targetPos = parseInt(targetPos || fromPos);
        finalPos = parseInt(finalPos || targetPos);

        if (fromPos === finalPos && fromPos === targetPos) {
            playerPositions[userId] = finalPos;
            renderPawns();
            if (onComplete) onComplete();
            return;
        }

        isWalking = true;
        let path = calculatePath(fromPos, targetPos, actionType);
        if (path.length === 0) path = [targetPos];

        let pawnEl = document.getElementById(`pawn-user-${userId}`);
        if (!pawnEl) {
            renderPawns();
            pawnEl = document.getElementById(`pawn-user-${userId}`);
        }

        let stepIndex = 0;

        function step() {
            if (stepIndex >= path.length) {
                // Sampai di targetPos!
                playerPositions[userId] = targetPos;

                // Cek apakah ada Tangga atau Ular
                if (actionType === 'ladder' && finalPos !== targetPos) {
                    showToast(`🪜 HORE! Naik tangga ke kotak ${finalPos}!`, 'success', 2200);
                    playAudio('ladder');

                    setTimeout(() => {
                        if (pawnEl) {
                            pawnEl.classList.remove('pawn-hopping');
                            pawnEl.classList.add('sliding');
                            const finalCenter = getTileCenterPercent(finalPos);
                            pawnEl.style.left = `${finalCenter.x}%`;
                            pawnEl.style.top = `${finalCenter.y}%`;
                        }

                        setTimeout(() => {
                            if (pawnEl) pawnEl.classList.remove('sliding');
                            playerPositions[userId] = finalPos;
                            finishWalk();
                        }, 600);
                    }, 300);

                } else if (actionType === 'snake' && finalPos !== targetPos) {
                    showToast(`🐍 ADUH! Terpeleset ular ke kotak ${finalPos}!`, 'error', 2200);
                    playAudio('snake');

                    setTimeout(() => {
                        if (pawnEl) {
                            pawnEl.classList.remove('pawn-hopping');
                            pawnEl.classList.add('sliding');
                            const finalCenter = getTileCenterPercent(finalPos);
                            pawnEl.style.left = `${finalCenter.x}%`;
                            pawnEl.style.top = `${finalCenter.y}%`;
                        }

                        setTimeout(() => {
                            if (pawnEl) pawnEl.classList.remove('sliding');
                            playerPositions[userId] = finalPos;
                            finishWalk();
                        }, 650);
                    }, 300);

                } else {
                    finishWalk();
                }
                return;
            }

            const nextTile = path[stepIndex];
            playerPositions[userId] = nextTile;

            // Pindahkan posisi token pion di board
            if (pawnEl) {
                const center = getTileCenterPercent(nextTile);
                pawnEl.classList.add('pawn-hopping');
                pawnEl.style.left = `${center.x}%`;
                pawnEl.style.top  = `${center.y}%`;
            }

            // Highlight petak yang sedang diinjak
            const tileEl = document.getElementById(`tile-${nextTile}`);
            if (tileEl) {
                tileEl.classList.add('tile-step-glow');
                setTimeout(() => tileEl.classList.remove('tile-step-glow'), 180);
            }

            // Mainkan SFX langkah per petak
            playAudio('step');

            // Getar halus jika didukung
            if (navigator.vibrate) {
                try { navigator.vibrate(15); } catch (e) {}
            }

            stepIndex++;
            setTimeout(step, 170); // 170ms per langkah petak
        }

        function finishWalk() {
            if (pawnEl) pawnEl.classList.remove('pawn-hopping', 'sliding');
            isWalking = false;

            // Efek mendarat di petak akhir
            const endTileEl = document.getElementById(`tile-${finalPos}`);
            if (endTileEl) {
                endTileEl.classList.add('tile-landed-glow');
                setTimeout(() => endTileEl.classList.remove('tile-landed-glow'), 600);
            }
            const boardEl = document.getElementById('board-container');
            if (boardEl) {
                boardEl.classList.add('board-landing-thud');
                setTimeout(() => boardEl.classList.remove('board-landing-thud'), 250);
            }

            renderPawns();
            if (onComplete) onComplete();
        }

        step();
    }

    // ── Aksi Lempar Dadu ─────────────────────────────────────
    async function rollDice() {
        if (!isMyTurn || isRolling || isWalking || gameOver) return;

        isRolling = true;
        const btnRoll = document.getElementById('btn-roll');
        btnRoll.disabled = true;
        btnRoll.classList.remove('can-roll');

        const diceCube = document.getElementById('dice-cube');
        diceCube.classList.add('rolling');

        playAudio('dice');
        if (navigator.vibrate) {
            try { navigator.vibrate([30, 20, 30]); } catch (e) {}
        }

        try {
            const res = await fetch(BASE_URL + 'ular_tangga/aksi_kocok', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'room_code=' + ROOM_CODE
            });
            const data = await res.json();

            // Tunggu animasi kocokan dadu selesai (500ms)
            setTimeout(() => {
                diceCube.classList.remove('rolling');
                isRolling = false;

                if (data.ok) {
                    // Putar dadu ke sisi angka hasil
                    const rot = DICE_ROTATIONS[data.dice] || { x: 0, y: 0 };
                    diceCube.style.transform = `rotateX(${rot.x}deg) rotateY(${rot.y}deg)`;

                    showToast(`🎲 Kamu melempar angka ${data.dice}!`, 'info', 1800);

                    // Jalankan Animasi Melangkah Per Petak (Hopping Walk)
                    const fromPos   = data.from_pos !== undefined ? data.from_pos : (playerPositions[MY_USER_ID] || 1);
                    const targetPos = data.target_pos !== undefined ? data.target_pos : fromPos;
                    const finalPos  = data.final_pos !== undefined ? data.final_pos : targetPos;
                    const action    = data.action_type || 'normal';

                    animateWalk(MY_USER_ID, fromPos, targetPos, action, finalPos, () => {
                        if (data.state) {
                            clientVersion = data.version || (clientVersion + 1);
                            updateGameUI(data.state);
                        } else {
                            pollState();
                        }
                    });

                } else {
                    showToast(data.message || 'Gagal melempar dadu.', 'error');
                    btnRoll.disabled = false;
                    btnRoll.classList.add('can-roll');
                }
            }, 500);

        } catch (e) {
            diceCube.classList.remove('rolling');
            isRolling = false;
            btnRoll.disabled = false;
            btnRoll.classList.add('can-roll');
            showToast('Koneksi bermasalah!', 'error');
        }
    }

    // ── Polling Realtime State ────────────────────────────────
    let pollInterval = null;
    async function pollState() {
        if (gameOver) return;
        try {
            const res = await fetch(BASE_URL + 'ular_tangga/poll/' + ROOM_CODE + '?v=' + clientVersion, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.updated) {
                if (data.version !== undefined && data.version !== null) {
                    clientVersion = parseInt(data.version);
                }

                // Cek apakah ada pemain lain yang bergerak
                let otherPlayerMoved = null;
                if (data.players && !isWalking && !isRolling) {
                    for (const p of data.players) {
                        const oldPos = playerPositions[p.user_id];
                        const newPos = parseInt(p.position || 1);
                        if (oldPos !== undefined && oldPos !== newPos) {
                            otherPlayerMoved = {
                                user_id: p.user_id,
                                from_pos: oldPos,
                                to_pos: newPos,
                                action_type: data.last_action || 'normal'
                            };
                            break;
                        }
                    }
                }

                if (otherPlayerMoved && otherPlayerMoved.user_id !== MY_USER_ID) {
                    animateWalk(
                        otherPlayerMoved.user_id,
                        otherPlayerMoved.from_pos,
                        otherPlayerMoved.to_pos,
                        otherPlayerMoved.action_type,
                        otherPlayerMoved.to_pos,
                        () => updateGameUI(data)
                    );
                } else if (!isWalking) {
                    updateGameUI(data);
                }
            }
        } catch (e) {}

        if (!gameOver) {
            clearTimeout(pollInterval);
            pollInterval = setTimeout(pollState, 1500);
        }
    }

    // ── Update Tampilan UI dari State ────────────────────────
    function updateGameUI(state) {
        isMyTurn = (state.current_turn_user_id === MY_USER_ID && !state.winner_user_id);
        playersData = state.players || [];

        // 1. Update Player Chips
        playersData.forEach(p => {
            playerPositions[p.user_id] = parseInt(p.position || 1);
            const chip = document.getElementById(`chip-${p.user_id}`);
            if (chip) {
                chip.classList.toggle('is-turn', p.is_turn);
                const posEl = document.getElementById(`pos-text-${p.user_id}`);
                if (posEl) posEl.textContent = p.position;
            }
        });

        // 2. Update Turn Banner & Tombol Roll
        const banner = document.getElementById('turn-banner');
        const turnText = document.getElementById('turn-text');
        const btnRoll = document.getElementById('btn-roll');

        if (state.winner_user_id) {
            gameOver = true;
            banner.classList.remove('my-turn');
            turnText.className = 'turn-text';
            turnText.innerHTML = `🏆 Permainan Selesai!`;
            btnRoll.disabled = true;
            btnRoll.classList.remove('can-roll');

            // Tampilkan Modal Pemenang / Kalah yang Jelas
            const isWin = (parseInt(state.winner_user_id) === parseInt(MY_USER_ID));
            const winnerName = state.winner ? state.winner.username : ((state.players || []).find(p => parseInt(p.user_id) === parseInt(state.winner_user_id))?.username || 'Pemain Lain');

            const winnerModal = document.getElementById('modal-winner');
            const iconEl   = document.getElementById('winner-modal-icon');
            const titleEl  = document.getElementById('winner-modal-title');
            const badgeEl  = document.getElementById('winner-modal-badge');
            const subEl    = document.getElementById('winner-modal-sub');
            const boxEl    = document.getElementById('winner-modal-box');

            if (iconEl) iconEl.textContent = isWin ? '🏆' : '💀';
            if (titleEl) {
                titleEl.textContent = isWin ? '🎉 KAMU MENANG!' : '💀 KAMU KALAH!';
                titleEl.style.color = isWin ? '#fef08a' : '#ef4444';
            }
            if (badgeEl) {
                badgeEl.textContent = isWin ? '👑 JUARA 1 ULAR TANGGA' : `👑 Pemenang: ${esc(winnerName)}`;
                badgeEl.style.background = isWin ? 'rgba(245, 158, 11, 0.2)' : 'rgba(239, 68, 68, 0.2)';
                badgeEl.style.color = isWin ? '#fbbf24' : '#fca5a5';
                badgeEl.style.borderColor = isWin ? 'rgba(245, 158, 11, 0.4)' : 'rgba(239, 68, 68, 0.4)';
            }
            if (subEl) {
                subEl.textContent = isWin
                    ? 'Hebat! Kamu berhasil mencapai kotak 100 pertama kali dan menaklukkan seluruh papan! 🥳'
                    : `${esc(winnerName)} telah berhasil mencapai kotak 100 terlebih dahulu. Tetap semangat, balas kekalahanmu di ronde berikutnya! 💪`;
            }
            if (boxEl) {
                boxEl.style.borderColor = isWin ? 'rgba(245, 158, 11, 0.5)' : 'rgba(239, 68, 68, 0.5)';
                boxEl.style.boxShadow = isWin ? '0 0 35px rgba(245, 158, 11, 0.35)' : '0 0 35px rgba(239, 68, 68, 0.3)';
            }

            winnerModal.style.display = 'flex';
            playAudio(isWin ? 'win' : 'lose');

            // Countdown otomatis kembali ke lobby
            let cdSecs = 5;
            const cdTimer = setInterval(() => {
                cdSecs--;
                const cdEl = document.getElementById('winner-countdown');
                if (cdEl) cdEl.textContent = cdSecs;
                if (cdSecs <= 0) {
                    clearInterval(cdTimer);
                    window.location.href = BASE_URL + 'lobby';
                }
            }, 1000);

            return;
        }

        if (isMyTurn && !isWalking) {
            banner.classList.add('my-turn');
            turnText.className = 'turn-text my-turn-text';
            turnText.innerHTML = `✨ Giliran Kamu! Lempar dadumu!`;
            btnRoll.disabled = false;
            btnRoll.classList.add('can-roll');
        } else {
            banner.classList.remove('my-turn');
            turnText.className = 'turn-text';
            const name = state.current_turn_player ? state.current_turn_player.username : 'Pemain';
            turnText.innerHTML = isWalking ? `🚶 Sedang melangkah...` : `⏳ Menunggu ${esc(name)}...`;
            btnRoll.disabled = true;
            btnRoll.classList.remove('can-roll');
        }

        // 3. Update Last Dice
        if (state.last_dice && !isRolling) {
            const diceCube = document.getElementById('dice-cube');
            const rot = DICE_ROTATIONS[state.last_dice] || { x: 0, y: 0 };
            diceCube.style.transform = `rotateX(${rot.x}deg) rotateY(${rot.y}deg)`;
        }

        // 4. Update Log Peek & Modal List
        if (state.logs && state.logs.length > 0) {
            const lastLog = state.logs[state.logs.length - 1];
            document.getElementById('log-peek').textContent = lastLog.message;

            const modalList = document.getElementById('modal-log-list');
            modalList.innerHTML = '';
            [...state.logs].reverse().forEach(l => {
                const item = document.createElement('div');
                item.className = `log-item ${l.action_type}`;
                item.textContent = l.message;
                modalList.appendChild(item);
            });
        }

        // 5. Render Ulang Posisi Pion
        if (!isWalking) {
            renderPawns();
        }
    }

    // ── Synthesized Web Audio Effects ─────────────────────────
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioCtx = null;
    function getAudioCtx() {
        if (!audioCtx && AudioContext) {
            audioCtx = new AudioContext();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function playAudio(type) {
        if (!soundEnabled) return;
        try {
            const ctx = getAudioCtx();
            if (!ctx) return;

            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'step') {
                // Suara ketukan langkah pion per petak (crisp wooden pop)
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(380, now);
                osc.frequency.exponentialRampToValueAtTime(70, now + 0.08);
                gain.gain.setValueAtTime(0.35, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.08);
                osc.start(now);
                osc.stop(now + 0.08);
            } else if (type === 'dice') {
                // Rattle noise
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(140, now);
                osc.frequency.exponentialRampToValueAtTime(40, now + 0.15);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.15);
                osc.start(now);
                osc.stop(now + 0.15);
            } else if (type === 'ladder') {
                // Happy upward chime
                osc.type = 'sine';
                osc.frequency.setValueAtTime(320, now);
                osc.frequency.setValueAtTime(440, now + 0.1);
                osc.frequency.setValueAtTime(660, now + 0.2);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } else if (type === 'snake') {
                // Slide down whistle
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(500, now);
                osc.frequency.exponentialRampToValueAtTime(150, now + 0.35);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } else if (type === 'win') {
                // Fanfare kemenangan
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, now);
                osc.frequency.setValueAtTime(554, now + 0.15);
                osc.frequency.setValueAtTime(659, now + 0.3);
                osc.frequency.setValueAtTime(880, now + 0.45);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.7);
                osc.start(now);
                osc.stop(now + 0.7);
            } else if (type === 'lose') {
                // Melodi kekalahan turun
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(340, now);
                osc.frequency.setValueAtTime(310, now + 0.2);
                osc.frequency.setValueAtTime(280, now + 0.4);
                osc.frequency.setValueAtTime(250, now + 0.6);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.85);
                osc.start(now);
                osc.stop(now + 0.85);
            }
        } catch (e) {}
    }

    function toggleMute() {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('btn-sound');
        btn.textContent = soundEnabled ? '🔊' : '🔇';
        showToast(soundEnabled ? 'Suara Aktif 🔊' : 'Suara Dimatikan 🔇', 'info', 1000);
    }

    // ── Helper Modal & Toast ─────────────────────────────────
    function showModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function showToast(msg, type = 'info', duration = 2000) {
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = 'game-toast';
        t.textContent = msg;
        container.appendChild(t);
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transition = 'opacity 0.25s ease';
            setTimeout(() => t.remove(), 250);
        }, duration);
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Resize Listener ──────────────────────────────────────
    window.addEventListener('resize', () => {
        drawSnakesAndLadders();
        renderPawns();
    });

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
            const res = await fetch('<?= site_url('ular_tangga/aksi_menyerah') ?>', {
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

    // ── Aturan Main Modal Handlers ────────────────────────────
    function openRuleModal() {
        const m = document.getElementById('modal-rule-guide');
        if (m) m.style.display = 'flex';
    }
    function closeRuleModal() {
        const chk = document.getElementById('chk-never-show-ut');
        if (chk && chk.checked) {
            localStorage.setItem('ut_rule_seen', '1');
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

    // ── Start ────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        buildBoard();
        if (!gameOver && !localStorage.getItem('ut_rule_seen')) {
            setTimeout(openRuleModal, 400);
        }
        pollInterval = setTimeout(pollState, 1500);
    });
    </script>
</body>
</html>
