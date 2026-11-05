<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="theme-color" content="#0a0a14">
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
            background: var(--bg-dark);
            touch-action: pan-y;
        }
        .uno-arena {
            display: flex;
            flex-direction: column;
            height: 100dvh;
            max-width: 480px;
            margin: 0 auto;
            overflow: hidden;
        }

        /* ── Top: Lawan ─── */
        .opponents-bar {
            flex-shrink: 0;
            padding: 0.625rem 0.875rem;
            background: rgba(10,10,20,0.95);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .opponents-bar::-webkit-scrollbar { display: none; }

        .opponent-chip {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            flex-shrink: 0;
            min-width: 64px;
            padding: 0.5rem;
            border-radius: var(--radius-md);
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            transition: border-color 0.3s;
        }
        .opponent-chip.is-turn { border-color: var(--accent-primary); box-shadow: 0 0 10px var(--accent-glow); }
        .opponent-chip.winner { border-color: #f59e0b; }
        .opp-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--card-bg-2); display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .opp-name { font-size: 0.6rem; font-weight: 600; color: var(--text-muted); max-width: 60px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: center; }
        .opp-cards { font-size: 0.7rem; font-weight: 700; color: var(--text-primary); }
        .opp-uno-badge { font-size: 0.6rem; background: #ef4444; color: #fff; padding: 1px 4px; border-radius: 4px; font-weight: 700; }

        /* ── Middle: Meja ─── */
        .game-table {
            flex: 1;
            min-height: 0; /* Mencegah overflow vertikal yang menutupi tombol kartu */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.5rem;
            position: relative;
            background: radial-gradient(ellipse at center, rgba(139,92,246,0.06) 0%, transparent 70%);
            overflow: hidden;
        }

        /* Giliran & Status */
        .turn-indicator {
            position: absolute;
            top: 0.625rem;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .turn-indicator .turn-name { color: var(--text-primary); }

        /* Direction Arrow */
        .direction-badge {
            position: absolute;
            top: 0.625rem;
            right: 0.875rem;
            font-size: 1.25rem;
            opacity: 0.6;
        }

        /* Kartu meja */
        .table-cards {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        /* Deck & Ambil Kartu di Meja */
        .deck-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.45rem;
            flex-shrink: 0;
            user-select: none;
            -webkit-user-select: none;
        }
        .deck-pile {
            width: 72px;
            height: 102px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 2px solid rgba(167, 139, 250, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            flex-shrink: 0;
            box-shadow: 3px 3px 0 #1e1b4b, 6px 6px 0 rgba(0,0,0,0.4), 0 8px 18px rgba(0,0,0,0.5);
            touch-action: manipulation;
        }
        /* Efek kartu bertumpuk di belakang deck */
        .deck-pile::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: 3px;
            bottom: 3px;
            border-radius: 10px;
            background: #1e1b4b;
            border: 1px solid rgba(255,255,255,0.1);
            z-index: -1;
        }
        .deck-pile::after {
            content: '';
            position: absolute;
            top: -6px;
            left: -6px;
            right: 6px;
            bottom: 6px;
            border-radius: 10px;
            background: #0f172a;
            border: 1px solid rgba(255,255,255,0.06);
            z-index: -2;
        }
        .deck-pile:hover:not(.disabled) {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.5), 4px 4px 0 #1e1b4b;
            border-color: rgba(192, 132, 252, 0.9);
        }
        .deck-pile:active:not(.disabled) {
            transform: translateY(2px) scale(0.96);
        }
        /* Saat giliran pemain aktif (bisa ambil kartu) */
        .deck-pile.can-draw {
            border-color: #818cf8;
            box-shadow: 0 0 16px rgba(129, 140, 248, 0.6), 4px 4px 0 #1e1b4b;
            animation: deckPulse 1.8s infinite ease-in-out;
        }
        @keyframes deckPulse {
            0%, 100% {
                box-shadow: 0 0 12px rgba(129, 140, 248, 0.4), 4px 4px 0 #1e1b4b;
                border-color: rgba(129, 140, 248, 0.6);
            }
            50% {
                box-shadow: 0 0 22px rgba(168, 85, 247, 0.8), 0 0 30px rgba(99, 102, 241, 0.4), 4px 4px 0 #1e1b4b;
                border-color: #c084fc;
            }
        }
        .deck-pile.disabled {
            cursor: not-allowed;
            opacity: 0.55;
            filter: grayscale(0.3);
            animation: none !important;
        }
        .deck-pile-icon {
            font-size: 1.5rem;
            line-height: 1;
            margin-bottom: 2px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }
        .deck-pile-label {
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #e0e7ff;
            text-align: center;
            line-height: 1.15;
            text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        }
        .deck-count-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: var(--radius-full);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        /* Tombol Pill Ambil Kartu di Bawah Tumpukan Meja */
        .deck-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 4px 10px;
            background: rgba(30, 27, 75, 0.9);
            border: 1px solid rgba(167, 139, 250, 0.5);
            border-radius: var(--radius-full);
            color: #c7d2fe;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            white-space: nowrap;
        }
        .deck-action-btn:hover:not(.disabled) {
            background: #4f46e5;
            color: #ffffff;
            border-color: #818cf8;
            transform: scale(1.04);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.55);
        }
        .deck-action-btn:active:not(.disabled) {
            transform: scale(0.96);
        }
        .deck-action-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            border-color: rgba(255,255,255,0.1);
        }
        .deck-action-btn.has-stack {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #ffffff;
            border-color: #fca5a5;
            animation: pulse-glow 1s infinite;
        }

        /* Top card */
        .top-card {
            width: 70px;
            height: 100px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border: 2px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        .top-card.merah   { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .top-card.kuning  { background: linear-gradient(135deg, #ca8a04, #eab308); color: #1a1a00 !important; }
        .top-card.hijau   { background: linear-gradient(135deg, #15803d, #22c55e); }
        .top-card.biru    { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
        .top-card.wild    { background: linear-gradient(135deg, #1a1a2e, #4c1d95); }
        .top-card-value { font-size: 1.75rem; font-weight: 900; line-height: 1; text-shadow: 0 2px 4px rgba(0,0,0,0.4); }
        .top-card-label { font-size: 0.55rem; letter-spacing: 0.5px; opacity: 0.8; }

        /* Active Color Indicator */
        .active-color-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.875rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .color-dot { width: 14px; height: 14px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); }
        .color-dot.merah  { background: #ef4444; }
        .color-dot.kuning { background: #eab308; }
        .color-dot.hijau  { background: #22c55e; }
        .color-dot.biru   { background: #3b82f6; }

        /* Draw Stack Badge */
        .draw-stack-badge {
            display: none;
            padding: 0.375rem 0.875rem;
            background: rgba(239,68,68,0.2);
            border: 1px solid rgba(239,68,68,0.4);
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 700;
            color: #fca5a5;
            animation: pulse-glow 1s infinite;
        }
        .draw-stack-badge.visible { display: flex; align-items: center; gap: 0.4rem; }

        /* ── Bottom: Tangan ─── */
        /* ── Bottom: Tangan ─── */
        .hand-area {
            flex-shrink: 0;
            background: rgba(10,10,20,0.98);
            border-top: 1px solid var(--border-color);
            padding-bottom: max(24px, calc(env(safe-area-inset-bottom, 0px) + 14px));
            position: relative;
            z-index: 30;
        }
        .hand-label {
            font-size: 0.68rem;
            color: var(--text-muted);
            text-align: center;
            padding: 0.3rem 0 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .hand-scroll {
            display: flex;
            gap: 0.35rem;
            overflow-x: auto;
            padding: 0.35rem 0.875rem 0.5rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .hand-scroll::-webkit-scrollbar { display: none; }

        .hand-card {
            width: clamp(52px, 14vw, 64px);
            height: clamp(78px, 20vw, 94px);
            min-width: clamp(52px, 14vw, 64px);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.18);
            flex-shrink: 0;
            scroll-snap-align: center;
            transition: none !important;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            position: relative;
        }
        .hand-card:active { transform: translateY(-12px); }
        .hand-card.selected {
            transform: translateY(-16px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.8), 0 0 18px rgba(255,255,255,0.9);
            border: 2px solid #ffffff;
            z-index: 20;
            transition: none !important;
        }
        .combo-badge {
            display: none;
            position: absolute;
            top: -8px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            color: #0f172a;
            font-weight: 900;
            font-size: 0.72rem;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.6);
            border: 2px solid #8b5cf6;
            animation: none !important;
            z-index: 25;
        }
        .hand-card.selected .combo-badge { display: flex; }
        .hand-card.unplayable { opacity: 0.28; cursor: not-allowed; filter: grayscale(0.2); }
        .hand-card.merah   { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; }
        .hand-card.kuning  { background: linear-gradient(135deg, #ca8a04, #eab308); color: #1a1a00; }
        .hand-card.hijau   { background: linear-gradient(135deg, #15803d, #22c55e); color: #fff; }
        .hand-card.biru    { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; }
        .hand-card.wild    { background: linear-gradient(135deg, #1a1a2e, #4c1d95); color: #fff; border-color: rgba(255,255,255,0.3); }
        .card-val { font-size: 1.35rem; font-weight: 900; line-height: 1; }
        .card-lbl { font-size: 0.5rem; opacity: 0.85; letter-spacing: 0.3px; font-weight: 700; }

        /* Action buttons (Khusus kartu tangan) - SELALU TERLIHAT DI HP */
        .action-bar {
            display: flex;
            gap: 0.5rem;
            padding: 0.35rem 0.875rem max(14px, calc(env(safe-area-inset-bottom, 0px) + 8px));
            align-items: center;
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 4px;
        }
        #btn-play {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 44px;
            height: 44px;
            font-size: 0.85rem;
            font-weight: 800;
            border-radius: var(--radius-md);
            border: 1px dashed rgba(255,255,255,0.18);
            cursor: not-allowed;
            color: var(--text-muted);
            background: rgba(255,255,255,0.05);
            box-shadow: none;
            transition: all 0.15s ease;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }
        #btn-play.active {
            cursor: pointer;
            color: #ffffff;
            background: linear-gradient(135deg, #10b981, #059669);
            border: 1px solid rgba(255,255,255,0.35);
            box-shadow: 0 4px 18px rgba(16,185,129,0.45);
            transform: translateY(-1px);
        }
        #btn-play.active:active {
            transform: translateY(1px);
            box-shadow: 0 2px 8px rgba(16,185,129,0.3);
        }
        #btn-cancel {
            display: none;
            min-height: 44px;
            height: 44px;
            padding: 0 0.875rem;
            border-radius: var(--radius-md);
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.35);
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, transform 0.1s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }
        #btn-cancel:hover {
            background: rgba(239,68,68,0.22);
            border-color: rgba(239,68,68,0.5);
        }
        #btn-cancel:active {
            transform: scale(0.96);
        }
        #btn-uno {
            width: 58px;
            min-height: 44px;
            height: 44px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: 2px solid #fca5a5;
            border-radius: var(--radius-md);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 900;
            cursor: pointer;
            letter-spacing: 1px;
            flex-shrink: 0;
            box-shadow: 0 0 12px rgba(239,68,68,0.4);
            transition: transform 0.1s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }
        #btn-uno:active {
            transform: scale(0.94);
        }

        /* Responsive Media Queries Khusus HP */
        @media (max-height: 720px) {
            .opponents-bar { padding: 0.35rem 0.5rem; gap: 0.4rem; }
            .opp-avatar { width: 30px; height: 30px; font-size: 0.85rem; }
            .opp-name { font-size: 0.55rem; }
            .deck-pile { width: 54px; height: 76px; }
            .table-card { width: 60px; height: 84px; }
            .deck-action-btn { min-height: 28px; font-size: 0.68rem; padding: 0.2rem 0.5rem; }
            .hand-label { padding: 0.2rem 0 0; font-size: 0.6rem; }
            .hand-scroll { padding: 0.25rem 0.75rem 0.45rem; }
            .hand-card { width: 50px; height: 74px; min-width: 50px; }
            .card-val { font-size: 1.25rem; }
            .action-bar { padding: 0.2rem 0.75rem 0.4rem; }
            #btn-play, #btn-uno, #btn-cancel { min-height: 38px; height: 38px; font-size: 0.8rem; }
        }
        @media (max-height: 560px) {
            .opponents-bar { padding: 0.2rem 0.5rem; }
            .opp-avatar { width: 24px; height: 24px; font-size: 0.75rem; }
            .deck-pile { width: 46px; height: 66px; }
            .table-card { width: 50px; height: 70px; }
            .hand-card { width: 44px; height: 64px; min-width: 44px; }
            .card-val { font-size: 1.1rem; }
            #btn-play, #btn-uno, #btn-cancel { min-height: 34px; height: 34px; font-size: 0.75rem; }
        }

        /* ── Modal Aturan Main (Rule Guide) ── */
        .modal-rule-box {
            background: #15152a;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            width: min(92vw, 460px);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
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
        .modal-rule-header h3 { font-size: 1.05rem; font-weight: 800; display: flex; align-items: center; gap: 6px; }
        .rule-tab-nav {
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
            background: rgba(0, 0, 0, 0.25);
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
            color: #ef4444;
            border-bottom-color: #ef4444;
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

        /* Modal Wild Color */
        #modal-wild { display: none; }
        .wild-color-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; margin-top: 1.25rem; }
        .wild-color-btn {
            height: 56px;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 700;
            transition: transform 0.08s ease;
            touch-action: manipulation;
            user-select: none;
            -webkit-user-select: none;
        }
        .wild-color-btn:active { transform: scale(0.92); }
        .wild-color-btn.merah  { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; box-shadow: 0 4px 12px rgba(239,68,68,0.4); }
        .wild-color-btn.kuning { background: linear-gradient(135deg, #ca8a04, #eab308); color: #1a1a00; box-shadow: 0 4px 12px rgba(234,179,8,0.4); }
        .wild-color-btn.hijau  { background: linear-gradient(135deg, #15803d, #22c55e); color: #fff; box-shadow: 0 4px 12px rgba(34,197,94,0.4); }
        .wild-color-btn.biru   { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.4); }

        /* Win/Lose Modal */
        #modal-endgame { display: none; }
        .endgame-content { text-align: center; }
        .endgame-emoji { font-size: 5rem; margin-bottom: 1rem; animation: bounce-in 0.5s ease; }
        .endgame-title { font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; }
        .endgame-sub { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<?php
    $state    = $state;
    $my_hand  = $state['my_hand'];
    $top      = $state['top_card'];
    $is_turn  = $state['is_my_turn'];
    $top_color = $state['active_color'];
    $top_value = $state['active_value'];
    $draw_stack = $state['draw_stack'];

    // Mapping label kartu
    $val_labels = [
        'skip'    => '🚫',
        'reverse' => '🔄',
        'tarik2'  => '+2',
        'wild'    => '🌈',
        'wild4'   => '+4',
    ];
    $val_display = function($v) use ($val_labels) { return isset($val_labels[$v]) ? $val_labels[$v] : $v; };
?>

<div class="uno-arena">

    <!-- ── Top Bar Header UNO ── -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.4rem 0.875rem;background:rgba(10,10,20,0.95);border-bottom:1px solid rgba(255,255,255,0.08);flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <a href="javascript:void(0)" onclick="openSurrenderModal()" style="color:#ffffff;text-decoration:none;font-size:0.9rem;padding:3px 8px;border-radius:50%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);" title="Menyerah / Keluar ke Lobby">←</a>
            <span style="font-weight:900;font-size:0.95rem;color:#ef4444;letter-spacing:0.5px;">🔥 UNO</span>
            <span style="font-size:0.65rem;color:#f59e0b;background:rgba(245,158,11,0.12);padding:1px 6px;border-radius:4px;font-weight:700;"><?= htmlspecialchars($room_code) ?></span>
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;">
            <button type="button" onclick="openRuleModal()" style="background:rgba(139,92,246,0.2);border:1px solid rgba(139,92,246,0.4);color:#c4b5fd;font-weight:700;font-size:0.75rem;padding:4px 10px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:4px;">
                📖 Aturan
            </button>
            <button type="button" onclick="openSurrenderModal()" style="background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;font-weight:700;font-size:0.75rem;padding:4px 10px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:4px;">
                🏳️ Menyerah
            </button>
        </div>
    </div>

    <!-- ── Bagian Atas: Lawan ── -->
    <div class="opponents-bar" id="opponents-bar">
        <?php foreach ($state['opponents'] as $opp): ?>
        <div class="opponent-chip <?= ($opp['user_id'] == $state['current_turn_user_id']) ? 'is-turn' : '' ?>"
             id="opp-<?= $opp['user_id'] ?>">
            <div class="opp-avatar">🎮</div>
            <div class="opp-name"><?= htmlspecialchars($opp['username']) ?></div>
            <div class="opp-cards">🃏 <?= $opp['card_count'] ?></div>
            <?php if ($opp['card_count'] == 1): ?>
            <div class="opp-uno-badge">UNO!</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Bagian Tengah: Meja ── -->
    <div class="game-table" id="game-table" onclick="deselectAllCards()">

        <!-- Giliran -->
        <div class="turn-indicator" id="turn-indicator">
            <?php if ($is_turn): ?>
                <span style="color:#22c55e;font-weight:700;">✨ Giliran Kamu!</span>
            <?php else: ?>
                Giliran: <span class="turn-name" id="turn-name">
                    <?php
                        foreach ($state['seat_map'] as $p) {
                            if ($p['user_id'] == $state['current_turn_user_id']) {
                                echo htmlspecialchars($p['username']); break;
                            }
                        }
                    ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Arah putaran -->
        <div class="direction-badge" id="direction-badge">
            <?= $state['direction'] === 1 ? '↻' : '↺' ?>
        </div>

        <!-- Kartu Meja -->
        <div class="table-cards">
            <!-- Deck / Ambil Kartu di Meja -->
            <div class="deck-wrapper">
                <div class="deck-pile <?= !$is_turn ? 'disabled' : '' ?> <?= $is_turn ? 'can-draw' : '' ?>" id="deck-pile" onclick="drawCard()" title="Klik untuk Ambil Kartu dari Deck">
                    <div style="text-align:center;">
                        <div class="deck-pile-icon">🎴</div>
                        <div class="deck-pile-label">AMBIL<br>KARTU</div>
                    </div>
                    <div class="deck-count-badge" id="deck-count" title="Sisa kartu"><?= $state['deck_count'] ?></div>
                </div>
                <button type="button" class="deck-action-btn <?= !$is_turn ? 'disabled' : '' ?> <?= $draw_stack > 0 ? 'has-stack' : '' ?>" id="deck-action-btn" onclick="drawCard()" title="Ambil Kartu">
                    <?= $draw_stack > 0 ? "⚠️ Ambil +{$draw_stack}" : "🎴 Ambil Kartu" ?>
                </button>
            </div>

            <!-- Kartu Teratas -->
            <div class="top-card <?= $top_color ?>" id="top-card">
                <div class="top-card-value"><?= $val_display($top_value) ?></div>
                <?php if (!is_numeric($top_value) && !in_array($top_value, ['wild','wild4'])): ?>
                <div class="top-card-label"><?= strtoupper($top_color) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Warna Aktif & Draw Stack -->
        <div style="display:flex;flex-direction:column;align-items:center;gap:0.625rem;">
            <div class="active-color-bar" id="active-color-bar">
                <div class="color-dot <?= $top_color ?>" id="active-color-dot"></div>
                <span id="active-color-label"><?= ucfirst($top_color) ?></span>
            </div>
            <div class="draw-stack-badge <?= $draw_stack > 0 ? 'visible' : '' ?>" id="draw-stack-badge">
                ⚠️ Tumpukan Tarik: +<?= $draw_stack ?>
            </div>
        </div>

    </div>

    <!-- ── Bagian Bawah: Tangan ── -->
    <div class="hand-area">
        <div class="hand-label">KARTU KAMU (<?= count($my_hand) ?>)</div>

        <div class="hand-scroll" id="hand-scroll">
            <?php
                // Cari value apa saja yang cocok langsung di meja saat ini
                $playable_vals = [];
                foreach ($my_hand as $c) {
                    $direct = ($c['card_color'] === $top_color || $c['card_value'] === $top_value || $c['card_color'] === 'wild');
                    if ($draw_stack > 0) {
                        if ($top_value === 'wild4') {
                            $direct = ($c['card_value'] === 'wild4');
                        } else {
                            $direct = in_array($c['card_value'], ['tarik2', 'wild4']);
                        }
                    }
                    if ($direct && $c['card_color'] !== 'wild') {
                        $playable_vals[$c['card_value']] = true;
                    }
                }
            ?>
            <?php foreach ($my_hand as $card): ?>
            <?php
                $playable = $is_turn && $state['winner_user_id'] === null;
                // Kartu bisa dimainkan jika cocok langsung atau memiliki nomor/pola yang sama dengan kartu cocok lain
                $can_play = (
                    $card['card_color'] === $top_color ||
                    $card['card_value'] === $top_value ||
                    $card['card_color'] === 'wild' ||
                    isset($playable_vals[$card['card_value']])
                );
                // Jika ada draw_stack:
                // Jika top_value adalah wild4 (+4), kartu +2 TIDAK BISA dikeluarkan! Hanya +4 yang bisa!
                // Jika top_value adalah tarik2 (+2), BISA dilawan dengan +2 ATAU +4!
                if ($draw_stack > 0) {
                    if ($top_value === 'wild4') {
                        $can_play = ($card['card_value'] === 'wild4');
                    } else {
                        $can_play = in_array($card['card_value'], ['tarik2', 'wild4']);
                    }
                }
            ?>
            <div class="hand-card <?= $card['card_color'] ?> <?= (!$can_play && $playable) ? 'unplayable' : '' ?>"
                 data-id="<?= $card['id'] ?>"
                 data-color="<?= $card['card_color'] ?>"
                 data-value="<?= $card['card_value'] ?>"
                 data-playable="<?= $can_play ? '1' : '0' ?>"
                 onclick="<?= $playable ? 'event.stopPropagation(); selectCard(this)' : '' ?>"
                 title="<?= htmlspecialchars($card['card_color'] . ' ' . $card['card_value']) ?>">
                <div class="card-val"><?= $val_display($card['card_value']) ?></div>
                <?php if (is_numeric($card['card_value'])): ?>
                <div class="card-lbl"><?= strtoupper($card['card_color']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-bar">
            <button id="btn-play" class="btn" onclick="playCards()" disabled>
                <?= $is_turn ? '▶ Pilih Kartu untuk Dimainkan' : '⏳ Menunggu Giliran Lawan' ?>
            </button>
            <button id="btn-cancel" class="btn" onclick="deselectAllCards()" style="display:none;" title="Batal Pilih">
                ✕ Batal
            </button>
            <button id="btn-uno" onclick="shoutUno()" title="UNO!">UNO!</button>
        </div>
    </div>
</div>

<!-- Modal: Pilih Warna Wild -->
<div class="modal-overlay" id="modal-wild">
    <div class="modal-box">
        <h3 style="text-align:center;">🌈 Pilih Warna</h3>
        <p style="text-align:center;font-size:0.875rem;color:var(--text-muted);">Kartu Wild dimainkan! Pilih warna aktif:</p>
        <div class="wild-color-grid">
            <button class="wild-color-btn merah"  onclick="confirmWild('merah')">🔴 Merah</button>
            <button class="wild-color-btn kuning" onclick="confirmWild('kuning')">🟡 Kuning</button>
            <button class="wild-color-btn hijau"  onclick="confirmWild('hijau')">🟢 Hijau</button>
            <button class="wild-color-btn biru"   onclick="confirmWild('biru')">🔵 Biru</button>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi Menyerah -->
<div class="modal-overlay" id="modal-surrender" style="display:none;" onclick="if(event.target===this)closeSurrenderModal()">
    <div class="modal-box" style="text-align:center;padding:1.5rem;max-width:380px;">
        <div style="font-size:3rem;margin-bottom:0.5rem;">🏳️</div>
        <div style="font-size:1.2rem;font-weight:900;color:#ef4444;margin-bottom:0.5rem;">MENYERAH DARI PERMAINAN?</div>
        <div style="font-size:0.8rem;color:#cbd5e1;line-height:1.45;margin-bottom:1.25rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:10px;text-align:left;">
            <p style="margin-bottom:6px;font-weight:700;color:#fca5a5;">⚠️ Konfirmasi keluar:</p>
            <ul style="padding-left:16px;margin:0;display:flex;flex-direction:column;gap:4px;">
                <li>Kamu akan keluar dari permainan dan kembali ke Lobby.</li>
                <li>Kartu di tanganmu akan dikembalikan ke tumpukan deck.</li>
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

<!-- Modal: Akhir Game -->
<div class="modal-overlay" id="modal-endgame">
    <div class="modal-box">
        <div class="endgame-content" id="endgame-content"></div>
    </div>
</div>

<!-- Modal: Panduan Aturan Main UNO -->
<div class="modal-overlay" id="modal-rule-guide" style="display:none;" onclick="if(event.target===this)closeRuleModal()">
    <div class="modal-rule-box">
        <div class="modal-rule-header">
            <h3>📖 Aturan Main UNO Seru</h3>
            <button type="button" class="btn" style="background:none;border:none;color:#94a3b8;font-size:1.2rem;cursor:pointer;" onclick="closeRuleModal()">✕</button>
        </div>

        <div class="rule-tab-nav">
            <button type="button" class="rule-tab-btn active" onclick="switchRuleTab('tab-uno-tujuan', this)">🎯 Tujuan</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-uno-cocok', this)">🃏 Multi-Drop</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-uno-spesial', this)">⚡ Kartu Aksi</button>
            <button type="button" class="rule-tab-btn" onclick="switchRuleTab('tab-uno-menang', this)">🏆 Menang & Tips</button>
        </div>

        <div class="rule-tab-body">
            <!-- TAB 1: TUJUAN -->
            <div id="tab-uno-tujuan" class="rule-tab-pane">
                <h4>🎯 Tujuan Permainan</h4>
                <p>Tujuan utama UNO adalah menjadi pemain pertama yang <b>menghabiskan seluruh kartu</b> di tangan sebelum lawan-lawanmu!</p>
                <br>
                <ul>
                    <li><b>Pembagian Kartu:</b> Setiap pemain mendapatkan 7 kartu acak saat permainan dimulai.</li>
                    <li><b>Kartu Meja Awal:</b> Satu kartu dibuka dari tumpukan deck sebagai kartu meja pertama.</li>
                    <li><b>Giliran:</b> Permainan berjalan memutar sesuai arah putaran (searah jarum jam secara default).</li>
                </ul>
            </div>

            <!-- TAB 2: PENCOCOKAN & MULTI-DROP -->
            <div id="tab-uno-cocok" class="rule-tab-pane" style="display:none;">
                <h4>🃏 Cara Main & Fitur Multi-Drop</h4>
                <ul>
                    <li><b>Kecocokan Kartu:</b> Pada giliranmu, kamu dapat memainkan kartu yang cocok dengan kartu teratas meja berdasarkan:
                        <ul>
                            <li>Warna yang sama (Merah, Kuning, Hijau, Biru)</li>
                            <li>Angka/Simbol yang sama (misal 5 dengan 5, atau Skip dengan Skip)</li>
                            <li>Kartu Wild (bisa dimainkan di atas kartu apa saja)</li>
                        </ul>
                    </li>
                    <li><b>🔥 Fitur Multi-Drop (Banting Beruntun):</b>
                        Jika kamu memiliki beberapa kartu dengan <b>angka atau simbol yang sama</b> di tangan (contoh: kamu punya angka 7 Merah, 7 Biru, dan 7 Hijau), kamu dapat memilih dan mengeluarkannya <b>sekaligus dalam 1 giliran</b>!
                    </li>
                    <li><b>Ambil Kartu:</b> Jika kamu tidak memiliki kartu yang cocok, klik tumpukan <b>Deck</b> untuk mengambil kartu baru.</li>
                </ul>
            </div>

            <!-- TAB 3: KARTU AKSI SPESIAL -->
            <div id="tab-uno-spesial" class="rule-tab-pane" style="display:none;">
                <h4>⚡ Kartu Aksi Spesial & Efeknya</h4>
                <ul>
                    <li>🚫 <b>Skip:</b> Melewatkan giliran pemain berikutnya. Jika multi-drop 2 Skip, maka 2 pemain dilewati!</li>
                    <li>🔄 <b>Reverse:</b> Membalikkan arah putaran giliran (searah jarum jam $\leftrightarrow$ berlawanan jarum jam). Jika 2 pemain, efeknya sama dengan Skip!</li>
                    <li>➕2 <b>Tarik 2 (+2):</b> Pemain berikutnya wajib mengambil 2 kartu dari deck dan kehilangan gilirannya.</li>
                    <li>🌈 <b>Wild:</b> Dapat dimainkan kapan saja. Pemain memilih warna aktif berikutnya.</li>
                    <li>🌈+4 <b>Wild Draw 4 (+4):</b> Pemain berikutnya wajib mengambil 4 kartu dan kamu bebas memilih warna aktif baru.</li>
                    <li>🛡️ <b>Penumpukan Kartu (+Stacking):</b> Jika pemain sebelumnya mengeluarkan +2, kamu bisa melawannya dengan mengeluarkan +2 atau +4 milikmu, sehingga total tarikan kartu terakumulasi ke pemain setelahmu! (Kartu +4 hanya bisa dilawan dengan +4).</li>
                </ul>
            </div>

            <!-- TAB 4: MENANG & TIPS -->
            <div id="tab-uno-menang" class="rule-tab-pane" style="display:none;">
                <h4>🏆 Aturan Teriak UNO & Menyerah</h4>
                <ul>
                    <li>📢 <b>Teriak UNO!:</b> Ketika kartu di tanganmu tinggal <b>1 kartu lagi</b>, segera tekan tombol <b>UNO!</b> sebelum pemain lain menegurmu!</li>
                    <li>🏆 <b>Kemenangan:</b> Pemain yang kartu di tangannya menjadi 0 dinyatakan sebagai <b>Juara 1 UNO</b> dan game selesai.</li>
                    <li>🏳️ <b>Fitur Menyerah:</b> Jika kamu perlu keluar dari meja, gunakan tombol <b>Menyerah</b>:
                        <ul>
                            <li><b>Main 2 orang:</b> Lawan langsung otomatis menang.</li>
                            <li><b>Main 3+ orang:</b> Kartumu dikembalikan ke deck dan pemain lain melanjutkan permainan.</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="modal-rule-footer">
            <label class="chk-rule-label">
                <input type="checkbox" id="chk-never-show-uno"> Jangan tampilkan otomatis lagi
            </label>
            <button type="button" class="btn btn-primary" style="padding:8px 16px; font-weight:800; border-radius:8px;" onclick="closeRuleModal()">
                🚀 Mengerti, Gas Main!
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
const BASE_URL   = '<?= site_url() ?>';
const ROOM_CODE  = '<?= $room_code ?>';
const MY_USER_ID = <?= (int)$user_id ?>;
const MY_USERNAME = '<?= addslashes($username) ?>';

let clientVersion  = <?= (int)($version ?? $room->state_version) ?>;
let selectedCards  = []; // [{id, color, value, el}]
let pendingWild    = false;
let polling        = true;
let isMyTurn       = <?= $is_turn ? 'true' : 'false' ?>;
let drawStack      = <?= (int)$draw_stack ?>;
let gameOver       = <?= $state['winner_user_id'] ? 'true' : 'false' ?>;
let currentActiveColor = '<?= addslashes($top_color) ?>';
let currentActiveValue = '<?= addslashes($top_value) ?>';

const VAL_LABELS = { skip:'🚫', reverse:'🔄', tarik2:'+2', wild:'🌈', wild4:'+4' };
function valDisplay(v) { return VAL_LABELS[v] || v; }

function checkCardPlayable(color, value) {
    if (drawStack > 0) {
        if (currentActiveValue === 'wild4') {
            return (value === 'wild4');
        } else {
            return (value === 'tarik2' || value === 'wild4');
        }
    }
    if (color === 'wild') return true;
    if (color === currentActiveColor || value === currentActiveValue) return true;

    // Cek apakah ada kartu lain di tangan yang nilainya sama dan cocok meja
    const allHand = Array.from(document.querySelectorAll('#hand-scroll .hand-card'));
    return allHand.some(c => 
        c.dataset.value === value && 
        (c.dataset.color === currentActiveColor || c.dataset.value === currentActiveValue)
    );
}

// ── Pilih / Angkat Kartu (Kontrol Penuh Pemain) ─────────────────
function selectCard(el) {
    if (!isMyTurn || gameOver) return;
    const id    = parseInt(el.dataset.id);
    const color = el.dataset.color;
    const value = el.dataset.value;

    if (navigator.vibrate) {
        try { navigator.vibrate(20); } catch (e) {}
    }

    // 1. JIKA KARTU SUDAH TERANGKAT:
    // User mengetuk kartu ini lagi -> BATALKAN PILIHAN KARTU INI (TURUNKAN KEMBALI)
    const existingIndex = selectedCards.findIndex(c => c.id === id);
    if (existingIndex !== -1) {
        el.classList.remove('selected');
        const badge = el.querySelector('.combo-badge');
        if (badge) badge.remove();
        selectedCards.splice(existingIndex, 1);
        updatePlayBtn();
        return;
    }

    // 2. ATURAN COUNTER SERANGAN (+4 VS +2):
    if (drawStack > 0) {
        if (currentActiveValue === 'wild4' && value !== 'wild4') {
            showToast('⛔ Kartu +4 tidak bisa dilawan dengan +2! Lawan dengan +4 atau ambil kartu.', 'error');
            return;
        }
        if (currentActiveValue !== 'wild4' && value !== 'tarik2' && value !== 'wild4') {
            showToast(`⚠️ Ada serangan tarik +${drawStack}! Harus lawan dengan +2 atau +4, atau ambil kartu.`, 'error');
            return;
        }
    }

    // 3. JIKA SUDAH ADA KARTU LAIN YANG TERANGKAT:
    if (selectedCards.length > 0) {
        const first = selectedCards[0];
        const isWild = (color === 'wild' || first.color === 'wild');
        const sameVal = (value === first.value);

        if (!isWild && sameVal) {
            // Nomor atau pola aksi SAMA (+2, skip, reverse, atau angka sama beda warna)!
            // Kartu ini IKUT TERANGKAT sesuai pilihan user!
            el.classList.add('selected');
            selectedCards.push({ id, color, value, el });
            updatePlayBtn();
            return;
        } else {
            // Berbeda nomor/pola:
            // Jika kartu baru ini bisa dimainkan di meja, ganti pilihan ke kartu baru ini
            if (checkCardPlayable(color, value)) {
                deselectAllCards();
                el.classList.add('selected');
                selectedCards.push({ id, color, value, el });
                updatePlayBtn();
                return;
            } else {
                showToast('⚠️ Hanya kartu dengan nomor atau pola yang sama yang bisa dikeluarkan barengan!', 'error');
                return;
            }
        }
    }

    // 4. KARTU PERTAMA YANG DIPILIH:
    if (!checkCardPlayable(color, value)) {
        showToast('⚠️ Kartu ini tidak cocok dengan warna atau angka di meja!', 'error');
        return;
    }

    // Angkat kartu ini (pilih)
    el.classList.add('selected');
    selectedCards.push({ id, color, value, el });
    updatePlayBtn();
}

function deselectAllCards() {
    selectedCards.forEach(c => {
        if (c.el) {
            c.el.classList.remove('selected');
            const badge = c.el.querySelector('.combo-badge');
            if (badge) badge.remove();
        }
    });
    selectedCards = [];
    updatePlayBtn();
}

function updatePlayBtn() {
    const btnPlay   = document.getElementById('btn-play');
    const btnCancel = document.getElementById('btn-cancel');

    // Update nomor urutan combo badge di tiap kartu terpilih
    selectedCards.forEach((c, index) => {
        let badge = c.el.querySelector('.combo-badge');
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'combo-badge';
            c.el.appendChild(badge);
        }
        badge.textContent = (selectedCards.length > 1) ? (index + 1) : '✓';
    });

    if (!btnPlay) return;

    if (selectedCards.length > 0 && isMyTurn) {
        btnPlay.disabled = false;
        btnPlay.classList.add('active');
        if (btnCancel) btnCancel.style.display = 'inline-flex';

        if (selectedCards.length > 1) {
            const sampleVal = valDisplay(selectedCards[0].value);
            btnPlay.textContent = `▶ Keluarkan ${selectedCards.length} Kartu (${sampleVal})`;
        } else {
            btnPlay.textContent = `▶ Keluarkan Kartu`;
        }
    } else {
        btnPlay.disabled = true;
        btnPlay.classList.remove('active');
        if (btnCancel) btnCancel.style.display = 'none';

        if (!isMyTurn) {
            btnPlay.textContent = `⏳ Menunggu Giliran Lawan`;
        } else {
            btnPlay.textContent = `▶ Pilih Kartu untuk Dimainkan`;
        }
    }
}

// ── Mainkan Kartu (Hanya Keluar Saat Tombol Ditekan) ───────────
function playCards() {
    if (selectedCards.length === 0 || !isMyTurn) return;

    // Cek kartu Wild
    const hasWild = selectedCards.some(c => c.color === 'wild');
    if (hasWild) {
        pendingWild = true;
        document.getElementById('modal-wild').style.display = 'flex';
        return;
    }

    // Multi-drop urutan: kartu yang cocok meja ditaruh di urutan awal [0]
    let ordered = [...selectedCards];
    if (ordered.length > 1) {
        const matchIdx = ordered.findIndex(c => c.color === currentActiveColor || c.value === currentActiveValue);
        if (matchIdx > 0) {
            const matchCard = ordered.splice(matchIdx, 1)[0];
            ordered.unshift(matchCard);
        }
    }

    sendPlayCards(null, ordered);
}

function confirmWild(color) {
    if (navigator.vibrate) {
        try { navigator.vibrate(20); } catch (e) {}
    }
    document.getElementById('modal-wild').style.display = 'none';
    pendingWild = false;
    showToast(`🌈 Memilih warna ${ucfirst(color)}...`, 'info', 1000);
    sendPlayCards(color, selectedCards);
}

async function sendPlayCards(wildColor, customCards = null) {
    isMyTurn = false;
    const cardsToPlay = customCards || selectedCards;
    const cardIds = cardsToPlay.map(c => c.id);
    deselectAllCards();

    // Update status tombol play dan cancel seketika
    const btnPlay = document.getElementById('btn-play');
    const btnCancel = document.getElementById('btn-cancel');
    if (btnPlay) {
        btnPlay.disabled = true;
        btnPlay.classList.remove('active');
        btnPlay.textContent = '⏳ Mengeluarkan Kartu...';
    }
    if (btnCancel) btnCancel.style.display = 'none';

    const body = new URLSearchParams({
        room_code: ROOM_CODE,
        card_ids: JSON.stringify(cardIds),
    });
    if (wildColor) body.append('wild_color', wildColor);

    try {
        const res = await fetch(BASE_URL + 'uno/aksi_mainkan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body,
        });
        const data = await res.json();
        if (!data.ok) {
            showToast(data.message || 'Gagal memainkan kartu.', 'error');
            isMyTurn = true;
            pollState();
        } else {
            // RESPON INSTAN: Langsung perbarui UI dan kartu meja tanpa menunggu polling!
            if (data.state) {
                clientVersion = data.version || (clientVersion + 1);
                updateGameUI(data.state);
            } else {
                pollState();
            }

            if (data.winner_id) {
                const isWin = (parseInt(data.winner_id) === parseInt(MY_USER_ID));
                const winnerName = isWin ? MY_USERNAME : (data.winner_name || (data.state ? data.state.winner_username : ''));
                showEndGame(isWin, winnerName);
            }
        }
    } catch (e) {
        showToast('Koneksi bermasalah!', 'error');
        isMyTurn = true;
    }
}

// ── Ambil Kartu ───────────────────────────────────────────────
async function drawCard() {
    if (!isMyTurn || gameOver) return;
    isMyTurn = false;
    const deckEl = document.getElementById('deck-pile');
    const deckBtn = document.getElementById('deck-action-btn');
    if (deckEl) {
        deckEl.classList.add('disabled');
        deckEl.classList.remove('can-draw');
    }
    if (deckBtn) deckBtn.classList.add('disabled');
    updatePlayBtn();

    try {
        const res = await fetch(BASE_URL + 'uno/aksi_ambil', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'room_code=' + ROOM_CODE,
        });
        const data = await res.json();
        if (data.ok) {
            const cnt = data.draw_count || 1;
            showToast(`🎴 Mengambil ${cnt} kartu...`, 'info', 1500);
            // RESPON INSTAN: Langsung munculkan kartu baru di tangan tanpa jeda polling!
            if (data.state) {
                clientVersion = data.version || (clientVersion + 1);
                updateGameUI(data.state);
            } else {
                pollState();
            }
        } else {
            showToast(data.message || 'Gagal ambil kartu.', 'error');
            isMyTurn = true;
            if (deckEl) {
                deckEl.classList.remove('disabled');
                deckEl.classList.add('can-draw');
            }
            if (deckBtn) deckBtn.classList.remove('disabled');
            updatePlayBtn();
        }
    } catch (e) {
        showToast('Koneksi bermasalah!', 'error');
        isMyTurn = true;
        if (deckEl) {
            deckEl.classList.remove('disabled');
            deckEl.classList.add('can-draw');
        }
        if (deckBtn) deckBtn.classList.remove('disabled');
        updatePlayBtn();
    }
}

// ── Teriak UNO ────────────────────────────────────────────────
async function shoutUno() {
    showToast('🃏 UNO! UNO! UNO!', 'success', 2000);
    await fetch(BASE_URL + 'uno/aksi_uno', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'room_code=' + ROOM_CODE,
    }).catch(() => {});
}

// ── Polling State ─────────────────────────────────────────────
async function pollState() {
    if (!polling || gameOver) return;
    try {
        const res = await fetch(BASE_URL + 'uno/poll/' + ROOM_CODE + '?v=' + clientVersion, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.updated) {
            if (data.version !== undefined && data.version !== null) {
                clientVersion = parseInt(data.version);
            }
            updateGameUI(data);
        }
    } catch (e) { /* silent */ }

    if (polling && !gameOver) setTimeout(pollState, 1500);
}

function updateGameUI(state) {
    isMyTurn   = (state.my_user_id === state.current_turn_user_id);
    drawStack  = state.draw_stack || 0;
    currentActiveColor = state.active_color;
    currentActiveValue = state.active_value;

    // Update giliran
    const turnEl = document.getElementById('turn-indicator');
    if (isMyTurn) {
        turnEl.innerHTML = '<span style="color:#22c55e;font-weight:700;">✨ Giliran Kamu!</span>';
    } else {
        const player = (state.seat_map || []).find(p => p.user_id == state.current_turn_user_id);
        const name   = player ? player.username : '...';
        turnEl.innerHTML = `Giliran: <span class="turn-name">${esc(name)}</span>`;
    }

    // Update arah
    document.getElementById('direction-badge').textContent = state.direction === 1 ? '↻' : '↺';

    // Update kartu teratas
    if (state.top_card) {
        const top = document.getElementById('top-card');
        top.className = 'top-card ' + state.active_color;
        top.innerHTML = `<div class="top-card-value">${valDisplay(state.active_value)}</div>`;
        if (!isNaN(parseInt(state.active_value))) {
            top.innerHTML += `<div class="top-card-label">${state.active_color.toUpperCase()}</div>`;
        }
    }

    // Update warna aktif
    document.getElementById('active-color-dot').className = 'color-dot ' + state.active_color;
    document.getElementById('active-color-label').textContent = ucfirst(state.active_color);

    // Update deck count
    document.getElementById('deck-count').textContent = state.deck_count;

    // Update draw_stack badge
    const dsb = document.getElementById('draw-stack-badge');
    if (state.draw_stack > 0) {
        dsb.classList.add('visible');
        dsb.innerHTML = `⚠️ Tumpukan Tarik: +${state.draw_stack}`;
    } else {
        dsb.classList.remove('visible');
    }

    // Update deck & tombol ambil kartu di meja
    const deckPile = document.getElementById('deck-pile');
    if (deckPile) {
        deckPile.classList.toggle('disabled', !isMyTurn);
        deckPile.classList.toggle('can-draw', isMyTurn && !gameOver);
    }
    const deckBtn = document.getElementById('deck-action-btn');
    if (deckBtn) {
        deckBtn.classList.toggle('disabled', !isMyTurn);
        if (state.draw_stack > 0) {
            deckBtn.innerHTML = `⚠️ Ambil +${state.draw_stack}`;
            deckBtn.classList.add('has-stack');
        } else {
            deckBtn.innerHTML = `🎴 Ambil Kartu`;
            deckBtn.classList.remove('has-stack');
        }
    }

    // Update kartu di tangan (re-render)
    if (state.my_hand) {
        renderHand(state.my_hand, state.active_color, state.active_value, state.draw_stack);
    }

    // Update lawan
    if (state.opponents) {
        updateOpponents(state.opponents, state.current_turn_user_id);
    }

    // Cek winner
    if (state.winner_user_id) {
        gameOver = true;
        polling  = false;
        const isWin = (parseInt(state.winner_user_id) === parseInt(MY_USER_ID));
        let winnerName = state.winner_username;
        if (!winnerName && state.seat_map) {
            const p = state.seat_map.find(s => parseInt(s.user_id) === parseInt(state.winner_user_id));
            if (p) winnerName = p.username;
        }
        showEndGame(isWin, winnerName);
    }
}

function renderHand(hand, activeColor, activeValue, ds) {
    const scroll = document.getElementById('hand-scroll');

    // Simpan kartu yang sebelumnya sedang dipilih oleh pemain agar tidak ter-reset/ter-cancel
    const prevSelectedIds = new Set(selectedCards.map(c => parseInt(c.id)));
    const newSelectedCards = [];

    scroll.innerHTML = '';

    const label = document.querySelector('.hand-label');
    if (label) label.textContent = `KARTU KAMU (${hand.length})`;

    // Cari value apa saja yang langsung cocok di meja saat ini
    const playableValues = new Set();
    hand.forEach(c => {
        let direct = (c.card_color === activeColor || c.card_value === activeValue || c.card_color === 'wild');
        if (ds > 0) {
            if (activeValue === 'wild4') {
                direct = (c.card_value === 'wild4');
            } else {
                direct = (c.card_value === 'tarik2' || c.card_value === 'wild4');
            }
        }
        if (direct && c.card_color !== 'wild') {
            playableValues.add(c.card_value);
        }
    });

    hand.forEach(card => {
        let canPlay = (
            card.card_color === activeColor ||
            card.card_value === activeValue ||
            card.card_color === 'wild' ||
            playableValues.has(card.card_value)
        );
        // Aturan counter draw_stack:
        // Jika kartu meja adalah wild4 (+4), kartu +2 TIDAK BISA dikeluarkan! Hanya +4 yang bisa!
        // Jika kartu meja adalah tarik2 (+2), BISA dilawan dengan +2 ATAU +4!
        if (ds > 0) {
            if (activeValue === 'wild4') {
                canPlay = (card.card_value === 'wild4');
            } else {
                canPlay = (card.card_value === 'tarik2' || card.card_value === 'wild4');
            }
        }

        const isPreviouslySelected = prevSelectedIds.has(parseInt(card.id)) && isMyTurn && canPlay;

        const div = document.createElement('div');
        div.className = `hand-card ${card.card_color} ${(!canPlay && isMyTurn) ? 'unplayable' : ''} ${isPreviouslySelected ? 'selected' : ''}`;
        div.dataset.id = card.id;
        div.dataset.color = card.card_color;
        div.dataset.value = card.card_value;
        div.dataset.playable = canPlay ? '1' : '0';
        div.onclick = (e) => { e.stopPropagation(); selectCard(div); };

        div.innerHTML = `<div class="card-val">${valDisplay(card.card_value)}</div>`;
        if (!isNaN(parseInt(card.card_value))) {
            div.innerHTML += `<div class="card-lbl">${card.card_color.toUpperCase()}</div>`;
        }

        if (isPreviouslySelected) {
            newSelectedCards.push({
                id: parseInt(card.id),
                color: card.card_color,
                value: card.card_value,
                el: div
            });
        }

        scroll.appendChild(div);
    });

    selectedCards = newSelectedCards;
    updatePlayBtn();
}

function updateOpponents(opponents, currentTurnId) {
    const bar = document.getElementById('opponents-bar');
    bar.innerHTML = '';
    opponents.forEach(opp => {
        const isTurn = opp.user_id == currentTurnId;
        bar.innerHTML += `
        <div class="opponent-chip ${isTurn ? 'is-turn' : ''}" id="opp-${opp.user_id}">
            <div class="opp-avatar">🎮</div>
            <div class="opp-name">${esc(opp.username)}</div>
            <div class="opp-cards">🃏 ${opp.card_count}</div>
            ${parseInt(opp.card_count) === 1 ? '<div class="opp-uno-badge">UNO!</div>' : ''}
        </div>`;
    });
}

// ── End Game ──────────────────────────────────────────────────
function showEndGame(isWin, winnerName) {
    gameOver = true;
    polling  = false;
    const el = document.getElementById('endgame-content');
    const winnerDisplay = esc(winnerName || 'Pemain Lain');

    el.innerHTML = `
        <div class="endgame-emoji" style="animation: bounce-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);">${isWin ? '🏆' : '💀'}</div>
        <div class="endgame-title" style="color:${isWin ? '#fbbf24' : '#ef4444'}; font-size:1.85rem; font-weight:900; letter-spacing:1px;">
            ${isWin ? '🎉 KAMU MENANG!' : '💀 KAMU KALAH!'}
        </div>
        <div style="margin: 8px auto 14px; display:inline-block; padding: 4px 14px; border-radius: 99px; font-size: 0.85rem; font-weight: 800; background: ${isWin ? 'rgba(251,191,36,0.15)' : 'rgba(239,68,68,0.15)'}; color: ${isWin ? '#fbbf24' : '#fca5a5'}; border: 1px solid ${isWin ? 'rgba(251,191,36,0.3)' : 'rgba(239,68,68,0.3)'};">
            ${isWin ? '👑 JUARA 1 UNO' : `👑 Pemenang: ${winnerDisplay}`}
        </div>
        <div class="endgame-sub" style="font-size:0.85rem; color:#cbd5e1; max-width:320px; margin:0 auto 1.5rem; line-height:1.45;">
            ${isWin 
                ? 'Selamat! Kartu di tanganmu telah habis dan kamu resmi menjadi Juara di Meja UNO! 🎉' 
                : `${winnerDisplay} telah menghabiskan seluruh kartunya terlebih dahulu. Jangan menyerah, buktikan di game berikutnya! 💪`}
        </div>
        <div style="background: rgba(255,255,255,0.04); border-radius: 12px; padding: 10px; margin-bottom: 1.25rem;">
            <p style="font-size:0.75rem; color:var(--text-muted); margin:0;">Otomatis kembali ke Lobby dalam <span id="countdown" style="font-weight:800; color:#fff;">5</span> detik...</p>
        </div>
        <a href="${BASE_URL}lobby" class="btn btn-primary btn-full" style="font-weight:800; padding:10px; display:flex; align-items:center; justify-content:center; gap:6px;">
            🏠 Kembali ke Lobby Sekarang
        </a>
    `;
    document.getElementById('modal-endgame').style.display = 'flex';

    let secs = 5;
    const timer = setInterval(() => {
        secs--;
        const cd = document.getElementById('countdown');
        if (cd) cd.textContent = secs;
        if (secs <= 0) {
            clearInterval(timer);
            window.location.href = BASE_URL + 'lobby';
        }
    }, 1000);
}

// ── Utilities ─────────────────────────────────────────────────
function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function ucfirst(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}
function showToast(msg, type = 'info', duration = 3000) {
    const wrap = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = msg;
    wrap.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'toast-out 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, duration);
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
        const res = await fetch('<?= site_url('uno/aksi_menyerah') ?>', {
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
    const chk = document.getElementById('chk-never-show-uno');
    if (chk && chk.checked) {
        localStorage.setItem('uno_rule_seen', '1');
    }
    const m = document.getElementById('modal-rule-guide');
    if (m) m.style.display = 'none';
}
function switchRuleTab(tabId, btn) {
    document.querySelectorAll('.rule-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.rule-tab-btn').forEach(b => b.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');
}

// ── Init ──────────────────────────────────────────────────────
if (gameOver) {
    const winnerId = <?= $state['winner_user_id'] ? (int)$state['winner_user_id'] : 'null' ?>;
    const winnerName = <?= json_encode($state['winner_username'] ?? '') ?>;
    if (winnerId !== null) {
        showEndGame(parseInt(winnerId) === parseInt(MY_USER_ID), winnerName);
    }
} else {
    // Tampilkan modal aturan otomatis di awal jika pemain belum mencentang jangan tampilkan
    if (!localStorage.getItem('uno_rule_seen')) {
        setTimeout(openRuleModal, 400);
    }
    setTimeout(pollState, 1500);
}
</script>
</body>
</html>
