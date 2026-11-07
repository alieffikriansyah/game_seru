<?php
    $room       = $room;
    $state      = $state;
    $user_id    = (int)$user_id;
    $username   = $username;
    $room_code  = $room_code;
    $is_my_turn = (bool)$state['is_my_turn'];
    $winner     = $state['game']['winner_user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="theme-color" content="#0b1120">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Space+Grotesk:wght@700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-page: #0b1120;
            --bg-card: rgba(30, 41, 59, 0.85);
            --border-card: rgba(255, 255, 255, 0.1);
            --color-red: #ef4444;
            --color-red-light: #fee2e2;
            --color-green: #10b981;
            --color-green-light: #d1fae5;
            --color-yellow: #f59e0b;
            --color-yellow-light: #fef3c7;
            --color-blue: #3b82f6;
            --color-blue-light: #dbeafe;
            --safe-star: #eab308;
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: max(16px, env(safe-area-inset-bottom, 0px));
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: radial-gradient(circle at 50% 20%, #1e1b4b 0%, #0f172a 55%, #030712 100%);
            color: #ffffff;
            height: 100dvh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            user-select: none;
            -webkit-user-select: none;
        }

        /* ── HEADER BAR ─────────────────────────────────────────────── */
        .game-header {
            flex-shrink: 0;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(12px, var(--safe-left)) 0 max(12px, var(--safe-right));
            background: rgba(15, 23, 42, 0.94);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            z-index: 50;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }
        .btn-back:active { transform: scale(0.9); }
        .game-title-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.95rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .header-room-code {
            font-family: monospace;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fbbf24;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 1px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-header-tool {
            height: 30px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }
        .btn-header-tool:active { transform: scale(0.92); }
        .btn-surrender {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        /* ── PLAYERS HUD BAR (RESPONSIF MOBILE) ──────────────────────── */
        .players-hud-bar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 6px;
            padding: 6px max(10px, var(--safe-left)) 6px max(10px, var(--safe-right));
            background: rgba(15, 23, 42, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            overflow-x: auto;
        }
        .player-badge-pill {
            flex: 1;
            min-width: 75px;
            max-width: 120px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            background: rgba(30, 41, 59, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            position: relative;
            transition: all 0.25s ease;
        }
        .player-badge-pill.color-red { border-left: 4px solid var(--color-red); }
        .player-badge-pill.color-green { border-left: 4px solid var(--color-green); }
        .player-badge-pill.color-yellow { border-left: 4px solid var(--color-yellow); }
        .player-badge-pill.color-blue { border-left: 4px solid var(--color-blue); }
        .player-badge-pill.is-active-turn {
            background: rgba(59, 130, 246, 0.22);
            border-color: #60a5fa;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
            transform: scale(1.02);
        }
        .player-badge-pill.is-resigned { opacity: 0.45; filter: grayscale(1); }
        .p-avatar-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
            font-weight: 900;
        }
        .p-meta {
            min-width: 0;
            flex: 1;
        }
        .p-name {
            font-size: 0.72rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #f1f5f9;
        }
        .p-score {
            font-size: 0.62rem;
            font-weight: 700;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .p-rank-tag {
            position: absolute;
            top: -5px;
            right: -4px;
            background: #f59e0b;
            color: #000;
            font-size: 0.55rem;
            font-weight: 900;
            padding: 1px 4px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* ── MAIN ARENA VIEWPORT (SCALING ADAPTIVE) ─────────────────── */
        .game-main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            overflow: hidden;
            position: relative;
        }

        /* ── PAPAN LUDO 15x15 GRID ──────────────────────────────────── */
        .ludo-board-wrapper {
            /* Aspek rasio 1:1, menyesuaikan ukuran layar HP secara sempurna */
            width: min(94vw, 92vh - 165px, 460px);
            height: min(94vw, 92vh - 165px, 460px);
            aspect-ratio: 1 / 1;
            background: #ffffff;
            border-radius: 14px;
            padding: 4px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.15);
            display: grid;
            grid-template-columns: repeat(15, 1fr);
            grid-template-rows: repeat(15, 1fr);
            gap: 1.5px;
            position: relative;
            touch-action: manipulation;
        }

        /* Petak Biasa (Track Cell) */
        .ludo-cell {
            background: #f8fafc;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-size: 0.65rem;
            color: #64748b;
            user-select: none;
            transition: background 0.15s ease;
        }
        .ludo-cell.safe-star {
            background: #fef08a !important;
        }
        .ludo-cell.safe-star::after {
            content: '★';
            color: #d97706;
            font-size: 0.95rem;
            font-weight: 900;
            position: absolute;
            pointer-events: none;
        }

        /* Petak Start Warna */
        .ludo-cell.start-red { background: var(--color-red) !important; color: #fff; }
        .ludo-cell.start-green { background: var(--color-green) !important; color: #fff; }
        .ludo-cell.start-yellow { background: var(--color-yellow) !important; color: #fff; }
        .ludo-cell.start-blue { background: var(--color-blue) !important; color: #fff; }
        .ludo-cell.start-cell::after {
            content: '▲';
            font-size: 0.75rem;
            color: #ffffff;
            position: absolute;
            opacity: 0.9;
        }

        /* Petak Jalur Khusus Home Run (5 petak per warna) */
        .ludo-cell.homerun-red { background: #f87171 !important; }
        .ludo-cell.homerun-green { background: #34d399 !important; }
        .ludo-cell.homerun-yellow { background: #fbbf24 !important; }
        .ludo-cell.homerun-blue { background: #60a5fa !important; }

        /* ── 4 KUADRAN MARKAS / KANDANG (6x6) ────────────────────────── */
        .ludo-yard {
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            position: relative;
        }
        .yard-red    { grid-column: 1 / 7; grid-row: 1 / 7; background: var(--color-red); }
        .yard-green  { grid-column: 10 / 16; grid-row: 1 / 7; background: var(--color-green); }
        .yard-yellow { grid-column: 10 / 16; grid-row: 10 / 16; background: var(--color-yellow); }
        .yard-blue   { grid-column: 1 / 7; grid-row: 10 / 16; background: var(--color-blue); }

        .yard-inner-card {
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 6px;
            padding: 8px;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .yard-slot {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: #f1f5f9;
            box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.2);
        }
        .yard-red .yard-slot { border: 2.5px solid var(--color-red); }
        .yard-green .yard-slot { border: 2.5px solid var(--color-green); }
        .yard-yellow .yard-slot { border: 2.5px solid var(--color-yellow); }
        .yard-blue .yard-slot { border: 2.5px solid var(--color-blue); }

        /* ── SEGITIGA AKHIR / FINISH CENTER (3x3) ────────────────────── */
        .ludo-center-finish {
            grid-column: 7 / 10;
            grid-row: 7 / 10;
            position: relative;
            background: #0f172a;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .center-svg-triangles {
            width: 100%;
            height: 100%;
            position: absolute;
            inset: 0;
        }
        .center-finish-trophy {
            position: absolute;
            font-size: 1.25rem;
            z-index: 5;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
            pointer-events: none;
        }

        /* ── PION / BIDAK LUDO (3D TOKEN) ────────────────────────────── */
        .ludo-token {
            width: 78%;
            height: 78%;
            border-radius: 50%;
            position: absolute;
            z-index: 20;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.65rem;
            color: #ffffff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4), inset 0 2px 3px rgba(255, 255, 255, 0.5);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            pointer-events: none;
        }
        .ludo-token.color-red {
            background: radial-gradient(circle at 35% 35%, #f87171, #dc2626 70%, #991b1b);
            border: 2px solid #ffffff;
        }
        .ludo-token.color-green {
            background: radial-gradient(circle at 35% 35%, #34d399, #059669 70%, #065f46);
            border: 2px solid #ffffff;
        }
        .ludo-token.color-yellow {
            background: radial-gradient(circle at 35% 35%, #fbbf24, #d97706 70%, #92400e);
            border: 2px solid #ffffff;
        }
        .ludo-token.color-blue {
            background: radial-gradient(circle at 35% 35%, #60a5fa, #2563eb 70%, #1e40af);
            border: 2px solid #ffffff;
        }

        /* Pion yang dapat diklik / melangkah (Pulsing Glow Animation) */
        .ludo-token.is-movable {
            pointer-events: auto !important;
            cursor: pointer;
            animation: pulseTokenMovable 0.9s infinite alternate;
            z-index: 35 !important;
        }
        @keyframes pulseTokenMovable {
            0% {
                transform: scale(1.02);
                box-shadow: 0 0 6px #ffffff, 0 0 14px rgba(255, 255, 255, 0.9);
            }
            100% {
                transform: scale(1.22);
                box-shadow: 0 0 12px #fbbf24, 0 0 24px #f59e0b;
            }
        }
        .ludo-token.is-movable:active {
            transform: scale(0.9);
        }

        /* Animasi Melangkah Per Petak (Hop Animation) */
        .token-walking-hop {
            transition: transform 0.16s cubic-bezier(0.34, 1.56, 0.64, 1), left 0.16s linear, top 0.16s linear !important;
            transform: scale(1.35) translateY(-5px) !important;
            z-index: 40 !important;
        }

        /* ── CONTROLS & ACTION BAR BAWAH (HP TOUCH READY) ────────────── */
        .game-controls-deck {
            flex-shrink: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.94);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            padding: 8px max(14px, var(--safe-left)) max(10px, var(--safe-bottom)) max(14px, var(--safe-right));
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 50;
        }

        .controls-inner-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .turn-status-banner {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .turn-indicator-title {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .turn-indicator-desc {
            font-size: 0.84rem;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Dadu 3D Box */
        .dice-interactive-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ludo-dice-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ffffff, #f1f5f9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.65rem;
            font-weight: 900;
            color: #0f172a;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4), inset 0 2px 3px rgba(255, 255, 255, 0.8);
            border: 2px solid #cbd5e1;
            transition: transform 0.25s ease;
        }
        .ludo-dice-box.dice-rolling {
            animation: diceShake 0.45s infinite ease-in-out;
        }
        @keyframes diceShake {
            0% { transform: rotate(0deg) scale(1); }
            25% { transform: rotate(-15deg) scale(1.1); }
            50% { transform: rotate(15deg) scale(0.95); }
            75% { transform: rotate(-10deg) scale(1.08); }
            100% { transform: rotate(0deg) scale(1); }
        }

        .btn-action-roll {
            height: 48px;
            padding: 0 18px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.95rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }
        .btn-action-roll:active { transform: scale(0.94); }
        .btn-action-roll:disabled {
            opacity: 0.55;
            filter: grayscale(0.8);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Ticker Log */
        .ludo-ticker-wrap {
            font-size: 0.68rem;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-top: 2px;
        }

        /* ── MODALS ──────────────────────────────────────────────────── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.78);
            backdrop-filter: blur(8px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-card {
            width: min(92vw, 420px);
            max-height: 85vh;
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .modal-header {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(30, 41, 59, 0.5);
        }
        .modal-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.05rem;
            font-weight: 900;
            color: #ffffff;
        }
        .modal-close-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0 4px;
        }
        .modal-body {
            padding: 16px;
            overflow-y: auto;
            color: #cbd5e1;
            font-size: 0.85rem;
            line-height: 1.55;
        }
        .rule-item {
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 10px 12px;
        }
        .rule-item-title {
            font-weight: 800;
            color: #fbbf24;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Toast */
        #toast-container {
            position: fixed;
            top: 52px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 120;
            pointer-events: none;
        }
        .ludo-toast {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid #3b82f6;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 8px 16px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: toastPop 0.25s ease;
        }
        @keyframes toastPop {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- TOP HEADER BAR -->
    <header class="game-header">
        <div class="header-left">
            <a href="<?= site_url('lobby') ?>" class="btn-back" title="Kembali ke Lobby">‹</a>
            <div class="game-title-badge">🎯 LUDO</div>
            <span class="header-room-code"><?= htmlspecialchars($room_code) ?></span>
        </div>
        <div class="header-actions">
            <button class="btn-header-tool" onclick="openRuleModal()" title="Panduan Aturan Main">
                📖 Aturan
            </button>
            <button class="btn-header-tool" id="btn-sound" onclick="toggleMute()" title="Suara">
                🔊
            </button>
            <button class="btn-header-tool btn-surrender" onclick="openSurrenderModal()" title="Menyerah">
                🏳️ Menyerah
            </button>
        </div>
    </header>

    <!-- PLAYERS HUD BAR -->
    <div class="players-hud-bar" id="players-hud-list">
        <!-- Rendered via JavaScript -->
    </div>

    <!-- MAIN ARENA VIEWPORT (SCALED ADAPTIVE 15x15 BOARD) -->
    <main class="game-main-area">
        <div class="ludo-board-wrapper" id="ludo-board">
            <!-- 4 Kuadran Markas (Yard) 6x6 -->
            <div class="ludo-yard yard-red">
                <div class="yard-inner-card">
                    <div class="yard-slot" id="yard-slot-red-0"></div>
                    <div class="yard-slot" id="yard-slot-red-1"></div>
                    <div class="yard-slot" id="yard-slot-red-2"></div>
                    <div class="yard-slot" id="yard-slot-red-3"></div>
                </div>
            </div>
            <div class="ludo-yard yard-green">
                <div class="yard-inner-card">
                    <div class="yard-slot" id="yard-slot-green-0"></div>
                    <div class="yard-slot" id="yard-slot-green-1"></div>
                    <div class="yard-slot" id="yard-slot-green-2"></div>
                    <div class="yard-slot" id="yard-slot-green-3"></div>
                </div>
            </div>
            <div class="ludo-yard yard-yellow">
                <div class="yard-inner-card">
                    <div class="yard-slot" id="yard-slot-yellow-0"></div>
                    <div class="yard-slot" id="yard-slot-yellow-1"></div>
                    <div class="yard-slot" id="yard-slot-yellow-2"></div>
                    <div class="yard-slot" id="yard-slot-yellow-3"></div>
                </div>
            </div>
            <div class="ludo-yard yard-blue">
                <div class="yard-inner-card">
                    <div class="yard-slot" id="yard-slot-blue-0"></div>
                    <div class="yard-slot" id="yard-slot-blue-1"></div>
                    <div class="yard-slot" id="yard-slot-blue-2"></div>
                    <div class="yard-slot" id="yard-slot-blue-3"></div>
                </div>
            </div>

            <!-- Segitiga Kemenangan Tengah (3x3 Finish Center) -->
            <div class="ludo-center-finish">
                <svg class="center-svg-triangles" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <!-- Segitiga Kiri (Merah) -->
                    <polygon points="0,0 50,50 0,100" fill="#ef4444" />
                    <!-- Segitiga Atas (Hijau) -->
                    <polygon points="0,0 100,0 50,50" fill="#10b981" />
                    <!-- Segitiga Kanan (Kuning) -->
                    <polygon points="100,0 100,100 50,50" fill="#f59e0b" />
                    <!-- Segitiga Bawah (Biru) -->
                    <polygon points="0,100 100,100 50,50" fill="#3b82f6" />
                </svg>
                <div class="center-finish-trophy">🏆</div>
            </div>

            <!-- Petak-petak jalur utama 52 sel & home run (Disusun oleh JS) -->
        </div>
    </main>

    <!-- BOTTOM CONTROLS DECK -->
    <footer class="game-controls-deck">
        <div class="controls-inner-row">
            <div class="turn-status-banner">
                <div class="turn-indicator-title" id="banner-turn-title">GILIRAN LAWAN</div>
                <div class="turn-indicator-desc" id="banner-turn-desc">Menunggu pemain melempar dadu...</div>
            </div>
            <div class="dice-interactive-container">
                <div class="ludo-dice-box" id="dice-display-box">
                    <?= !empty($state['game']['dice_value']) ? $state['game']['dice_value'] : '?' ?>
                </div>
                <button class="btn-action-roll" id="btn-roll-dice" onclick="rollDice()">
                    🎲 KOCOK!
                </button>
            </div>
        </div>
        <div class="ludo-ticker-wrap" id="ticker-log-msg">
            • <?= htmlspecialchars($state['game']['last_action_desc'] ?? 'Permainan Ludo dibuka!') ?>
        </div>
    </footer>

    <!-- MODAL: ATURAN PERMAINAN LUDO STANDAR -->
    <div class="modal-overlay" id="modal-rule" style="display:none;" onclick="if(event.target===this)closeRuleModal()">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">📖 Aturan Resmi Permainan Ludo</div>
                <button class="modal-close-btn" onclick="closeRuleModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="rule-item">
                    <div class="rule-item-title">🎲 Keluar dari Markas (Yard)</div>
                    Pion hanya bisa keluar dari Markas ke petak Start jika Anda mendapatkan angka dadu <b>6</b>.
                </div>
                <div class="rule-item">
                    <div class="rule-item-title">🔁 Lemparan Bonus (Roll Again)</div>
                    Anda mendapatkan giliran lempar dadu tambahan jika:
                    <ul style="margin-left:18px; margin-top:4px;">
                        <li>Mendapatkan angka dadu <b>6</b>.</li>
                        <li>Berhasil <b>memakan (*capture*)</b> pion lawan.</li>
                        <li>Pion berhasil masuk ke <b>Segitiga Kemenangan</b>.</li>
                    </ul>
                </div>
                <div class="rule-item">
                    <div class="rule-item-title">⚠️ Batas 3x Angka 6 Berurutan</div>
                    Jika Anda mendapatkan angka 6 sebanyak <b>3 kali berturut-turut</b>, lemparan ke-3 dianggap batal (*foul*) dan giliran langsung beralih ke pemain berikutnya.
                </div>
                <div class="rule-item">
                    <div class="rule-item-title">⚔️ Mekanik Makan Lawan (Capture)</div>
                    Jika pion Anda mendarat tepat di petak yang ditempati pion lawan (non-aman), pion lawan akan <b>ditendang kembali ke Markas</b> asalnya, dan Anda mendapat <b>bonus 1x lemparan dadu</b>!
                </div>
                <div class="rule-item">
                    <div class="rule-item-title">★ Petak Aman (Safe Squares)</div>
                    Pion di <b>Petak Bintang (★)</b> dan <b>Petak Start</b> kebal dari dimakan. Beberapa pion bisa berdampingan dengan damai.
                </div>
                <div class="rule-item">
                    <div class="rule-item-title">🎯 Masuk Finish (Exact Roll)</div>
                    Pion harus masuk ke Segitiga Kemenangan dengan <b>angka dadu pas</b>. Pemain yang pertama kali memasukkan seluruh <b>4 pionnya</b> dinyatakan sebagai <b>Juara 1</b>!
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: KONFIRMASI MENYERAH -->
    <div class="modal-overlay" id="modal-surrender" style="display:none;" onclick="if(event.target===this)closeSurrenderModal()">
        <div class="modal-card" style="width:min(90vw, 360px); text-align:center;">
            <div class="modal-header">
                <div class="modal-title" style="color:#ef4444;">🏳️ Menyerah dari Permainan?</div>
                <button class="modal-close-btn" onclick="closeSurrenderModal()">✕</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyerah dan keluar ke Lobby? Seluruh pion Anda akan ditarik dari papan.</p>
                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button type="button" class="btn-header-tool" style="flex:1; height:40px;" onclick="closeSurrenderModal()">Batal</button>
                    <button type="button" id="btn-confirm-surrender" class="btn-header-tool btn-surrender" style="flex:1; height:40px; font-weight:900;" onclick="confirmSurrender()">Ya, Menyerah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: KEMENANGAN / FINISH -->
    <div class="modal-overlay" id="modal-victory" style="display:none;">
        <div class="modal-card" style="width:min(90vw, 380px); text-align:center; padding:24px 16px;">
            <div id="vic-icon" style="font-size:3.5rem; margin-bottom:8px;">🏆</div>
            <div id="vic-title" style="font-family:'Space Grotesk',sans-serif; font-size:1.4rem; font-weight:900; color:#fbbf24; margin-bottom:4px;">
                PERMAINAN SELESAI!
            </div>
            <div id="vic-winner" style="font-size:1.05rem; font-weight:800; color:#ffffff; margin-bottom:12px;">
                Pemenang: [Nama]
            </div>
            <div id="vic-desc" style="font-size:0.82rem; color:#94a3b8; line-height:1.5; margin-bottom:18px;">
                Selamat kepada sang Juara atas strategi dan kelincahan bidaknya!
            </div>
            <button type="button" class="btn-action-roll" style="width:100%; justify-content:center;" onclick="window.location.href='<?= site_url('lobby') ?>'">
                Kembali ke Lobby 🚀
            </button>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <script>
    const BASE_URL   = '<?= site_url() ?>';
    const ROOM_CODE  = '<?= $room_code ?>';
    const USER_ID    = <?= $user_id ?>;
    let clientVersion = <?= (int)$version ?>;
    let gameState    = <?= json_encode($state, JSON_UNESCAPED_UNICODE) ?>;
    let soundEnabled = true;
    let isWalking    = false;

    // ── DEFINISI KOORDINAT 15x15 GRID ────────────────────────────────
    // Koordinat 52 Petak Jalur Utama (col: 1..15, row: 1..15)
    // Dimulai dari petak Start Merah (index 0) berputar searah jarum jam:
    const TRACK_COORDS = [
        { c: 2,  r: 7  }, // 0  (Start Merah - Safe)
        { c: 3,  r: 7  }, // 1
        { c: 4,  r: 7  }, // 2
        { c: 5,  r: 7  }, // 3
        { c: 6,  r: 7  }, // 4
        { c: 7,  r: 6  }, // 5
        { c: 7,  r: 5  }, // 6
        { c: 7,  r: 4  }, // 7
        { c: 7,  r: 3  }, // 8  (Safe Star ★)
        { c: 7,  r: 2  }, // 9
        { c: 7,  r: 1  }, // 10
        { c: 8,  r: 1  }, // 11
        { c: 9,  r: 1  }, // 12
        { c: 9,  r: 2  }, // 13 (Start Hijau - Safe)
        { c: 9,  r: 3  }, // 14
        { c: 9,  r: 4  }, // 15
        { c: 9,  r: 5  }, // 16
        { c: 9,  r: 6  }, // 17
        { c: 10, r: 7  }, // 18
        { c: 11, r: 7  }, // 19
        { c: 12, r: 7  }, // 20
        { c: 13, r: 7  }, // 21 (Safe Star ★)
        { c: 14, r: 7  }, // 22
        { c: 15, r: 7  }, // 23
        { c: 15, r: 8  }, // 24
        { c: 15, r: 9  }, // 25
        { c: 14, r: 9  }, // 26 (Start Kuning - Safe)
        { c: 13, r: 9  }, // 27
        { c: 12, r: 9  }, // 28
        { c: 11, r: 9  }, // 29
        { c: 10, r: 9  }, // 30
        { c: 9,  r: 10 }, // 31
        { c: 9,  r: 11 }, // 32
        { c: 9,  r: 12 }, // 33
        { c: 9,  r: 13 }, // 34 (Safe Star ★)
        { c: 9,  r: 14 }, // 35
        { c: 9,  r: 15 }, // 36
        { c: 8,  r: 15 }, // 37
        { c: 7,  r: 15 }, // 38
        { c: 7,  r: 14 }, // 39 (Start Biru - Safe)
        { c: 7,  r: 13 }, // 40
        { c: 7,  r: 12 }, // 41
        { c: 7,  r: 11 }, // 42
        { c: 7,  r: 10 }, // 43
        { c: 6,  r: 9  }, // 44
        { c: 5,  r: 9  }, // 45
        { c: 4,  r: 9  }, // 46
        { c: 3,  r: 9  }, // 47 (Safe Star ★)
        { c: 2,  r: 9  }, // 48
        { c: 1,  r: 9  }, // 49
        { c: 1,  r: 8  }, // 50
        { c: 1,  r: 7  }  // 51
    ];

    // Petak Jalur Khusus Home Run per Warna (posisi 100 .. 104)
    const HOMERUN_COORDS = {
        red: [
            { c: 2, r: 8 }, { c: 3, r: 8 }, { c: 4, r: 8 }, { c: 5, r: 8 }, { c: 6, r: 8 }
        ],
        green: [
            { c: 8, r: 2 }, { c: 8, r: 3 }, { c: 8, r: 4 }, { c: 8, r: 5 }, { c: 8, r: 6 }
        ],
        yellow: [
            { c: 14, r: 8 }, { c: 13, r: 8 }, { c: 12, r: 8 }, { c: 11, r: 8 }, { c: 10, r: 8 }
        ],
        blue: [
            { c: 8, r: 14 }, { c: 8, r: 13 }, { c: 8, r: 12 }, { c: 8, r: 11 }, { c: 8, r: 10 }
        ]
    };

    // Segitiga Kemenangan Akhir (posisi 105)
    const FINISH_COORDS = {
        red:    { c: 7, r: 8 },
        green:  { c: 8, r: 7 },
        yellow: { c: 9, r: 8 },
        blue:   { c: 8, r: 9 }
    };

    // ── WEB AUDIO SYNTHESIZER SOUND EFFECTS ──────────────────────────
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioCtx = null;
    function getAudioCtx() {
        if (!audioCtx) audioCtx = new AudioContext();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        return audioCtx;
    }

    function playSound(type) {
        if (!soundEnabled) return;
        try {
            const ctx = getAudioCtx();
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'dice') {
                // Suara guncangan dadu
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(140, now);
                osc.frequency.linearRampToValueAtTime(320, now + 0.15);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.22);
                osc.start(now);
                osc.stop(now + 0.22);
            } else if (type === 'step') {
                // Suara langkah pion hopping (tok... tok...)
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, now);
                osc.frequency.exponentialRampToValueAtTime(780, now + 0.08);
                gain.gain.setValueAtTime(0.35, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.09);
                osc.start(now);
                osc.stop(now + 0.09);
            } else if (type === 'capture') {
                // Suara memakan lawan (boom / knockout!)
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(380, now);
                osc.frequency.linearRampToValueAtTime(120, now + 0.28);
                gain.gain.setValueAtTime(0.4, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } else if (type === 'finish') {
                // Suara pion masuk segitiga kemenangan
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, now);       // C5
                osc.frequency.setValueAtTime(659.25, now + 0.1); // E5
                osc.frequency.setValueAtTime(783.99, now + 0.2); // G5
                osc.frequency.setValueAtTime(1046.5, now + 0.3); // C6
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.linearRampToValueAtTime(0, now + 0.55);
                osc.start(now);
                osc.stop(now + 0.55);
            }
        } catch (e) {}
    }

    function toggleMute() {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('btn-sound');
        btn.textContent = soundEnabled ? '🔊' : '🔇';
        showToast(soundEnabled ? 'Suara Aktif 🔊' : 'Suara Dimatikan 🔇');
    }

    // ── PEMBUATAN ELEMEN PETAK PAPAN ─────────────────────────────────
    function buildBoardCells() {
        const board = document.getElementById('ludo-board');
        const safeStars = [8, 21, 34, 47];

        // 1. Petak Track Utama (0 .. 51)
        TRACK_COORDS.forEach((coord, idx) => {
            const cell = document.createElement('div');
            cell.className = 'ludo-cell';
            cell.id = `cell-track-${idx}`;
            cell.style.gridColumn = coord.c;
            cell.style.gridRow = coord.r;

            if (idx === 0) cell.classList.add('start-red', 'start-cell');
            else if (idx === 13) cell.classList.add('start-green', 'start-cell');
            else if (idx === 26) cell.classList.add('start-yellow', 'start-cell');
            else if (idx === 39) cell.classList.add('start-blue', 'start-cell');
            else if (safeStars.includes(idx)) cell.classList.add('safe-star');

            board.appendChild(cell);
        });

        // 2. Petak Jalur Home Run Khusus Tiap Warna (100 .. 104)
        ['red', 'green', 'yellow', 'blue'].forEach(color => {
            HOMERUN_COORDS[color].forEach((coord, stepIdx) => {
                const cell = document.createElement('div');
                cell.className = `ludo-cell homerun-${color}`;
                cell.id = `cell-homerun-${color}-${100 + stepIdx}`;
                cell.style.gridColumn = coord.c;
                cell.style.gridRow = coord.r;
                board.appendChild(cell);
            });
        });
    }

    // ── RENDER PION / BIDAK DI PAPAN ──────────────────────────────────
    function renderTokens() {
        // Hapus pion lama dari papan
        document.querySelectorAll('.ludo-token').forEach(el => el.remove());

        if (!gameState || !gameState.tokens) return;

        const board = document.getElementById('ludo-board');
        const isMyTurn = gameState.is_my_turn;
        const phase = gameState.game.game_phase;
        const movableIds = gameState.valid_movable_tokens || [];

        // Kelompokkan pion yang berada di petak track yang sama agar tidak menutupi 100%
        const cellOccupants = {};

        gameState.tokens.forEach(t => {
            const tokenEl = document.createElement('div');
            tokenEl.className = `ludo-token color-${t.color}`;
            tokenEl.id = `token-${t.id}`;
            tokenEl.textContent = t.token_index + 1;
            tokenEl.dataset.tokenId = t.id;

            // Jika memenuhi syarat langkah valid di giliran saya
            const canMove = isMyTurn && (phase === 'select_token') && movableIds.includes(parseInt(t.id));
            if (canMove) {
                tokenEl.classList.add('is-movable');
                tokenEl.onclick = () => selectTokenToMove(t.id);
            }

            // Penempatan Posisi Bidak
            if (t.state === 'yard') {
                // Di slot markas masing-masing
                const slot = document.getElementById(`yard-slot-${t.color}-${t.token_index}`);
                if (slot) {
                    slot.appendChild(tokenEl);
                    tokenEl.style.position = 'relative';
                }
            } else if (t.state === 'finished') {
                // Di Segitiga Kemenangan Tengah
                const finishBox = document.querySelector('.ludo-center-finish');
                if (finishBox) {
                    tokenEl.style.position = 'absolute';
                    tokenEl.style.width = '24px';
                    tokenEl.style.height = '24px';
                    tokenEl.style.transform = `scale(0.85) translate(${(t.token_index % 2) * 14 - 7}px, ${Math.floor(t.token_index / 2) * 14 - 7}px)`;
                    finishBox.appendChild(tokenEl);
                }
            } else {
                // Di Track Utama (0..51) atau Home Run (100..104)
                let cellId = '';
                if (t.state === 'track') {
                    cellId = `cell-track-${t.position}`;
                } else if (t.state === 'home_run') {
                    cellId = `cell-homerun-${t.color}-${t.position}`;
                }

                const targetCell = document.getElementById(cellId);
                if (targetCell) {
                    if (!cellOccupants[cellId]) cellOccupants[cellId] = [];
                    cellOccupants[cellId].push(tokenEl);

                    targetCell.appendChild(tokenEl);
                }
            }
        });

        // Tata pion yang bertumpuk di petak yang sama
        Object.keys(cellOccupants).forEach(cId => {
            const list = cellOccupants[cId];
            if (list.length > 1) {
                list.forEach((el, idx) => {
                    el.style.transform = `scale(0.78) translate(${(idx % 2) * 6 - 3}px, ${Math.floor(idx / 2) * 6 - 3}px)`;
                });
            }
        });
    }

    // ── ANIMASI BERJALAN PETAK PER PETAK (HOPPING ANIMATION) ──────────
    async function animateWalk(tokenId, stepsPath, onComplete) {
        if (!stepsPath || stepsPath.length === 0) {
            if (onComplete) onComplete();
            return;
        }

        isWalking = true;
        const tokenEl = document.getElementById(`token-${tokenId}`);
        if (!tokenEl) {
            isWalking = false;
            if (onComplete) onComplete();
            return;
        }

        const tokenData = (gameState.tokens || []).find(t => parseInt(t.id) === parseInt(tokenId));
        const color = tokenData ? tokenData.color : 'red';

        // Pindahkan pion selangkah demi selangkah dengan efek melompat (hop) & suara
        for (let i = 0; i < stepsPath.length; i++) {
            const step = stepsPath[i];
            let targetCell = null;

            if (step.type === 'track') {
                targetCell = document.getElementById(`cell-track-${step.pos}`);
            } else if (step.type === 'home_run') {
                targetCell = document.getElementById(`cell-homerun-${color}-${step.pos}`);
            } else if (step.type === 'finish') {
                targetCell = document.querySelector('.ludo-center-finish');
            }

            if (targetCell) {
                targetCell.appendChild(tokenEl);
                tokenEl.classList.add('token-walking-hop');
                playSound('step');

                await new Promise(r => setTimeout(r, 170));
                tokenEl.classList.remove('token-walking-hop');
            }
        }

        isWalking = false;
        if (onComplete) onComplete();
    }

    // ── UPDATE HUD PLAYERS & STATUS KONTROL ───────────────────────────
    function updateHUD(state) {
        if (!state) return;
        const playersListEl = document.getElementById('players-hud-list');
        const curTurnUserId = parseInt(state.game.current_turn_user_id);
        const isMyTurn = (curTurnUserId === USER_ID);

        // Render Kartu Pemain di Atas
        if (playersListEl && state.players) {
            playersListEl.innerHTML = state.players.map(p => {
                const isCur = (parseInt(p.user_id) === curTurnUserId);
                const isResigned = p.is_resigned;
                const rankText = p.finished_rank > 0 ? `🏅 #${p.finished_rank}` : '';

                return `
                    <div class="player-badge-pill color-${p.color} ${isCur ? 'is-active-turn' : ''} ${isResigned ? 'is-resigned' : ''}">
                        ${rankText ? `<span class="p-rank-tag">${rankText}</span>` : ''}
                        <div class="p-avatar-dot">${p.avatar ? '👤' : p.color[0].toUpperCase()}</div>
                        <div class="p-meta">
                            <div class="p-name">${p.username}</div>
                            <div class="p-score">🚩 ${p.tokens_finished}/4 Finish</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Update Banner Status Giliran
        const bTitle = document.getElementById('banner-turn-title');
        const bDesc  = document.getElementById('banner-turn-desc');
        const btnRoll = document.getElementById('btn-roll-dice');
        const diceBox = document.getElementById('dice-display-box');

        if (bTitle) {
            bTitle.textContent = isMyTurn ? '🔥 GILIRAN ANDA' : '⏳ GILIRAN LAWAN';
            bTitle.style.color = isMyTurn ? '#60a5fa' : '#94a3b8';
        }

        if (bDesc) {
            bDesc.textContent = state.game.last_action_desc || 'Lempar dadu untuk melangkah!';
        }

        if (diceBox && state.game.dice_value) {
            diceBox.textContent = state.game.dice_value;
        }

        // Atur status tombol kocok
        if (btnRoll) {
            if (isMyTurn && state.game.game_phase === 'roll' && !state.game.winner_user_id) {
                btnRoll.disabled = false;
                btnRoll.textContent = '🎲 KOCOK!';
            } else if (isMyTurn && state.game.game_phase === 'select_token') {
                btnRoll.disabled = true;
                btnRoll.textContent = '👆 PILIH PION';
            } else {
                btnRoll.disabled = true;
                btnRoll.textContent = '⏳ MENUNGGU';
            }
        }

        // Ticker Log
        const ticker = document.getElementById('ticker-log-msg');
        if (ticker && state.logs && state.logs.length > 0) {
            ticker.textContent = `• ${state.logs[0].message}`;
        }

        // Tampilkan Modal Pemenang jika Game Selesai
        if (state.game.winner_user_id || state.game.game_phase === 'ended') {
            const vicModal = document.getElementById('modal-victory');
            if (vicModal && vicModal.style.display !== 'flex') {
                const winPlayer = (state.players || []).find(p => parseInt(p.user_id) === parseInt(state.game.winner_user_id));
                const winName = winPlayer ? winPlayer.username : 'Pemenang';
                const isMe = (parseInt(state.game.winner_user_id) === USER_ID);

                document.getElementById('vic-icon').textContent = isMe ? '🏆' : '🏁';
                document.getElementById('vic-title').textContent = isMe ? '🎉 SELAMAT! ANDA MENANG!' : 'PERMAINAN SELESAI!';
                document.getElementById('vic-winner').textContent = `Juara 1: 👑 ${winName}`;
                vicModal.style.display = 'flex';
            }
        }
    }

    // ── API REQUEST HELPER ───────────────────────────────────────────
    async function requestJson(url, options = {}) {
        const defaultHeaders = { 'X-Requested-With': 'XMLHttpRequest' };
        options.headers = Object.assign({}, defaultHeaders, options.headers || {});
        try {
            const res = await fetch(url, options);
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (parseErr) {
                console.error('Non-JSON server response from ' + url + ':', text);
                return { ok: false, message: 'Gagal memproses respon dari server: ' + text.replace(/<[^>]+>/g, ' ').trim().substring(0, 100) };
            }
        } catch (netErr) {
            console.error('Network error requesting ' + url + ':', netErr);
            return { ok: false, message: 'Koneksi ke server terputus.' };
        }
    }

    // ── AKSI KOCOK DADU ──────────────────────────────────────────────
    async function rollDice() {
        const btn = document.getElementById('btn-roll-dice');
        const diceBox = document.getElementById('dice-display-box');
        if (btn) btn.disabled = true;

        playSound('dice');
        if (diceBox) diceBox.classList.add('dice-rolling');

        // Putaran angka acak cepat di dadu
        let rollInterval = setInterval(() => {
            if (diceBox) diceBox.textContent = Math.floor(Math.random() * 6) + 1;
        }, 50);

        try {
            const formData = new FormData();
            formData.append('room_code', ROOM_CODE);

            const data = await requestJson(BASE_URL + 'ludo/aksi_kocok', {
                method: 'POST',
                body: formData
            });

            setTimeout(() => {
                clearInterval(rollInterval);
                if (diceBox) {
                    diceBox.classList.remove('dice-rolling');
                    if (data.dice) diceBox.textContent = data.dice;
                }

                if (data.ok) {
                    if (data.state) {
                        gameState = data.state;
                        clientVersion = data.version || gameState.game.state_version;
                        updateHUD(gameState);
                        renderTokens();
                    }

                    if (data.three_sixes) {
                        showToast('💥 3x Angka 6 berurutan! Lemparan hangus.');
                    } else if (data.no_moves) {
                        showToast(`Dadu ${data.dice}. Tidak ada langkah valid!`);
                    } else if (data.auto_move) {
                        // Jika hanya ada 1 pilihan pion, otomatis bantu arahkan pengguna
                        showToast(`🎲 Dadu ${data.dice}! Sentuh pion Anda untuk melangkah.`);
                    }
                } else {
                    alert(data.message || 'Gagal melempar dadu');
                    if (btn) btn.disabled = false;
                }
            }, 450);

        } catch (e) {
            clearInterval(rollInterval);
            if (diceBox) diceBox.classList.remove('dice-rolling');
            if (btn) btn.disabled = false;
        }
    }

    // ── AKSI JALANKAN BIDAK / PION ────────────────────────────────────
    async function selectTokenToMove(tokenId) {
        if (isWalking) return;

        try {
            const formData = new FormData();
            formData.append('room_code', ROOM_CODE);
            formData.append('token_id', tokenId);

            const data = await requestJson(BASE_URL + 'ludo/aksi_gerak_pion', {
                method: 'POST',
                body: formData
            });

            if (data.ok) {
                // Jalankan animasi langkah bertahap petak per petak!
                animateWalk(data.token_id, data.steps_path, () => {
                    // Animasi pemakanan lawan jika ada
                    if (data.captured_token) {
                        playSound('capture');
                        showToast(`⚔️ Memakan pion ${data.captured_token.opp_name}!`);
                    } else if (data.is_finished) {
                        playSound('finish');
                        showToast('🎯 Pion berhasil masuk Segitiga Kemenangan!');
                    }

                    if (data.bonus_roll) {
                        showToast('🎉 Mendapat bonus 1x lemparan dadu lagi!');
                    }

                    if (data.state) {
                        gameState = data.state;
                        clientVersion = data.version || gameState.game.state_version;
                        updateHUD(gameState);
                        renderTokens();
                    }
                });
            } else {
                alert(data.message || 'Gagal menjalankan pion');
            }
        } catch (e) {
            console.error(e);
        }
    }

    // ── REALTIME POLLING LOOP ─────────────────────────────────────────
    setInterval(async () => {
        if (isWalking) return;
        try {
            const data = await requestJson(`${BASE_URL}ludo/poll/${ROOM_CODE}?v=${clientVersion}`);
            if (data && data.updated) {
                clientVersion = data.version;
                gameState = data;
                updateHUD(gameState);
                renderTokens();
            }
        } catch (e) {}
    }, 1500);

    // ── MODAL & TOAST HANDLERS ────────────────────────────────────────
    function openRuleModal() {
        document.getElementById('modal-rule').style.display = 'flex';
    }
    function closeRuleModal() {
        document.getElementById('modal-rule').style.display = 'none';
    }
    function openSurrenderModal() {
        document.getElementById('modal-surrender').style.display = 'flex';
    }
    function closeSurrenderModal() {
        document.getElementById('modal-surrender').style.display = 'none';
    }

    async function confirmSurrender() {
        const btn = document.getElementById('btn-confirm-surrender');
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Memproses...'; }

        try {
            const formData = new FormData();
            formData.append('room_code', ROOM_CODE);
            const data = await requestJson(BASE_URL + 'ludo/aksi_menyerah', {
                method: 'POST',
                body: formData
            });
            if (data.ok) {
                window.location.href = data.redirect || BASE_URL + 'lobby';
            } else {
                alert(data.message || 'Gagal menyerah.');
                if (btn) { btn.disabled = false; btn.textContent = 'Ya, Menyerah'; }
            }
        } catch(e) {
            window.location.href = BASE_URL + 'lobby';
        }
    }

    function showToast(msg) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'ludo-toast';
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.25s ease';
            setTimeout(() => toast.remove(), 250);
        }, 2200);
    }

    // ── INISIALISASI PERTAMA KALI ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        buildBoardCells();
        updateHUD(gameState);
        renderTokens();
    });
    </script>
</body>
</html>
