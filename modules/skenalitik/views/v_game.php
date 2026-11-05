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
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Space+Grotesk:wght@600;700;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-page: #080c1a;
            --bg-arena: radial-gradient(ellipse at 50% 35%, #1e1b4b 0%, #0f172a 60%, #030712 100%);
            --bg-board: #0b1120;
            --tile-bg: #ffffff;
            --tile-border: #cbd5e1;
            --accent-neon: #10b981;
            --accent-orange: #f59e0b;
            --accent-purple: #8b5cf6;
            --accent-red: #ef4444;
            --accent-cyan: #06b6d4;
            --accent-gold: #fbbf24;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: max(22px, env(safe-area-inset-bottom, 0px));
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: var(--bg-arena);
            color: var(--text-primary);
            height: 100dvh;
            width: 100vw;
            overflow: hidden;
            touch-action: manipulation;
            user-select: none;
            -webkit-user-select: none;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* ── ATMOSPHERIC TOURNAMENT ARENA SPOTLIGHTS & STARS ─────── */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 18% 15%, rgba(99, 102, 241, 0.22) 0%, transparent 45%),
                radial-gradient(circle at 82% 20%, rgba(245, 158, 11, 0.16) 0%, transparent 40%),
                radial-gradient(circle at 50% 85%, rgba(16, 185, 129, 0.18) 0%, transparent 50%),
                radial-gradient(2px 2px at 15% 30%, rgba(255,255,255,0.5), transparent),
                radial-gradient(2px 2px at 85% 45%, rgba(255,255,255,0.5), transparent),
                radial-gradient(2px 2px at 45% 75%, rgba(255,255,255,0.4), transparent);
            pointer-events: none;
            z-index: 0;
        }

        /* ── TOP HEADER BAR (LINE GET RICH STADIUM EDITION) ────────── */
        .game-header {
            flex-shrink: 0;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(12px, var(--safe-left)) 0 max(12px, var(--safe-right));
            background: rgba(15, 23, 42, 0.92);
            border-bottom: 2px solid rgba(255, 255, 255, 0.09);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
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
            font-size: 0.95rem;
            font-weight: 800;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            transition: all 0.15s;
        }
        .btn-back:hover { 
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        .header-title-wrap h1 {
            font-size: 1rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fef08a 0%, #f59e0b 60%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            filter: drop-shadow(0 2px 6px rgba(245, 158, 11, 0.4));
        }
        .room-code-tag {
            font-size: 0.68rem;
            font-weight: 900;
            color: #fef08a;
            background: rgba(245, 158, 11, 0.2);
            border: 1.5px solid #f59e0b;
            padding: 2px 10px;
            border-radius: 99px;
            letter-spacing: 1px;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.35);
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-rule-trigger {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            font-weight: 900;
            font-size: 0.72rem;
            border: 1.5px solid #fde68a;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
            transition: transform 0.1s;
        }
        .btn-rule-trigger:active { transform: scale(0.96); }
        .btn-cards-trigger {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: #ffffff;
            font-weight: 900;
            font-size: 0.72rem;
            border: 1.5px solid #c4b5fd;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);
            transition: transform 0.1s;
        }
        .btn-cards-trigger:active { transform: scale(0.96); }
        .btn-cards-trigger.has-cards {
            border-color: #fbbf24;
            animation: cardsGlowPulse 1.8s infinite alternate;
        }
        @keyframes cardsGlowPulse {
            0% { box-shadow: 0 0 6px rgba(196, 181, 253, 0.4); }
            100% { box-shadow: 0 0 16px rgba(245, 158, 11, 0.9); }
        }
        .btn-surrender-trigger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            font-weight: 900;
            font-size: 0.72rem;
            border: 1.5px solid #fca5a5;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
            transition: transform 0.1s, background 0.15s;
        }
        .btn-surrender-trigger:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #f87171, #ef4444);
        }
        .btn-surrender-trigger:active { transform: scale(0.96); }
        .btn-icon-top {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.88rem;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-icon-top:hover { 
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        /* ── MAIN LANDSCAPE CONTAINER (FULL WIDESCREEN) ───────────── */
        .arena-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 2px max(4px, var(--safe-left)) max(10px, var(--safe-bottom)) max(4px, var(--safe-right));
            width: 100%;
            height: calc(100vh - 46px);
        }

        /* ── BOARD STAGE (PERSEGI PANJANG - FULL LANDSCAPE) ───────── */
        .board-stage {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            position: relative;
            overflow: hidden;
        }

        /* Papan Skenalitik: LINE Let's Get Rich Widescreen Stadium Board */
        .skena-board {
            display: grid;
            grid-template-columns: minmax(110px, 1.45fr) repeat(9, 1fr) minmax(110px, 1.45fr);
            grid-template-rows: minmax(48px, 1.35fr) repeat(9, 1fr) minmax(48px, 1.35fr);
            width: 100%;
            max-width: 100%;
            height: 100%;
            max-height: 100%;
            aspect-ratio: auto;
            background: #0b1120;
            border: 3px solid #334155;
            border-radius: 14px;
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.75),
                0 0 30px rgba(99, 102, 241, 0.25),
                inset 0 0 15px rgba(0, 0, 0, 0.6);
            position: relative;
            overflow: hidden;
            gap: 2px;
            padding: 3px;
        }

        /* ── TILES STYLING (Get Rich 3D Acrylic Game Blocks) ──────── */
        .tile {
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1px 2px;
            cursor: pointer;
            transition: all 0.12s ease;
            overflow: hidden;
            box-shadow: 0 2px 0 #94a3b8, 0 3px 5px rgba(0, 0, 0, 0.15);
        }
        .tile:hover {
            transform: translateY(-2px);
            z-index: 10;
            border-color: #f59e0b;
            box-shadow: 0 4px 0 #d97706, 0 6px 14px rgba(245, 158, 11, 0.45);
        }
        .tile:active {
            transform: translateY(1px);
            box-shadow: 0 1px 0 #94a3b8;
        }

        /* Color Bars on Property Tiles - Glossy Saturated 3D Pill */
        .tile-color-bar {
            height: 4px;
            width: 100%;
            border-radius: 3px 3px 0 0;
            flex-shrink: 0;
            margin-bottom: 1px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.25);
            background-image: linear-gradient(180deg, rgba(255,255,255,0.6) 0%, transparent 60%);
        }
        .color-cokelat   { background-color: #92400e; }
        .color-biru_muda { background-color: #0284c7; }
        .color-merah_muda{ background-color: #e11d48; }
        .color-oranye    { background-color: #ea580c; }
        .color-merah     { background-color: #dc2626; }
        .color-kuning    { background-color: #eab308; }
        .color-hijau     { background-color: #10b981; }
        .color-biru_tua  { background-color: #2563eb; }
        .color-transit   { background-color: #9333ea; }
        .color-utilitas  { background-color: #475569; }

        /* Top & Bottom Tiles Inner (Vertikal) */
        .tile-inner {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            width: 100%;
            text-align: center;
            pointer-events: none;
            gap: 1px;
            overflow: hidden;
            padding: 1px 2px;
        }
        .tile-name {
            font-size: 0.53rem;
            font-weight: 800;
            color: #0f172a;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.05;
            word-break: break-word;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
            max-height: 26px;
            letter-spacing: -0.2px;
        }

        /* ── THEMATIC VISUAL ART BADGE (GAMBAR PETAK) ──────────────── */
        .tile-art-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin: 1px auto;
            flex-shrink: 0;
        }
        .tile:hover .tile-art-badge {
            transform: scale(1.15);
            border-color: #f59e0b;
        }
        .tile-art-icon {
            font-size: 0.88rem;
            line-height: 1;
        }

        .tile-price {
            font-size: 0.52rem;
            font-weight: 900;
            color: #78350f;
            background: #fef3c7;
            padding: 0 3px;
            border-radius: 3px;
            border: 0.5px solid #f59e0b;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            line-height: 1.1;
        }
        .tile-action-tag {
            font-size: 0.5rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #1e293b;
            background: #e2e8f0;
            border: 0.5px solid #cbd5e1;
            padding: 0.5px 3px;
            border-radius: 3px;
            white-space: nowrap;
            line-height: 1.1;
        }

        /* Kolom Kiri & Kanan (Petak Samping Melebar Horizontal: Icon + Nama + Harga) */
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-inner,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-inner {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 0 4px;
            gap: 4px;
            height: 100%;
            width: 100%;
        }
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-art-badge,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-art-badge {
            width: 17px;
            height: 17px;
            margin: 0;
            flex-shrink: 0;
        }
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-art-icon,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-art-icon {
            font-size: 0.88rem;
        }
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-name,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-name {
            text-align: left;
            flex: 1;
            min-width: 0;
            font-size: 0.52rem;
            font-weight: 800;
            line-height: 1.05;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: normal;
            word-break: break-word;
            max-height: 22px;
        }
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-price,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-price,
        .tile[style*="grid-column: 1;"]:not(.tile-corner) .tile-action-tag,
        .tile[style*="grid-column: 11;"]:not(.tile-corner) .tile-action-tag {
            flex-shrink: 0;
            font-size: 0.5rem;
            padding: 0.5px 3px;
        }

        /* ── TILE OWNERSHIP STYLES (PENANDA HAK MILIK PROPERTI) ── */
        .tile.is-owned {
            transition: all 0.2s ease;
        }
        .tile.is-owned.p-owner-1 {
            border: 2px solid #3b82f6 !important;
            background: #f8fbff;
        }
        .tile.is-owned.p-owner-2 {
            border: 2px solid #f43f5e !important;
            background: #fff8f8;
        }
        .tile.is-owned.p-owner-3 {
            border: 2px solid #10b981 !important;
            background: #f6fdf9;
        }
        .tile.is-owned.p-owner-4 {
            border: 2px solid #f59e0b !important;
            background: #fffdf5;
        }
        .tile.is-owned.p-owner-5 {
            border: 2px solid #8b5cf6 !important;
            background: #faf8ff;
        }
        .tile.is-owned.p-owner-6 {
            border: 2px solid #06b6d4 !important;
            background: #f4fdff;
        }

        /* Corner Ownership Seal (Stempel Ikon Pemilik di Pojok Kiri Atas Petak) */
        .tile-owner-seal {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            z-index: 6;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            border: 1.5px solid #cbd5e1;
            pointer-events: none;
        }
        .p-owner-1 .tile-owner-seal { border-color: #2563eb; background: #eff6ff; }
        .p-owner-2 .tile-owner-seal { border-color: #e11d48; background: #fff1f2; }
        .p-owner-3 .tile-owner-seal { border-color: #059669; background: #f0fdf4; }
        .p-owner-4 .tile-owner-seal { border-color: #d97706; background: #fffbeb; }
        .p-owner-5 .tile-owner-seal { border-color: #7c3aed; background: #faf5ff; }
        .p-owner-6 .tile-owner-seal { border-color: #0891b2; background: #ecfeff; }

        /* Bottom Ownership Banner (Spanduk Nama & Token Pemilik) */
        .tile-owner-banner {
            width: calc(100% - 2px);
            padding: 1px 2px;
            border-radius: 3px;
            font-size: 0.44rem;
            font-weight: 900;
            color: #ffffff;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            margin-top: 1px;
        }
        .tile-owner-banner.p-banner-1 { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .tile-owner-banner.p-banner-2 { background: linear-gradient(135deg, #e11d48, #f43f5e); }
        .tile-owner-banner.p-banner-3 { background: linear-gradient(135deg, #059669, #10b981); }
        .tile-owner-banner.p-banner-4 { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .tile-owner-banner.p-banner-5 { background: linear-gradient(135deg, #7c3aed, #8b5cf6); }
        .tile-owner-banner.p-banner-6 { background: linear-gradient(135deg, #0891b2, #06b6d4); }

        /* Building Level Miniatures & Landmark Badges */
        .tile-building-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 0.48rem;
            padding: 1px 5px;
            border-radius: 4px;
            font-weight: 900;
            color: #ffffff;
            z-index: 6;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .tile-building-badge.level-1 { background: linear-gradient(135deg, #b45309, #d97706); }
        .tile-building-badge.level-2 { background: linear-gradient(135deg, #0284c7, #38bdf8); }
        .tile-building-badge.level-3 { background: linear-gradient(135deg, #7c3aed, #9333ea); }

        /* Corner Tiles Special Styling (LINE Get Rich Special Zones - Widescreen Fit) */
        .tile-corner {
            background: linear-gradient(145deg, #ffffff, #f1f5f9);
            box-shadow: 0 4px 0 #64748b, 0 6px 12px rgba(0, 0, 0, 0.25);
            font-weight: 900;
        }
        .tile-corner .tile-inner {
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 2px 5px;
            height: 100%;
            width: 100%;
        }
        .tile-corner .tile-art-badge {
            width: 24px;
            height: 24px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            margin: 0;
            flex-shrink: 0;
        }
        .tile-corner .tile-art-icon {
            font-size: 1.15rem;
            line-height: 1;
        }
        .tile-corner .tile-name {
            font-size: 0.58rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            text-align: left;
            flex: 1;
            min-width: 0;
            max-height: none;
            -webkit-line-clamp: 3;
            white-space: normal;
        }
        .corner-start {
            background: radial-gradient(circle at center, #dcfce7 0%, #86efac 100%);
            border: 2px solid #22c55e;
            box-shadow: 0 4px 0 #15803d, 0 0 15px rgba(34, 197, 94, 0.45);
        }
        .corner-jail {
            background: radial-gradient(circle at center, #fee2e2 0%, #fca5a5 100%);
            border: 2px solid #ef4444;
            box-shadow: 0 4px 0 #b91c1c, 0 0 15px rgba(239, 68, 68, 0.45);
        }
        .corner-vip {
            background: radial-gradient(circle at center, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            box-shadow: 0 4px 0 #b45309, 0 0 15px rgba(245, 158, 11, 0.45);
        }
        .corner-skandal {
            background: radial-gradient(circle at center, #ede9fe 0%, #c4b5fd 100%);
            border: 2px solid #8b5cf6;
            box-shadow: 0 4px 0 #6d28d9, 0 0 15px rgba(139, 92, 246, 0.45);
        }

        /* ── ANIMASI MELANGKAH & TOKEN PION PEMAIN JELAS & BESAR ──── */
        .pawns-container {
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
            flex-wrap: wrap;
            pointer-events: none;
            z-index: 30;
            min-height: 20px;
        }
        .pawn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            border: 1.5px solid #3b82f6;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3), 0 0 0 1px #ffffff;
            font-size: 0.95rem;
            line-height: 1;
            transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            flex-shrink: 0;
            pointer-events: auto;
        }
        .pawn-icon.pawn-user-order-1 { border-color: #2563eb; background: #eff6ff; }
        .pawn-icon.pawn-user-order-2 { border-color: #e11d48; background: #fff1f2; }
        .pawn-icon.pawn-user-order-3 { border-color: #059669; background: #f0fdf4; }
        .pawn-icon.pawn-user-order-4 { border-color: #d97706; background: #fffbeb; }
        .pawn-icon.pawn-user-order-5 { border-color: #7c3aed; background: #faf5ff; }
        .pawn-icon.pawn-user-order-6 { border-color: #0891b2; background: #ecfeff; }

        /* Highlight khusus pion milik user sendiri */
        .pawn-icon.is-my-pawn {
            box-shadow: 0 0 0 2px #38bdf8, 0 3px 10px rgba(56, 189, 248, 0.7);
            z-index: 35;
        }

        /* Highlight pion yang sedang memegang giliran jalan */
        .pawn-icon.is-turn-pawn {
            z-index: 45 !important;
            box-shadow: 0 0 0 2.5px #f59e0b, 0 0 14px rgba(245, 158, 11, 0.95) !important;
            animation: turnPawnGlow 1.2s infinite ease-in-out alternate !important;
        }
        @keyframes turnPawnGlow {
            0% { transform: scale(1.1); box-shadow: 0 0 0 2px #f59e0b, 0 0 8px rgba(245, 158, 11, 0.7); }
            100% { transform: scale(1.35); box-shadow: 0 0 0 3.5px #f59e0b, 0 0 18px rgba(245, 158, 11, 1); }
        }

        .pawn-hopping {
            animation: pawn-hop-step 0.18s ease-in-out forwards;
        }
        @keyframes pawn-hop-step {
            0% { transform: translateY(0) scale(1); }
            40% { transform: translateY(-18px) scale(1.5) rotate(-6deg); z-index: 50; }
            100% { transform: translateY(0) scale(1) rotate(0deg); }
        }
        .tile-step-glow {
            animation: step-flash 0.3s ease-out;
        }
        @keyframes step-flash {
            0% { background: #fef08a; box-shadow: inset 0 0 12px rgba(245, 158, 11, 0.8); }
            100% { background: #ffffff; box-shadow: none; }
        }
        .tile-landed-glow {
            animation: landed-burst 0.6s ease-out;
        }
        @keyframes landed-burst {
            0% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.7), inset 0 0 10px rgba(16, 185, 129, 0.4); border-color: #10b981; }
            100% { box-shadow: none; }
        }
        .board-landing-thud {
            animation: board-thud 0.22s ease-out;
        }
        @keyframes board-thud {
            0% { transform: scale(1); }
            30% { transform: scale(0.988) translateY(2px); }
            60% { transform: scale(1.006) translateY(-1px); }
            100% { transform: scale(1) translateY(0); }
        }

        /* ── CENTER BOARD: GET RICH TOURNAMENT ARENA (3-COLUMN ARENA) ── */
        .board-center {
            grid-column: 2 / 11;
            grid-row: 2 / 11;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 4px 6px;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 75%, #090d16 100%);
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 35px rgba(0, 0, 0, 0.75), 0 0 25px rgba(99, 102, 241, 0.2);
            gap: 6px;
        }
        .board-center::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Player Wings (Inside Center Table) & Center Core */
        .hud-player-wing {
            width: 130px;
            max-width: 140px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            justify-content: center;
            flex-shrink: 0;
            z-index: 5;
        }
        .center-game-core {
            flex: 1;
            min-width: 0;
            max-width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            gap: 3px;
            z-index: 5;
        }

        /* Center Top Bar */
        .center-top-bar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            z-index: 5;
        }
        .center-brand {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 99px;
            padding: 3px 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        .brand-trophy { font-size: 0.95rem; }
        .brand-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.82rem;
            font-weight: 900;
            letter-spacing: 1.2px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .center-pot-tax {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.35));
            border: 1.5px solid #f59e0b;
            border-radius: 99px;
            padding: 3px 10px;
            font-size: 0.68rem;
            color: #fef08a;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
            white-space: nowrap;
        }

        /* Center Middle: Dice Shaker & Live Status */
        .center-middle-arena {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            z-index: 5;
            width: 100%;
        }
        .center-turn-banner {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.78rem;
            font-weight: 900;
            letter-spacing: 1px;
            color: #fbbf24;
            text-transform: uppercase;
            text-shadow: 0 0 10px rgba(245, 158, 11, 0.6);
        }
        .dice-display-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 14px;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5), inset 0 0 10px rgba(99, 102, 241, 0.2);
        }
        .dice-cup-icon {
            font-size: 1.4rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
        }
        .dice-pair {
            display: flex;
            gap: 6px;
        }
        .dice-cube {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            color: #0f172a;
            border-radius: 8px;
            font-weight: 900;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 0 #94a3b8, 0 4px 10px rgba(0,0,0,0.3);
            border: 1.5px solid #ffffff;
            transition: transform 0.15s;
        }
        .dice-rolling {
            animation: dice-tumble 0.5s infinite ease-in-out;
        }
        @keyframes dice-tumble {
            0% { transform: rotate(0deg) scale(1); }
            25% { transform: rotate(90deg) scale(1.15) translateY(-4px); }
            50% { transform: rotate(180deg) scale(0.9) translateY(2px); }
            75% { transform: rotate(270deg) scale(1.1) translateY(-2px); }
            100% { transform: rotate(360deg) scale(1); }
        }
        .center-desc-banner {
            font-size: 0.68rem;
            color: #cbd5e1;
            font-weight: 600;
            text-align: center;
            max-width: 90%;
            line-height: 1.25;
        }

        /* Center Bottom: Action Controls Deck */
        .center-action-deck {
            width: 100%;
            max-width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            z-index: 10;
        }

        /* Get Rich Dice Control Power Gauge */
        .dice-gauge-wrap {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .dice-gauge-bar {
            width: 100%;
            height: 10px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 99px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            padding: 0 6px;
            align-items: center;
        }
        .dice-gauge-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 35%;
            background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);
            border-radius: 99px;
            animation: gauge-sweep 2s infinite ease-in-out alternate;
        }
        @keyframes gauge-sweep {
            0% { width: 15%; }
            50% { width: 65%; }
            100% { width: 95%; }
        }
        .gauge-label {
            font-size: 0.5rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.7);
            z-index: 2;
        }

        /* 3D Action Buttons */
        .btn-action-main {
            width: 100%;
            height: 38px;
            border-radius: 10px;
            font-weight: 900;
            font-size: 0.88rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.12s ease;
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.4), 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        .btn-action-main:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
        }
        .btn-roll {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border-top: 1px solid #6ee7b7;
            box-shadow: 0 4px 0 #047857, 0 6px 18px rgba(16, 185, 129, 0.4);
        }
        .btn-endturn {
            background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            border-top: 1px solid #93c5fd;
            box-shadow: 0 4px 0 #1e40af, 0 6px 18px rgba(59, 130, 246, 0.4);
        }
        .btn-danger-jail {
            background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
            color: #ffffff;
            font-size: 0.74rem;
            height: 34px;
            box-shadow: 0 3px 0 #991b1b;
        }
        .btn-gold-jail {
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 0.74rem;
            height: 34px;
            box-shadow: 0 3px 0 #b45309, 0 0 12px rgba(245, 158, 11, 0.6);
            border-top: 1px solid #fde68a;
            animation: pulse-gold 1.5s infinite alternate;
        }
        @keyframes pulse-gold {
            0% { transform: scale(1); box-shadow: 0 3px 0 #b45309, 0 0 8px rgba(245, 158, 11, 0.5); }
            100% { transform: scale(1.02); box-shadow: 0 3px 0 #b45309, 0 0 16px rgba(245, 158, 11, 0.9); }
        }

        /* Property Purchase Offer Card in Center */
        .center-buy-card {
            width: 100%;
            background: rgba(15, 23, 42, 0.95);
            border: 1.5px solid #f59e0b;
            border-radius: 10px;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.35);
        }
        .buy-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }
        .buy-card-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }
        .buy-card-price {
            font-size: 0.75rem;
            font-weight: 900;
            color: #fbbf24;
        }
        .buy-card-buttons {
            display: flex;
            gap: 6px;
        }
        .buy-card-buttons .btn-buy {
            flex: 2;
            height: 32px;
            font-size: 0.78rem;
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            border-top: 1px solid #fde68a;
            box-shadow: 0 3px 0 #b45309;
        }
        .buy-card-buttons .btn-upgrade {
            flex: 2;
            height: 32px;
            font-size: 0.78rem;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border-top: 1px solid #6ee7b7;
            box-shadow: 0 3px 0 #047857;
        }
        .center-upgrade-card {
            border-color: #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }
        .buy-card-buttons .btn-skip {
            flex: 1;
            height: 32px;
            font-size: 0.72rem;
            background: #334155;
            color: #cbd5e1;
            border: 1px solid #475569;
            box-shadow: 0 3px 0 #1e293b;
        }

        /* Center Waiting Badge */
        .center-waiting-badge {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 99px;
            padding: 4px 12px;
            font-size: 0.68rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Center Mini Marquee Ticker */
        .center-logs-ticker {
            font-size: 0.58rem;
            color: #94a3b8;
            background: rgba(0, 0, 0, 0.35);
            border-radius: 6px;
            padding: 2px 8px;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 5;
        }

        /* ── S+ PLAYER CARDS (INSIDE CENTER ARENA TABLE) ────────── */
        .player-item {
            width: 100%;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.98));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 4px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .player-item.is-active-turn {
            border-color: #f59e0b !important;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.22), rgba(15, 23, 42, 0.95)) !important;
            box-shadow: 0 0 18px rgba(245, 158, 11, 0.8), inset 0 0 10px rgba(245, 158, 11, 0.3) !important;
            animation: turn-fire-pulse 1.4s infinite alternate !important;
            opacity: 1 !important;
            filter: none !important;
            z-index: 10;
        }
        @keyframes turn-fire-pulse {
            0% { transform: scale(1); box-shadow: 0 0 12px rgba(245, 158, 11, 0.5); }
            100% { transform: scale(1.02); box-shadow: 0 0 20px rgba(245, 158, 11, 0.85); }
        }
        .player-item.is-eliminated {
            opacity: 0.35;
            filter: grayscale(1);
        }
        .p-token-avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: radial-gradient(circle, #334155 0%, #0f172a 100%);
            border: 1.5px solid #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        .player-item.p-user-order-1 .p-token-avatar { border-color: #38bdf8; background: radial-gradient(circle, #0284c7 0%, #0c4a6e 100%); }
        .player-item.p-user-order-2 .p-token-avatar { border-color: #fb7185; background: radial-gradient(circle, #e11d48 0%, #881337 100%); }
        .player-item.p-user-order-3 .p-token-avatar { border-color: #34d399; background: radial-gradient(circle, #059669 0%, #064e3b 100%); }
        .player-item.p-user-order-4 .p-token-avatar { border-color: #fbbf24; background: radial-gradient(circle, #d97706 0%, #78350f 100%); }

        .p-meta {
            flex: 1;
            min-width: 0;
        }
        .p-name {
            font-size: 0.68rem;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .p-pr-balance {
            font-size: 0.7rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-top: 1px;
        }
        .player-item.p-user-order-1 .p-pr-balance { color: #7dd3fc; }
        .player-item.p-user-order-2 .p-pr-balance { color: #fda4af; }
        .player-item.p-user-order-3 .p-pr-balance { color: #6ee7b7; }
        .player-item.p-user-order-4 .p-pr-balance { color: #fde047; }
        .p-pr-balance.in-debt {
            color: #f87171 !important;
            animation: pulse-danger 1s infinite alternate;
        }
        @keyframes pulse-danger {
            0% { opacity: 0.8; }
            100% { opacity: 1; text-shadow: 0 0 6px rgba(220, 38, 38, 0.8); }
        }
        .p-badge {
            font-size: 0.58rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
        }
        .badge-jail { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Flying "+200 CLOUT" banner */
        .floating-clout-banner {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5);
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 900;
            font-size: 1.3rem;
            padding: 8px 20px;
            border-radius: 99px;
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.65);
            border: 2px solid #a7f3d0;
            z-index: 1000;
            pointer-events: none;
            animation: clout-fly 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes clout-fly {
            0% { opacity: 0; transform: translate(-50%, 0) scale(0.4); }
            20% { opacity: 1; transform: translate(-50%, -50%) scale(1.2); }
            80% { opacity: 1; transform: translate(-50%, -70%) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -100%) scale(0.8); }
        }

        /* ── MODAL ATURAN MAIN & PANDUAN (ZINE EDITION) ───────────── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            animation: fadeIn 0.2s ease-out;
        }
        .modal-rule-box {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 18px;
            width: min(92vw, 760px);
            height: min(92vh, 480px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25), 0 0 25px rgba(245, 158, 11, 0.15);
        }
        .modal-header {
            padding: 12px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-modal-close {
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.3rem;
            cursor: pointer;
            transition: color 0.15s;
        }
        .btn-modal-close:hover { color: #0f172a; }

        /* Nav Tab Horisontal */
        .rule-tabs {
            display: flex;
            gap: 6px;
            padding: 8px 14px;
            background: #f1f5f9;
            overflow-x: auto;
            scrollbar-width: none;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .rule-tabs::-webkit-scrollbar { display: none; }
        .rule-tab-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.15s;
        }
        .rule-tab-btn.active {
            background: #f59e0b;
            color: #ffffff;
            border-color: #f59e0b;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
        }

        .rule-tab-content {
            flex: 1;
            padding: 16px 20px;
            overflow-y: auto;
            font-size: 0.82rem;
            line-height: 1.5;
            color: #334155;
        }
        .rule-tab-content h4 {
            color: #059669;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        .rule-tab-content ul {
            padding-left: 18px;
            margin-bottom: 10px;
        }
        .rule-tab-content li {
            margin-bottom: 5px;
        }

        .modal-footer {
            padding: 10px 18px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chk-dont-show {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #64748b;
            cursor: pointer;
        }
        .btn-gas-play {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 8px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }

        /* ── MODAL INSPECT PETAK (DEED CARD) ─────────────────────── */
        .deed-card-box {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 16px;
            width: min(85vw, 320px);
            padding: 16px;
            color: #0f172a;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
        }

        /* ── MODAL UPGRADE PROPERTI (LINE GET RICH POPUP - ULTRA COMPACT FOR LANDSCAPE) ─ */
        .modal-upgrade-box {
            background: linear-gradient(180deg, #091322 0%, #0f172a 60%, #1e293b 100%);
            border: 2px solid #10b981;
            border-radius: 16px;
            width: min(94vw, 440px);
            max-height: 94vh;
            padding: 10px 14px;
            color: #ffffff;
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.45), 0 20px 60px rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            animation: zoomInGetRich 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            position: relative;
            box-sizing: border-box;
        }
        @keyframes zoomInGetRich {
            0% { opacity: 0; transform: scale(0.85) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .upg-header-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .upgrade-modal-top-tag {
            font-size: 0.62rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #6ee7b7;
            text-transform: uppercase;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            padding: 2px 10px;
            border-radius: 99px;
            margin: 0 auto;
        }
        .upg-close-btn {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #94a3b8;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .upg-close-btn:hover { color: #fff; background: rgba(239, 68, 68, 0.5); }
        .upg-tile-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 1px 0;
        }
        .upgrade-modal-icon-badge {
            font-size: 1.4rem;
            line-height: 1;
        }
        .upgrade-modal-tile-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.98rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
        }
        .upgrade-levels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            width: 100%;
            margin: 1px 0;
        }
        .upg-level-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 5px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            user-select: none;
        }
        .upg-level-card:hover:not(.is-already-built) {
            transform: translateY(-2px);
            border-color: #34d399;
        }
        .upg-level-card.is-selected {
            background: linear-gradient(180deg, rgba(6, 78, 59, 0.9) 0%, rgba(6, 95, 70, 0.6) 100%);
            border-color: #10b981;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.5), inset 0 0 6px rgba(16, 185, 129, 0.3);
            transform: scale(1.02);
        }
        .upg-level-card.is-already-built {
            background: rgba(15, 23, 42, 0.45);
            border-color: rgba(255, 255, 255, 0.06);
            opacity: 0.65;
            cursor: not-allowed;
        }
        .upg-card-top {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .upg-card-icon {
            font-size: 1.15rem;
        }
        .upg-card-badge {
            font-size: 0.58rem;
            font-weight: 800;
            background: rgba(255, 255, 255, 0.12);
            padding: 1px 4px;
            border-radius: 4px;
            color: #cbd5e1;
        }
        .upg-level-card.is-selected .upg-card-badge {
            background: #10b981;
            color: #ffffff;
        }
        .upg-card-name {
            font-size: 0.62rem;
            font-weight: 800;
            color: #f1f5f9;
            text-align: center;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }
        .upg-card-cost {
            font-size: 0.72rem;
            font-weight: 900;
            color: #fbbf24;
        }
        .upg-card-status {
            font-size: 0.54rem;
            color: #94a3b8;
            font-weight: 600;
            white-space: nowrap;
        }
        .upg-level-card.is-already-built .upg-card-cost {
            color: #10b981;
            font-size: 0.62rem;
        }
        .upgrade-stats-card {
            width: 100%;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 6px 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 10px;
            box-sizing: border-box;
        }
        .upg-stat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.7rem;
            gap: 4px;
        }
        .upg-stat-label {
            color: #94a3b8;
            font-weight: 600;
            white-space: nowrap;
        }
        .upg-stat-val {
            font-weight: 800;
            white-space: nowrap;
        }
        .upg-val-cost {
            color: #fbbf24;
            font-size: 0.78rem;
            font-weight: 900;
        }
        .upg-val-rent {
            color: #34d399;
        }
        .upg-val-balance {
            color: #cbd5e1;
        }
        .upgrade-modal-buttons {
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: 8px;
            margin-top: 2px;
        }
        .btn-upgrade-confirm {
            flex: 2;
            height: 38px;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.85rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            border-radius: 10px;
            border: none;
            border-top: 1px solid #a7f3d0;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(16, 185, 129, 0.45), 0 2px 0 #047857;
            transition: transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-upgrade-confirm:active {
            transform: translateY(2px);
            box-shadow: 0 1px 0 #047857;
        }
        .btn-upgrade-skip {
            flex: 1;
            height: 38px;
            background: rgba(51, 65, 85, 0.85);
            color: #cbd5e1;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-upgrade-skip:hover {
            background: rgba(71, 85, 105, 1);
            color: #ffffff;
        }

        /* ── CARD REVEAL & HAND CARDS POPUP MODAL ────────────────── */
        .card-drawn-box {
            width: min(90vw, 380px);
            background: #0f172a;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
            border: 2px solid #38bdf8;
            animation: cardPopupZoom 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card-drawn-box.type-politik {
            border-color: #f59e0b;
            box-shadow: 0 0 35px rgba(245, 158, 11, 0.45);
        }
        .card-drawn-box.type-skena {
            border-color: #06b6d4;
            box-shadow: 0 0 35px rgba(6, 182, 212, 0.45);
        }
        @keyframes cardPopupZoom {
            0% { transform: scale(0.7) rotate(-4deg); opacity: 0; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .card-reveal-badge {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 900;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .card-reveal-badge.type-politik {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid #f59e0b;
        }
        .card-reveal-badge.type-skena {
            background: rgba(6, 182, 212, 0.2);
            color: #38bdf8;
            border: 1px solid #06b6d4;
        }
        .card-reveal-icon {
            font-size: 3.5rem;
            margin-bottom: 8px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.5));
        }
        .card-reveal-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 8px;
        }
        .card-reveal-desc {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #cbd5e1;
            background: rgba(30, 41, 59, 0.7);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .badge-card-kept {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            color: #34d399;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid #10b981;
            padding: 6px 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .badge-card-instant {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid #0284c7;
            padding: 6px 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        /* Hand Cards Modal */
        .hand-card-item {
            background: #1e293b;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1.5px solid #334155;
            transition: all 0.2s ease;
        }
        .hand-card-item.type-politik {
            border-color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), #1e293b);
        }
        .hand-card-item.type-skena {
            border-color: #06b6d4;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), #1e293b);
        }
        .hand-card-info {
            text-align: left;
            flex: 1;
        }
        .hand-card-name {
            font-weight: 900;
            font-size: 0.92rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }
        .hand-card-desc {
            font-size: 0.74rem;
            color: #94a3b8;
            line-height: 1.4;
        }

        /* ── PORTRAIT ORIENTATION SHIELD (HP WAJIB LANDSCAPE) ────── */
        #portrait-shield {
            display: none;
            position: fixed;
            inset: 0;
            background: #ffffff;
            z-index: 999999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
            color: #0f172a;
        }
        @media screen and (orientation: portrait) and (max-width: 900px) {
            #portrait-shield { display: flex; }
        }
        .rotate-phone-anim {
            font-size: 4rem;
            margin-bottom: 16px;
            animation: rotate-phone 2s infinite ease-in-out;
        }
        @keyframes rotate-phone {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(90deg); }
            100% { transform: rotate(0deg); }
        }
        .rotate-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #d97706;
            margin-bottom: 8px;
        }
        .rotate-desc {
            font-size: 0.88rem;
            color: #475569;
            max-width: 290px;
            line-height: 1.45;
            margin-bottom: 20px;
        }
        .btn-fullscreen-shield {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            font-weight: 800;
            padding: 9px 22px;
            border-radius: 99px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
        }
    </style>
</head>
<body>

<!-- 📱 SHIELD: ROTATE PHONE TO LANDSCAPE (Untuk Pengguna HP Vertikal) -->
<div id="portrait-shield">
    <div class="rotate-phone-anim">📱</div>
    <div class="rotate-title">PUTAR KE MODE LANDSCAPE</div>
    <div class="rotate-desc">
        Panggung <b>SKENALITIK</b> didesain khusus secara rapi dalam orientasi mendatar (Landscape) agar papan 40 petak tampil maksimal.
    </div>
    <button class="btn-fullscreen-shield" onclick="enableLandscape()">🔄 Kunci & Masuk Landscape</button>
</div>

<!-- 🔝 TOP HEADER BAR -->
<header class="game-header">
    <div class="header-left">
        <a href="javascript:void(0)" onclick="openSurrenderModal()" class="btn-back" title="Menyerah / Keluar ke Lobby">←</a>
        <div class="header-title-wrap">
            <h1>🎙️ SKENALITIK</h1>
            <span class="room-code-tag"><?= htmlspecialchars($room_code) ?></span>
        </div>
    </div>
    <div class="header-right">
        <?php $card_count = count($state['my_cards'] ?? []); ?>
        <button class="btn-cards-trigger <?= $card_count > 0 ? 'has-cards' : '' ?>" id="btn-hand-cards" onclick="openHandCardsModal()" title="Koleksi Kartu Sakti di Tangan">
            🃏 Kartu Sakti (<span id="hand-card-count"><?= $card_count ?></span>)
        </button>
        <button class="btn-rule-trigger" onclick="openRuleModal()">
            📖 Aturan Main
        </button>
        <button class="btn-surrender-trigger" onclick="openSurrenderModal()" title="Menyerah dan keluar ke Lobby">
            🏳️ Menyerah
        </button>
        <button class="btn-icon-top" id="btn-sound-toggle" onclick="toggleSound()" title="Suara SFX">
            🔊
        </button>
        <button class="btn-icon-top" onclick="toggleFullScreen()" title="Layar Penuh">
            ⛶
        </button>
    </div>
</header>

<?php
    $left_players = [];
    $right_players = [];
    $current_turn_player = null;
    foreach ($state['players'] as $idx => $p) {
        if ($idx % 2 === 0) $left_players[] = $p;
        else $right_players[] = $p;
        if ((int)$p['user_id'] === (int)$state['game']['current_turn_user_id']) {
            $current_turn_player = $p;
        }
    }
    $turn_player_name = $current_turn_player ? htmlspecialchars($current_turn_player['username']) : 'LAWAN';
    $turn_player_order = $current_turn_player ? ' (P' . $current_turn_player['turn_order'] . ')' : '';
?>

<!-- 🏟️ MAIN LANDSCAPE ARENA (FULL VIEWPORT MONOPOLY) -->
<div class="arena-container">
    <!-- ── CENTER STAGE: BOARD (11x11 Grid Perimeter) ── -->
    <div class="board-stage">
        <div class="skena-board" id="skena-board-grid">
            <!-- Center Board Arena (Get Rich Tournament Stage: 3-Column Arena) -->
            <div class="board-center">
                <!-- 🎭 LEFT PLAYER HUD WING (S+ CHARACTER CARDS) -->
                <div class="hud-player-wing hud-left" id="hud-player-left">
                    <?php foreach ($left_players as $p): ?>
                    <?php
                        $is_current = ((int)$state['game']['current_turn_user_id'] === (int)$p['user_id']);
                        $is_debt = ((int)$p['reputation_points'] < 0);
                        $is_in_jail = ($p['is_in_jail'] === true || (int)$p['is_in_jail'] === 1 || $p['is_in_jail'] === '1');
                        $is_elim = ($p['is_eliminated'] === true || (int)$p['is_eliminated'] === 1 || $p['is_eliminated'] === '1');
                    ?>
                    <div class="player-item p-user-order-<?= $p['turn_order'] ?> <?= $is_current ? 'is-active-turn' : '' ?> <?= $is_elim ? 'is-eliminated' : '' ?>">
                        <div class="p-token-avatar" title="<?= htmlspecialchars($p['token_character']) ?>">
                            <?= $p['token_icon'] ?>
                        </div>
                        <div class="p-meta">
                            <div class="p-name">
                                <span><?= htmlspecialchars($p['username']) ?></span>
                                <span style="font-size:0.62rem;opacity:0.8;font-weight:700;">(P<?= $p['turn_order'] ?>)</span>
                                <?= $is_current ? '🔥' : '' ?>
                            </div>
                            <div class="p-pr-balance <?= $is_debt ? 'in-debt' : '' ?>">
                                🟡 <?= number_format($p['reputation_points']) ?> PR
                                <?php if ($is_debt): ?>
                                <span style="font-size:0.55rem; color:#ef4444;">(UTANG)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($is_in_jail): ?>
                        <span class="p-badge badge-jail">Karantina</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- 🎲 CENTER GAME CORE -->
                <div class="center-game-core">
                    <!-- Center Top: Logo & Kas Denda VIP Jackpot -->
                    <div class="center-top-bar">
                        <div class="center-brand">
                            <span class="brand-trophy">🏆</span>
                            <span class="brand-title">SKENALITIK</span>
                        </div>
                        <div class="center-pot-tax" id="pot-tax-info">
                            💰 KAS VIP: <b><?= (int)($state['game']['pot_tax'] ?? 0) ?> PR</b>
                        </div>
                    </div>

                    <!-- Center Middle: Status & Velvet Dice Tray -->
                    <div class="center-middle-arena">
                        <div class="center-turn-banner" id="prompt-card-title">
                            <?= $is_my_turn ? '🔥 GILIRAN ANDA' : '⏳ GILIRAN @' . strtoupper($turn_player_name) . $turn_player_order ?>
                        </div>
                        <div class="dice-display-box">
                            <span class="dice-cup-icon" id="dice-cup">☕</span>
                            <div class="dice-pair">
                                <div class="dice-cube" id="cube-d1"><?= $state['game']['dice_1'] ?? '?' ?></div>
                                <div class="dice-cube" id="cube-d2"><?= $state['game']['dice_2'] ?? '?' ?></div>
                            </div>
                        </div>
                        <div class="center-desc-banner" id="prompt-card-desc">
                            <?= htmlspecialchars($state['game']['last_action_desc'] ?? 'Lempar dadu untuk melangkah di panggung!') ?>
                        </div>
                    </div>

                    <!-- Center Bottom: Dynamic Action Deck (Roll / Buy / End Turn) -->
                    <div class="center-action-deck" id="action-controls-grid">
                        <?php if ($is_my_turn && empty($winner)): ?>
                            <?php if ($state['game']['game_phase'] === 'roll'): ?>
                                <?php
                                    $me = null;
                                    foreach ($state['players'] as $pl) { if ((int)$pl['user_id'] === $user_id) $me = $pl; }
                                    $me_in_jail = ($me && ($me['is_in_jail'] === true || (int)$me['is_in_jail'] === 1 || $me['is_in_jail'] === '1'));
                                ?>
                                <?php if ($me_in_jail): ?>
                                    <?php
                                        $has_hakim_card = false;
                                        foreach (($state['my_cards'] ?? []) as $c) {
                                            if ($c['deck_type'] === 'politik' && (int)$c['card_index'] === 8) $has_hakim_card = true;
                                        }
                                    ?>
                                    <?php if ($has_hakim_card): ?>
                                        <button class="btn-action-main btn-gold-jail" onclick="useJailCard()">
                                            ⚖️ Bebas dengan Kartu Intervensi Hakim
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-action-main btn-danger-jail" onclick="payJailFine()">
                                        💸 Bayar Denda Karantina (150 PR)
                                    </button>
                                <?php endif; ?>
                                <div class="dice-gauge-wrap">
                                    <div class="dice-gauge-bar">
                                        <div class="dice-gauge-fill"></div>
                                        <span class="gauge-label">2-3</span>
                                        <span class="gauge-label">4-9</span>
                                        <span class="gauge-label">10-12</span>
                                    </div>
                                </div>
                                <button class="btn-action-main btn-roll" id="btn-roll-dice" onclick="rollDice()">
                                    🎲 KOCOK DADU!
                                </button>
                            <?php elseif ($state['game']['pending_action'] === 'buy_property'): ?>
                                <?php
                                    $pending_tile = $state['tiles_config'][$state['game']['pending_tile_id']];
                                ?>
                                <div class="center-buy-card">
                                    <div class="buy-card-header">
                                        <span style="font-size:1.1rem;"><?= $pending_tile['icon'] ?? '🏢' ?></span>
                                        <div class="buy-card-title"><?= htmlspecialchars($pending_tile['name']) ?></div>
                                        <div class="buy-card-price">🪙 <?= $pending_tile['price'] ?> PR</div>
                                    </div>
                                    <div class="buy-card-buttons">
                                        <button class="btn-action-main btn-buy" onclick="buyProperty()">
                                            🏷️ BELI (<?= $pending_tile['price'] ?> PR)
                                        </button>
                                        <button class="btn-action-main btn-skip" onclick="skipProperty()">
                                            ✕ Lewati
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($state['game']['pending_action'] === 'upgrade_property'): ?>
                                <?php
                                    $pending_tile = $state['tiles_config'][$state['game']['pending_tile_id']];
                                    $prop = $state['properties'][$state['game']['pending_tile_id']] ?? null;
                                    $cur_lvl = (int)($prop['building_level'] ?? 0);
                                    $next_lvl = $cur_lvl + 1;
                                    $lvl_badges = [
                                        1 => '🛖 Level 1 (Sablon Gelap)',
                                        2 => '🏢 Level 2 (Ruang Kreatif Elit)',
                                        3 => '🏛️ Level 3 (Korporasi Media)'
                                    ];
                                    $target_badge = $lvl_badges[$next_lvl] ?? "Level {$next_lvl}";
                                    $cost = (int)($pending_tile['upgrade_cost'] ?? 100);
                                ?>
                                <div style="display:flex; gap:6px; width:100%;">
                                    <button class="btn-action-main btn-upgrade" style="flex:2;" onclick="openUpgradeModal(<?= $pending_tile['id'] ?>)">
                                        🏗️ BANGUN (<?= $cost ?> PR)
                                    </button>
                                    <button class="btn-action-main btn-skip" style="flex:1;" onclick="skipProperty()">
                                        ✕ Lewati
                                    </button>
                                </div>
                            <?php else: ?>
                                <button class="btn-action-main btn-endturn" onclick="endTurn()">
                                    ⏭️ SELESAI GILIRAN
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="center-waiting-badge">
                                <?= !empty($winner) ? '🏆 GAME TELAH BERAKHIR' : '<span>⏳</span> Giliran pemain lain melempar dadu...' ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Mini Marquee Log Ticker -->
                    <div class="center-logs-ticker" id="logs-ticker-wrap">
                        <?php if (!empty($state['logs'])): ?>
                            • <?= htmlspecialchars($state['logs'][0]['message'] ?? 'Permainan dimulai!') ?>
                        <?php else: ?>
                            • Permainan Skenalitik dibuka! Kocok dadu untuk mulai.
                        <?php endif; ?>
                    </div>
                </div> <!-- /.center-game-core -->

                <!-- 🎭 RIGHT PLAYER HUD WING (S+ CHARACTER CARDS) -->
                <div class="hud-player-wing hud-right" id="hud-player-right">
                    <?php foreach ($right_players as $p): ?>
                    <?php
                        $is_current = ((int)$state['game']['current_turn_user_id'] === (int)$p['user_id']);
                        $is_debt = ((int)$p['reputation_points'] < 0);
                        $is_in_jail = ($p['is_in_jail'] === true || (int)$p['is_in_jail'] === 1 || $p['is_in_jail'] === '1');
                        $is_elim = ($p['is_eliminated'] === true || (int)$p['is_eliminated'] === 1 || $p['is_eliminated'] === '1');
                    ?>
                    <div class="player-item p-user-order-<?= $p['turn_order'] ?> <?= $is_current ? 'is-active-turn' : '' ?> <?= $is_elim ? 'is-eliminated' : '' ?>">
                        <div class="p-token-avatar" title="<?= htmlspecialchars($p['token_character']) ?>">
                            <?= $p['token_icon'] ?>
                        </div>
                        <div class="p-meta">
                            <div class="p-name">
                                <span><?= htmlspecialchars($p['username']) ?></span>
                                <span style="font-size:0.62rem;opacity:0.8;font-weight:700;">(P<?= $p['turn_order'] ?>)</span>
                                <?= $is_current ? '🔥' : '' ?>
                            </div>
                            <div class="p-pr-balance <?= $is_debt ? 'in-debt' : '' ?>">
                                🟡 <?= number_format($p['reputation_points']) ?> PR
                                <?php if ($is_debt): ?>
                                <span style="font-size:0.55rem; color:#ef4444;">(UTANG)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($is_in_jail): ?>
                        <span class="p-badge badge-jail">Karantina</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Petak-petak 0 s/d 39 dirender via PHP & JS -->
            <?php
                // Grid Coordinate Mapping for Perimeter 11x11 (40 tiles)
                $coords = [
                    // Bawah: 10 ke 0
                    0  => [11, 11], 1 => [11, 10], 2 => [11, 9], 3 => [11, 8], 4 => [11, 7],
                    5  => [11, 6],  6 => [11, 5],  7 => [11, 4], 8 => [11, 3], 9 => [11, 2],
                    10 => [11, 1],
                    // Kiri: 11 ke 20
                    11 => [10, 1], 12 => [9, 1],  13 => [8, 1],  14 => [7, 1], 15 => [6, 1],
                    16 => [5, 1],  17 => [4, 1],  18 => [3, 1],  19 => [2, 1],
                    20 => [1, 1],
                    // Atas: 21 ke 30
                    21 => [1, 2],  22 => [1, 3],  23 => [1, 4],  24 => [1, 5], 25 => [1, 6],
                    26 => [1, 7],  27 => [1, 8],  28 => [1, 9],  29 => [1, 10],
                    30 => [1, 11],
                    // Kanan: 31 ke 39
                    31 => [2, 11], 32 => [3, 11], 33 => [4, 11], 34 => [5, 11], 35 => [6, 11],
                    36 => [7, 11], 37 => [8, 11], 38 => [9, 11], 39 => [10, 11],
                ];

                $tiles = $state['tiles_config'];
                $props = $state['properties'];
                $players = $state['players'];

                foreach ($coords as $tile_id => $coord):
                    $t = $tiles[$tile_id];
                    $p = $props[$tile_id] ?? null;
                    $row = $coord[0];
                    $col = $coord[1];
                    $is_corner = ($t['type'] === 'corner');

                    $corner_class = '';
                    if ($tile_id === 0)  $corner_class = 'tile-corner corner-start';
                    if ($tile_id === 10) $corner_class = 'tile-corner corner-jail';
                    if ($tile_id === 20) $corner_class = 'tile-corner corner-vip';
                    if ($tile_id === 30) $corner_class = 'tile-corner corner-skandal';

                    $is_owned = ($p && !empty($p['owner_user_id']));
                    $owner_order = $is_owned ? ((int)($p['owner_order'] ?? 1)) : null;
                    $owner_class = $is_owned ? "is-owned p-owner-{$owner_order}" : '';
            ?>
            <div class="tile <?= $corner_class ?> <?= $owner_class ?>"
                 id="tile-<?= $tile_id ?>"
                 data-tile-id="<?= $tile_id ?>"
                 data-owner-id="<?= $is_owned ? $p['owner_user_id'] : '' ?>"
                 data-owner-order="<?= $owner_order ?>"
                 style="grid-row: <?= $row ?>; grid-column: <?= $col ?>;"
                 onclick="inspectTile(<?= $tile_id ?>)">
                 
                <?php if (!empty($t['group']) && !in_array($t['group'], ['corner', 'tax', 'skena', 'politik'])): ?>
                <div class="tile-color-bar color-<?= $t['group'] ?>"></div>
                <?php endif; ?>

                <?php if ($is_owned): ?>
                <div class="tile-owner-seal" id="seal-tile-<?= $tile_id ?>" title="Milik: <?= htmlspecialchars($p['owner_name']) ?>">
                    <?= $p['owner_token'] ?? '⭐' ?>
                </div>
                <?php endif; ?>

                <?php if ($p && (int)$p['building_level'] > 0): ?>
                <?php
                    $lvl = (int)$p['building_level'];
                    $lvl_icon = ($lvl === 1) ? '🛖' : (($lvl === 2) ? '🏢' : '🏛️');
                ?>
                <span class="tile-building-badge level-<?= $lvl ?>" id="bld-tile-<?= $tile_id ?>" title="Tingkat Bangunan: Level <?= $lvl ?>">
                    <?= $lvl_icon ?> Lv <?= $lvl ?>
                </span>
                <?php endif; ?>

                <div class="tile-inner">
                    <span class="tile-name"><?= htmlspecialchars($t['name']) ?></span>
                    
                    <!-- THEMATIC GRAPHIC BADGE (GAMBAR PETAK) -->
                    <div class="tile-art-badge">
                        <span class="tile-art-icon"><?= $t['icon'] ?? '📍' ?></span>
                    </div>

                    <?php if ($is_owned): ?>
                    <div class="tile-owner-banner p-banner-<?= $owner_order ?>" id="owner-banner-tile-<?= $tile_id ?>" title="Milik: <?= htmlspecialchars($p['owner_name']) ?>">
                        <span><?= $p['owner_token'] ?? '⭐' ?></span>
                        <span><?= htmlspecialchars($p['owner_name']) ?></span>
                    </div>
                    <?php elseif ($t['price'] > 0): ?>
                    <span class="tile-price"><?= $t['price'] ?> PR</span>
                    <?php elseif (!empty($t['badge'])): ?>
                    <span class="tile-action-tag"><?= htmlspecialchars($t['badge']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Pion / Token yang sedang hinggap di petak ini -->
                <div class="pawns-container" id="pawns-tile-<?= $tile_id ?>">
                    <?php foreach ($players as $ply): ?>
                        <?php 
                            $p_elim = ($ply['is_eliminated'] === true || (int)$ply['is_eliminated'] === 1 || $ply['is_eliminated'] === '1');
                            $p_turn = ((int)$state['game']['current_turn_user_id'] === (int)$ply['user_id']);
                        ?>
                        <?php if ((int)$ply['position'] === (int)$tile_id && !$p_elim): ?>
                        <span class="pawn-icon pawn-user-<?= $ply['user_id'] ?> pawn-user-order-<?= $ply['turn_order'] ?> <?= ((int)$ply['user_id'] === $user_id) ? 'is-my-pawn' : '' ?> <?= $p_turn ? 'is-turn-pawn' : '' ?>" id="pawn-user-<?= $ply['user_id'] ?>" title="<?= htmlspecialchars($ply['username']) ?> (<?= $ply['token_character'] ?>)<?= $p_turn ? ' — Sedang Giliran' : '' ?>">
                            <?= $ply['token_icon'] ?>
                        </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 📖 MODAL POP-UP ATURAN MAIN & PANDUAN (ZINE EDITION) -->
<div class="modal-overlay" id="modal-rule-guide" style="display:none;" onclick="if(event.target===this)closeRuleModal()">
    <div class="modal-rule-box">
        <div class="modal-header">
            <h3>📖 PANDUAN LENGKAP: SKENALITIK</h3>
            <button class="btn-modal-close" onclick="closeRuleModal()">✕</button>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="rule-tabs">
            <button class="rule-tab-btn active" onclick="switchRuleTab('tab-tujuan', this)">🏆 Misi & Menang</button>
            <button class="rule-tab-btn" onclick="switchRuleTab('tab-dadu', this)">🎲 Dadu & Langkah</button>
            <button class="rule-tab-btn" onclick="switchRuleTab('tab-bangunan', this)">🏛️ Bangunan & Sewa</button>
            <button class="rule-tab-btn" onclick="switchRuleTab('tab-kartu', this)">🃏 Kartu Sakti</button>
            <button class="rule-tab-btn" onclick="switchRuleTab('tab-utang', this)">🚨 Utang & Eliminasi</button>
            <button class="rule-tab-btn" onclick="switchRuleTab('tab-karantina', this)">⚖️ Karantina Skandal</button>
        </div>

        <!-- TAB CONTENT AREA -->
        <div class="rule-tab-content">
            <!-- TAB 1 -->
            <div id="tab-tujuan" class="tab-pane">
                <h4>🎯 Selamat Datang di Arena Skenalitik!</h4>
                <p>Game papan satir yang mempertemukan skena kreatif Jakarta dengan oligarki politik kekuasaan. Siapa yang paling lihai memoles reputasi dan menyingkirkan rival akan menjadi penguasa panggung.</p>
                <br>
                <ul>
                    <li><b>Modal Awal:</b> Setiap pemain memulai perjalanan dengan modal <b>1.500 Poin Reputasi (PR)</b>.</li>
                    <li><b>Lewat START:</b> Ambil <b>+200 PR</b> (Dana Segar Proyek Komunitas) setiap kali bidak Anda melintasi Petak 0.</li>
                    <li><b>Syarat Menang:</b> Jadilah pemain terakhir yang bertahan di papan setelah seluruh lawan bangkrut (*Deplatformed*)!</li>
                </ul>
            </div>

            <!-- TAB 2 -->
            <div id="tab-dadu" class="tab-pane" style="display:none;">
                <h4>🎲 Dadu Ganda (2x D6) & Skandal Kembar</h4>
                <ul>
                    <li>Pemain melempar sepasang dadu standar (D6) setiap gilirannya.</li>
                    <li><b>Dadu Angka Kembar (*Double*):</b> Anda berhak melempar dadu sekali lagi (*bonus roll*).</li>
                    <li><b>Awas Skandal Kembar 3x:</b> Jika Anda melempar angka kembar 3 kali berturut-turut dalam satu giliran, Anda langsung dianggap curang dan digelandang paksa ke <b>Karantina Klarifikasi</b>!</li>
                </ul>
            </div>

            <!-- TAB 3 -->
            <div id="tab-bangunan" class="tab-pane" style="display:none;">
                <h4>🏛️ Hak Milik, Monopoli & Upgrade Bangunan</h4>
                <ul>
                    <li><b>Beli Petak Kosong:</b> Mendarat di properti tanpa pemilik? Beli sesuai harga tertera untuk memiliki tanah dasar.</li>
                    <li><b>Mendarat di Tanah Sendiri (Muter & Upgrade):</b> Setelah membeli tanah dan memutari papan, jika Anda mendarat kembali di petak milik Anda, Anda dapat membangun/upgrade bangunan bertahap:
                        <ul>
                            <li><b>Level 1 (Sablon Gelap):</b> Biaya upgrade sesuai tertera $\rightarrow$ Sewa melonjak <b>5x lipat</b> dari sewa dasar!</li>
                            <li><b>Level 2 (Ruang Kreatif Elit):</b> Bangunan Lv 1 $\rightarrow$ Sewa melonjak <b>15x lipat</b>!</li>
                            <li><b>Level 3 (Korporasi Media):</b> Tingkat maksimal $\rightarrow$ Sewa melonjak <b>40x lipat</b> (Mematikan saldo lawan seketika!).</li>
                        </ul>
                    </li>
                    <li><b>Monopoli Warna:</b> Kuasai seluruh petak dalam satu rumpun warna untuk melipatgandakan sewa tanah dasar (2x sewa dasar).</li>
                    <li><b>Transit (MRT / Stasiun / Bandara):</b> Sewa berlipat ganda sesuai jumlah stasiun yang Anda miliki (25 $\rightarrow$ 50 $\rightarrow$ 100 $\rightarrow$ 200 PR).</li>
                    <li><b>Utilitas (Genset / Pasukan Bot):</b> Sewa dihitung dari hasil dadu lawan (4x atau 10x angka dadu).</li>
                </ul>
            </div>

            <!-- TAB 4 -->
            <div id="tab-kartu" class="tab-pane" style="display:none;">
                <h4>🃏 Manuver Politik & Gimmick Skena</h4>
                <ul>
                    <li><b>Manuver Politik (20 Kartu):</b> Kartu licik kekuasaan (*Kudeta Aset*, *Gunting dalam Lipatan*, *OTT Rekayasa*, *Bansos Dobel*, dll).</li>
                    <li><b>Gimmick Skena (20 Kartu):</b> Realitas satir industri (*Bawa Kabur Dana Pre-Order*, *Gatekeeping Jalur Gig*, *Drama Settingan Twitter*, *Podcast Aib*, dll).</li>
                    <li>Kartu spesial seperti <b>Intervensi Hakim</b> disimpan di tangan dan bisa digunakan saat darurat untuk bebas dari Karantina!</li>
                </ul>
            </div>

            <!-- TAB 5 -->
            <div id="tab-utang" class="tab-pane" style="display:none;">
                <h4>🚨 Batas Utang & Eliminasi Brutal (*Deplatformed*)</h4>
                <ul>
                    <li><b>Toleransi Utang:</b> Jika kehabisan PR saat membayar sewa, saldo boleh minus hingga batas maksimal <b>-300 PR</b>. Layar akan menyala merah retak sebagai tanda bahaya.</li>
                    <li><b>DEPLATFORMED (-301 PR):</b> Jika di akhir giliran saldo Anda berada di angka <b>-301 PR atau lebih minus</b>, Anda langsung <b>TERELIMINASI</b>!</li>
                    <li>Seluruh properti pemain yang tereliminasi disita dan dilelang bebas ke pemain yang masih hidup.</li>
                </ul>
            </div>

            <!-- TAB 6 -->
            <div id="tab-karantina" class="tab-pane" style="display:none;">
                <h4>⚖️ Karantina Klarifikasi (Penjara)</h4>
                <ul>
                    <li><b>Penyebab Masuk:</b> Mendarat di petak nomor 30 (*Terjerat Skandal*), kartu OTT, atau dadu kembar 3x.</li>
                    <li><b>Status:</b> Bidak tidak bisa melangkah, tetapi properti Anda tetap bisa menarik uang sewa dari lawan.</li>
                    <li><b>3 Cara Bebas:</b>
                        <ol>
                            <li>Melempar dadu angka kembar di giliran berikutnya.</li>
                            <li>Membayar denda <b>150 PR</b> ke kas.</li>
                            <li>Memainkan kartu sakti <b>Intervensi Hakim</b>.</li>
                        </ol>
                    </li>
                    <li><i>Maksimal 3 putaran; jika belum bebas di putaran ke-3, wajib bayar denda 150 PR dan langsung dikeluarkan.</i></li>
                </ul>
            </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="modal-footer">
            <label class="chk-dont-show">
                <input type="checkbox" id="chk-never-show"> Jangan tampilkan otomatis lagi
            </label>
            <button class="btn-gas-play" onclick="closeRuleModal()">
                🚀 SAYA PAHAM, GAS MASUK PANGGUNG!
            </button>
        </div>
    </div>
</div>

<!-- 🏷️ MODAL DETAIL PETAK (INSPECTOR) -->
<div class="modal-overlay" id="modal-tile-inspect" style="display:none;" onclick="if(event.target===this)closeInspectModal()">
    <div class="deed-card-box" id="deed-card-content">
        <!-- Rendered dynamically -->
    </div>
</div>

<!-- 🏗️ MODAL POPUP UPGRADE PROPERTI (LINE GET RICH STYLE - ULTRA COMPACT) -->
<div class="modal-overlay" id="modal-upgrade-property" style="display:none;" onclick="if(event.target===this)closeUpgradeModal()">
    <div class="modal-upgrade-box" id="upgrade-modal-box">
        <div class="upg-header-row">
            <span class="upgrade-modal-top-tag">✨ HAK BANGUN PROPERTI ✨</span>
            <button type="button" class="upg-close-btn" onclick="skipUpgradeAction()" title="Tutup">✕</button>
        </div>

        <div class="upg-tile-header">
            <span class="upgrade-modal-icon-badge" id="upg-icon-badge">🍜</span>
            <div class="upgrade-modal-tile-name" id="upg-tile-name">Nama Properti</div>
        </div>

        <div style="font-size:0.62rem; color:#94a3b8; font-weight:700; margin:0;">PILIH TINGKAT BANGUNAN YANG INGIN DIDIRIKAN:</div>

        <div class="upgrade-levels-grid">
            <!-- Level 1 -->
            <div class="upg-level-card" id="upg-card-1" onclick="selectUpgradeLevel(1)">
                <div class="upg-card-top">
                    <span class="upg-card-icon">🛖</span>
                    <span class="upg-card-badge">Lv 1</span>
                </div>
                <div class="upg-card-name">Sablon Gelap</div>
                <div class="upg-card-cost" id="upg-card-cost-1">50 PR</div>
                <div class="upg-card-status" id="upg-card-rent-1">Sewa: 10 PR</div>
            </div>

            <!-- Level 2 -->
            <div class="upg-level-card" id="upg-card-2" onclick="selectUpgradeLevel(2)">
                <div class="upg-card-top">
                    <span class="upg-card-icon">🏢</span>
                    <span class="upg-card-badge">Lv 2</span>
                </div>
                <div class="upg-card-name">Ruang Elit</div>
                <div class="upg-card-cost" id="upg-card-cost-2">100 PR</div>
                <div class="upg-card-status" id="upg-card-rent-2">Sewa: 30 PR</div>
            </div>

            <!-- Level 3 -->
            <div class="upg-level-card" id="upg-card-3" onclick="selectUpgradeLevel(3)">
                <div class="upg-card-top">
                    <span class="upg-card-icon">🏛️</span>
                    <span class="upg-card-badge">Lv 3</span>
                </div>
                <div class="upg-card-name">Korporasi Media</div>
                <div class="upg-card-cost" id="upg-card-cost-3">150 PR</div>
                <div class="upg-card-status" id="upg-card-rent-3">Sewa: 80 PR</div>
            </div>
        </div>

        <div class="upgrade-stats-card">
            <div class="upg-stat-item">
                <span class="upg-stat-label">🎯 Pilihan:</span>
                <span class="upg-stat-val" id="upg-selected-summary" style="color:#6ee7b7;">Level 1</span>
            </div>
            <div class="upg-stat-item">
                <span class="upg-stat-label">🪙 Biaya PR:</span>
                <span class="upg-stat-val upg-val-cost" id="upg-cost-text">50 PR</span>
            </div>
            <div class="upg-stat-item">
                <span class="upg-stat-label">📈 Sewa Baru:</span>
                <span class="upg-stat-val upg-val-rent" id="upg-rent-text">10 PR</span>
            </div>
            <div class="upg-stat-item">
                <span class="upg-stat-label">🟡 Sisa Saldo:</span>
                <span class="upg-stat-val upg-val-balance" id="upg-balance-text">1658 PR</span>
            </div>
        </div>

        <div class="upgrade-modal-buttons">
            <button type="button" class="btn-upgrade-confirm" id="btn-upgrade-confirm-action" onclick="confirmUpgradeAction()">
                🏗️ BANGUN LEVEL 1 (50 PR)
            </button>
            <button type="button" class="btn-upgrade-skip" onclick="skipUpgradeAction()">
                ✕ Lewati
            </button>
        </div>
    </div>
</div>

<!-- 🏳️ MODAL KONFIRMASI MENYERAH -->
<div class="modal-overlay" id="modal-surrender" style="display:none;" onclick="if(event.target===this)closeSurrenderModal()">
    <div class="modal-rule-box" style="width:min(90vw, 420px); text-align:center; padding:20px;">
        <div style="font-size:3.2rem; margin-bottom:8px;">🏳️</div>
        <div style="font-family:'Space Grotesk',sans-serif; font-size:1.25rem; font-weight:900; color:#dc2626; margin-bottom:8px;">
            MENYERAH DARI PERMAINAN?
        </div>
        <div style="font-size:0.82rem; color:#475569; line-height:1.5; margin-bottom:18px; background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:12px; text-align:left;">
            <p style="margin-bottom:8px; font-weight:800; color:#991b1b;">⚠️ Konfirmasi keluar dari panggung:</p>
            <ul style="padding-left:18px; margin:0; display:flex; flex-direction:column; gap:6px;">
                <li>Anda akan keluar dari game dan dialihkan langsung kembali ke <b>Lobby</b>.</li>
                <li>Seluruh properti & kartu yang Anda miliki akan dibebaskan.</li>
                <li><b>Jika bermain 2 orang:</b> Game langsung berakhir dan lawan dinyatakan menang.</li>
                <li><b>Jika bermain 3 orang atau lebih:</b> Game tetap berlanjut antara pemain yang tersisa.</li>
            </ul>
        </div>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button type="button" class="btn-action-main" style="flex:1; background:#94a3b8; color:#fff;" onclick="closeSurrenderModal()">
                ❌ Batal
            </button>
            <button type="button" id="btn-confirm-surrender" class="btn-action-main" style="flex:1; background:linear-gradient(135deg, #ef4444, #b91c1c); color:#fff; box-shadow:0 4px 12px rgba(239,68,68,0.35);" onclick="confirmSurrender()">
                🏳️ Ya, Menyerah
            </button>
        </div>
    </div>
</div>

<!-- 🏆 MODAL KEMENANGAN / GAME SELESAI -->
<div class="modal-overlay" id="modal-victory" style="display:none;">
    <div class="modal-rule-box" id="victory-modal-box" style="width:min(90vw, 420px); text-align:center; padding:24px; transition:all 0.3s ease;">
        <div id="victory-icon" style="font-size:3.8rem; margin-bottom:8px;">🏆</div>
        <div id="victory-title" style="font-family:'Space Grotesk',sans-serif; font-size:1.35rem; font-weight:900; color:#d97706; margin-bottom:4px;">
            PERMAINAN SELESAI!
        </div>
        <div id="victory-winner-name" style="font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:10px;">
            Pemenang: [Nama]
        </div>
        <div id="victory-desc" style="font-size:0.82rem; color:#475569; line-height:1.5; margin-bottom:18px; background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:12px;">
            Selamat atas kemenangan tahta dan panggung Skenalitik!
        </div>
        <button type="button" class="btn-action-main" id="btn-victory-back" style="width:100%; background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff; font-size:0.9rem;" onclick="window.location.href='<?= site_url('lobby') ?>'">
            🏠 Kembali ke Lobby
        </button>
    </div>
</div>

<!-- 🃏 MODAL KARTU TERBUKA (CARD REVEAL POPUP) -->
<div class="modal-overlay" id="modal-card-drawn" style="display:none;" onclick="if(event.target===this)closeCardDrawnModal()">
    <div class="card-drawn-box type-politik" id="card-reveal-box">
        <span class="card-reveal-badge type-politik" id="card-reveal-badge">🏛️ MANUVER POLITIK</span>
        <div class="card-reveal-icon" id="card-reveal-icon">⚖️</div>
        <div class="card-reveal-title" id="card-reveal-title">Intervensi Hakim</div>
        <div class="card-reveal-desc" id="card-reveal-desc">Bebaskan diri Anda dari petak Karantina Klarifikasi tanpa syarat. Simpan kartu ini.</div>
        <div id="card-reveal-footer">
            <span class="badge-card-kept">📥 Disimpan di Saku Anda!</span>
        </div>
        <button type="button" class="btn-action-main btn-roll" style="width:100%; margin-top:8px; font-size:0.9rem;" onclick="closeCardDrawnModal()">
            ⚡ Mengerti & Lanjutkan
        </button>
    </div>
</div>

<!-- 🎴 MODAL KOLEKSI KARTU DI TANGAN (MY HAND CARDS) -->
<div class="modal-overlay" id="modal-my-cards" style="display:none;" onclick="if(event.target===this)closeHandCardsModal()">
    <div class="modal-rule-box" style="width:min(92vw, 440px); padding:20px;">
        <div class="modal-header">
            <div style="font-size:1.15rem; font-weight:900; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <span>🃏</span> <span>Kartu Sakti di Tangan</span>
            </div>
            <button class="btn-modal-close" onclick="closeHandCardsModal()">✕</button>
        </div>
        <div id="hand-cards-list" style="max-height:60vh; overflow-y:auto; padding:8px 0;">
            <!-- Rendered via JS -->
        </div>
        <div class="modal-footer" style="margin-top:12px; padding-top:12px; border-top:1px solid #e2e8f0;">
            <button type="button" class="btn-action-main" style="width:100%; background:#64748b; color:#fff;" onclick="closeHandCardsModal()">
                Tutup Saku Kartu
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL   = '<?= site_url() ?>';
const ROOM_CODE  = '<?= $room_code ?>';
const USER_ID    = <?= $user_id ?>;
let clientVersion = <?= (int)$version ?>;
let isMyTurn         = <?= $is_my_turn ? 'true' : 'false' ?>;
let tilesConfig       = <?= json_encode($state['tiles_config']) ?>;
let currentProperties = <?= json_encode($state['properties']) ?>;
let currentPlayers    = <?= json_encode($state['players']) ?>;
let currentMyCards    = <?= json_encode($state['my_cards'] ?? []) ?>;
let soundEnabled      = true;

// Posisi bidak tiap pemain yang terlacak di memori client
let playerPositions = {};
let isWalking = false;

// Inisialisasi posisi awal pemain
<?php foreach ($state['players'] as $ply): ?>
playerPositions[<?= (int)$ply['user_id'] ?>] = <?= (int)$ply['position'] ?>;
<?php endforeach; ?>

// ── Web Audio API Procedural Synthesizer (Instant SFX) ────────────
let audioCtx = null;
function getAudioContext() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

function playSound(type) {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;

        if (type === 'dice') {
            // Paper cup dice rattle
            for (let i = 0; i < 4; i++) {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(140 + Math.random() * 80, now + i * 0.05);
                gain.gain.setValueAtTime(0.3, now + i * 0.05);
                gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.05 + 0.06);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now + i * 0.05);
                osc.stop(now + i * 0.05 + 0.06);
            }
        } else if (type === 'step') {
            // 808 kick drum hop per petak
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.frequency.setValueAtTime(160, now);
            osc.frequency.exponentialRampToValueAtTime(45, now + 0.08);
            gain.gain.setValueAtTime(0.45, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.08);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.08);
        } else if (type === 'buy') {
            // Gavel wood clack & spray fizz
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'square';
            osc.frequency.setValueAtTime(320, now);
            osc.frequency.exponentialRampToValueAtTime(100, now + 0.12);
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.12);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.12);
        } else if (type === 'rent') {
            // Error buzz tone
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(120, now);
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.25);
        } else if (type === 'start') {
            // Major power chord fanfare
            [261.63, 329.63, 392.00, 523.25].forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now + idx * 0.04);
                gain.gain.setValueAtTime(0.2, now + idx * 0.04);
                gain.gain.exponentialRampToValueAtTime(0.01, now + idx * 0.04 + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now + idx * 0.04);
                osc.stop(now + idx * 0.04 + 0.35);
            });
        }
    } catch (e) {
        // Audio fallback
    }
}

function toggleSound() {
    soundEnabled = !soundEnabled;
    document.getElementById('btn-sound-toggle').textContent = soundEnabled ? '🔊' : '🔇';
}

// ── Fullscreen & Orientation Lock ─────────────────────────────────
function enableLandscape() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(() => {});
    }
    if (screen.orientation && screen.orientation.lock) {
        screen.orientation.lock('landscape').catch(() => {});
    }
    document.getElementById('portrait-shield').style.display = 'none';
}

function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        if (document.exitFullscreen) document.exitFullscreen();
    }
}

// ── Modal Aturan Main (Zine Edition) ──────────────────────────────
function openRuleModal() {
    document.getElementById('modal-rule-guide').style.display = 'flex';
}

function closeRuleModal() {
    const chk = document.getElementById('chk-never-show');
    if (chk && chk.checked) {
        localStorage.setItem('skenalitik_rule_seen', 'true');
    }
    document.getElementById('modal-rule-guide').style.display = 'none';
}

function switchRuleTab(tabId, btn) {
    document.querySelectorAll('.rule-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
    document.getElementById(tabId).style.display = 'block';
}

// Tampilkan Otomatis untuk Pemain Baru & Auto-Popup Upgrade jika ada aksi tertunda
window.addEventListener('DOMContentLoaded', () => {
    const seen = localStorage.getItem('skenalitik_rule_seen');
    if (!seen) {
        openRuleModal();
    }
    <?php if (($state['game']['pending_action'] ?? '') === 'upgrade_property' && $is_my_turn): ?>
    setTimeout(() => {
        openUpgradeModal(<?= (int)$state['game']['pending_tile_id'] ?>);
    }, 250);
    <?php endif; ?>
});

// ── Tile Inspector Popup ──────────────────────────────────────────
function inspectTile(tileId) {
    const tile = tilesConfig[tileId];
    if (!tile) return;

    const prop = (currentProperties && currentProperties[tileId]) ? currentProperties[tileId] : null;

    let html = `
        <div style="border-bottom:2px solid #f59e0b;padding-bottom:8px;margin-bottom:10px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:4px;">${tile.icon || '📍'}</div>
            <div style="font-size:0.65rem;color:#b45309;font-weight:800;letter-spacing:1px;text-transform:uppercase;">SERTIFIKAT HAK MILIK</div>
            <h3 style="font-size:1.1rem;font-weight:900;color:#0f172a;">${tile.name}</h3>
        </div>
    `;

    // Kartu Status Kepemilikan yang sangat jelas
    if (tile.type === 'property' || tile.type === 'transit' || tile.type === 'utilitas') {
        if (prop && prop.owner_user_id) {
            const lvl = parseInt(prop.building_level || 0);
            const lvlLabel = (lvl === 1) ? '🛖 Sablon Gelap (Lv 1)' : ((lvl === 2) ? '🏢 Ruang Elit (Lv 2)' : ((lvl === 3) ? '🏛️ Korporasi Media (Lv 3)' : 'Tanah Dasar (Belum Diupgrade)'));
            html += `
                <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:8px 12px;margin-bottom:10px;text-align:center;">
                    <div style="font-size:0.62rem;color:#166534;font-weight:800;letter-spacing:1px;text-transform:uppercase;">PEMILIK RESMI PETAK INI</div>
                    <div style="font-size:1rem;font-weight:900;color:#0f172a;margin-top:3px;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <span style="font-size:1.25rem;">${prop.owner_token || '⭐'}</span> <span>${prop.owner_name}</span>
                    </div>
                    <div style="font-size:0.72rem;color:#059669;font-weight:700;margin-top:3px;">
                        Status Bangunan: <b>${lvlLabel}</b>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div style="background:#f8fafc;border:1.5px solid #cbd5e1;border-radius:10px;padding:8px 12px;margin-bottom:10px;text-align:center;">
                    <div style="font-size:0.62rem;color:#64748b;font-weight:800;letter-spacing:1px;text-transform:uppercase;">STATUS KEPEMILIKAN</div>
                    <div style="font-size:0.9rem;font-weight:900;color:#0f172a;margin-top:2px;">🟢 BELUM DIMILIKI (BEBAS)</div>
                    <div style="font-size:0.72rem;color:#0284c7;font-weight:700;margin-top:2px;">Bisa dibeli seharga ${tile.price} PR saat Anda mendarat</div>
                </div>
            `;
        }
    }

    if (tile.type === 'property') {
        const base = parseInt(tile.rent);
        html += `
            <div style="font-size:0.75rem;line-height:1.6;color:#1e293b;">
                <div>Harga Beli: <b style="color:#059669;">${tile.price} PR</b></div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:6px 0;">
                <div>Sewa Dasar: <b>${base} PR</b></div>
                <div>Sewa Monopoli Warna: <b>${base * 2} PR</b></div>
                <div style="color:#d97706;font-weight:700;">Sewa Lv 1 (Sablon Gelap): <b>${base * 5} PR</b></div>
                <div style="color:#0284c7;font-weight:700;">Sewa Lv 2 (Ruang Kreatif Elit): <b>${base * 15} PR</b></div>
                <div style="color:#db2777;font-weight:800;">Sewa Lv 3 (Korporasi Media): <b>${base * 40} PR</b></div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:6px 0;">
                <div style="font-size:0.7rem;color:#64748b;">Biaya Bangun per Level: ${tile.upgrade_cost} PR</div>
            </div>
        `;
    } else if (tile.type === 'transit') {
        html += `
            <div style="font-size:0.75rem;line-height:1.6;color:#1e293b;">
                <div>Harga Beli: <b style="color:#059669;">${tile.price} PR</b></div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:6px 0;">
                <div>1 Stasiun: <b>25 PR</b></div>
                <div>2 Stasiun: <b>50 PR</b></div>
                <div>3 Stasiun: <b>100 PR</b></div>
                <div style="color:#7c3aed;font-weight:800;">4 Stasiun: <b>200 PR</b></div>
            </div>
        `;
    } else {
        html += `<div style="font-size:0.75rem;color:#475569;line-height:1.4;">${tile.desc || 'Petak Khusus'}</div>`;
    }

    if (tile.type === 'property' && prop && parseInt(prop.owner_user_id) === USER_ID) {
        const lvl = parseInt(prop.building_level || 0);
        if (lvl < 3 && isMyTurn && parseInt(playerPositions[USER_ID]) === parseInt(tileId)) {
            const nextLvl = lvl + 1;
            const lvlNames = {1: 'Level 1 (Sablon Gelap)', 2: 'Level 2 (Ruang Kreatif Elit)', 3: 'Level 3 (Korporasi Media)'};
            html += `<button onclick="upgradeProperty(${tileId})" style="width:100%;margin-top:10px;background:linear-gradient(135deg, #10b981, #059669);color:#ffffff;border:none;padding:10px;border-radius:8px;font-weight:900;font-size:0.8rem;cursor:pointer;box-shadow:0 3px 0 #047857;">🏗️ Bangun ${lvlNames[nextLvl] || 'Level ' + nextLvl} (${tile.upgrade_cost} PR)</button>`;
        }
    }

    html += `<button onclick="closeInspectModal()" style="width:100%;margin-top:12px;background:#f1f5f9;color:#0f172a;border:1px solid #cbd5e1;padding:8px;border-radius:8px;font-weight:700;font-size:0.75rem;cursor:pointer;">Tutup</button>`;

    document.getElementById('deed-card-content').innerHTML = html;
    document.getElementById('modal-tile-inspect').style.display = 'flex';
}

function closeInspectModal() {
    document.getElementById('modal-tile-inspect').style.display = 'none';
}

// ── BANNER CLOUT SAAT MELINTAS START ──────────────────────────────
function triggerCloutBanner() {
    const centerBoard = document.querySelector('.board-center');
    if (!centerBoard) return;
    const banner = document.createElement('div');
    banner.className = 'floating-clout-banner';
    banner.innerHTML = '⚡ +200 CLOUT!';
    centerBoard.appendChild(banner);
    setTimeout(() => banner.remove(), 1400);
}

// ── ENGINE ANIMASI MELANGKAH PER PETAK (HOPPING WALK) ──────────────
function animateWalk(userId, targetPos, onComplete) {
    targetPos = parseInt(targetPos);
    const fromPos = playerPositions[userId] !== undefined ? parseInt(playerPositions[userId]) : 0;
    if (isNaN(targetPos) || fromPos === targetPos) {
        if (onComplete) onComplete();
        return;
    }

    // Bangun jalur langkah per petak (dengan batas maksimum agar aman dari infinite loop)
    let path = [];
    let curr = fromPos;
    let guard = 0;
    while (curr !== targetPos && guard < 45) {
        curr = (curr + 1) % 40;
        path.push(curr);
        guard++;
    }

    isWalking = true;
    let stepIndex = 0;

    function step() {
        if (stepIndex >= path.length) {
            // Sampai di petak tujuan!
            playerPositions[userId] = targetPos;
            isWalking = false;

            // Efek hentakan mendarat di papan
            const targetTileEl = document.getElementById(`tile-${targetPos}`);
            if (targetTileEl) {
                targetTileEl.classList.add('tile-landed-glow');
                setTimeout(() => targetTileEl.classList.remove('tile-landed-glow'), 600);
            }
            const boardEl = document.getElementById('skena-board-grid');
            if (boardEl) {
                boardEl.classList.add('board-landing-thud');
                setTimeout(() => boardEl.classList.remove('board-landing-thud'), 250);
            }

            if (onComplete) onComplete();
            return;
        }

        const nextTileId = path[stepIndex];
        const prevTileId = (stepIndex === 0) ? fromPos : path[stepIndex - 1];

        // Pindahkan elemen pion di DOM
        movePawnInDOM(userId, prevTileId, nextTileId);

        // Mainkan SFX langkah kick drum per petak
        playSound('step');

        // Jika melintasi petak START (0)
        if (nextTileId === 0) {
            playSound('start');
            triggerCloutBanner();
        }

        // Highlight petak yang diinjak sekejap
        const tileEl = document.getElementById(`tile-${nextTileId}`);
        if (tileEl) {
            tileEl.classList.add('tile-step-glow');
            setTimeout(() => tileEl.classList.remove('tile-step-glow'), 220);
        }

        stepIndex++;
        setTimeout(step, 160); // Ritme langkah 160ms per petak
    }

    step();
}

function movePawnInDOM(userId, fromTileId, toTileId) {
    const toWrap = document.getElementById(`pawns-tile-${toTileId}`);
    if (!toWrap) return;

    let pawnEl = document.getElementById(`pawn-user-${userId}`);
    if (!pawnEl) {
        pawnEl = document.querySelector(`.pawn-user-${userId}`);
    }

    if (pawnEl) {
        toWrap.appendChild(pawnEl);
        pawnEl.classList.remove('pawn-hopping');
        void pawnEl.offsetWidth; // trigger reflow
        pawnEl.classList.add('pawn-hopping');
    }
}

// ── Game Actions API ──────────────────────────────────────────────
async function rollDice() {
    playSound('dice');
    const btn = document.getElementById('btn-roll-dice');
    if (btn) btn.disabled = true;

    // Animasi kocok dadu berputar 3D
    const d1El = document.getElementById('cube-d1');
    const d2El = document.getElementById('cube-d2');
    d1El.classList.add('dice-rolling');
    d2El.classList.add('dice-rolling');

    const cupEl = document.getElementById('dice-cup');
    if (cupEl) cupEl.style.transform = 'rotate(-20deg) scale(1.15)';

    // Angka dadu acak berputar cepat selama 500ms
    let rollInterval = setInterval(() => {
        d1El.textContent = Math.floor(Math.random() * 6) + 1;
        d2El.textContent = Math.floor(Math.random() * 6) + 1;
    }, 50);

    try {
        const formData = new FormData();
        formData.append('room_code', ROOM_CODE);

        const res = await fetch(BASE_URL + 'skenalitik/aksi_kocok', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const resText = await res.text();
        let data;
        try {
            data = JSON.parse(resText);
        } catch (parseErr) {
            console.error('Non-JSON server response:', resText);
            clearInterval(rollInterval);
            d1El.classList.remove('dice-rolling');
            d2El.classList.remove('dice-rolling');
            if (cupEl) cupEl.style.transform = 'none';
            alert('Gagal memproses data kocok dadu dari server.');
            if (btn) btn.disabled = false;
            return;
        }

        setTimeout(() => {
            clearInterval(rollInterval);
            d1El.classList.remove('dice-rolling');
            d2El.classList.remove('dice-rolling');
            if (cupEl) cupEl.style.transform = 'none';

            if (data.ok) {
                d1El.textContent = data.d1;
                d2El.textContent = data.d2;

                if (data.in_jail) {
                    if (data.state) applyState(data.state, true);
                    return;
                }

                // Animasi melangkah bidak per petak secara berurutan!
                animateWalk(USER_ID, data.new_pos, () => {
                    if (data.state) applyState(data.state, true);
                    if (data.drawn_card) {
                        showCardDrawnModal(data.drawn_card);
                    }
                });
            } else {
                alert(data.message || 'Gagal melempar dadu');
                if (btn) btn.disabled = false;
            }
        }, 500);

    } catch (e) {
        clearInterval(rollInterval);
        d1El.classList.remove('dice-rolling');
        d2El.classList.remove('dice-rolling');
        if (cupEl) cupEl.style.transform = 'none';
        console.error(e);
        if (btn) btn.disabled = false;
    }
}

async function buyProperty() {
    playSound('buy');
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);

    const res = await fetch(BASE_URL + 'skenalitik/aksi_beli', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
    } else {
        alert(data.message || 'Gagal membeli properti');
    }
}

let currentUpgradeTileId = null;
let currentSelectedLevel = 1;

function selectUpgradeLevel(lvl) {
    lvl = parseInt(lvl);
    const cardEl = document.getElementById(`upg-card-${lvl}`);
    if (!cardEl || cardEl.classList.contains('is-already-built')) return;

    currentSelectedLevel = lvl;
    updateUpgradeModalDisplay();
}

function updateUpgradeModalDisplay() {
    if (currentUpgradeTileId === null) return;
    const tile = tilesConfig ? tilesConfig[currentUpgradeTileId] : null;
    if (!tile) return;

    const prop = (currentProperties && currentProperties[currentUpgradeTileId]) ? currentProperties[currentUpgradeTileId] : null;
    const curLvl = parseInt(prop ? prop.building_level : 0);

    const costPerLevel = parseInt(tile.upgrade_cost || 100);
    const baseRent = parseInt(tile.rent || 0);
    const rentMults = { 0: 1, 1: 5, 2: 15, 3: 40 };
    const curRent = baseRent * (rentMults[curLvl] || 1);

    const lvlNames = {
        1: 'Level 1 (Sablon Gelap)',
        2: 'Level 2 (Ruang Kreatif Elit)',
        3: 'Level 3 (Korporasi Media)'
    };

    // Update status & biaya setiap kartu pilihan
    [1, 2, 3].forEach(lvl => {
        const card = document.getElementById(`upg-card-${lvl}`);
        const costLabel = document.getElementById(`upg-card-cost-${lvl}`);
        const rentLabel = document.getElementById(`upg-card-rent-${lvl}`);
        if (!card) return;

        card.classList.remove('is-selected', 'is-already-built');

        const lvlRent = baseRent * (rentMults[lvl] || 1);
        if (rentLabel) rentLabel.textContent = `Sewa: ${lvlRent} PR`;

        if (lvl <= curLvl) {
            card.classList.add('is-already-built');
            if (costLabel) costLabel.innerHTML = '✅ Terpasang';
        } else {
            const addedLevels = lvl - curLvl;
            const lvlCost = addedLevels * costPerLevel;
            if (costLabel) costLabel.textContent = `🪙 ${lvlCost} PR`;

            if (lvl === currentSelectedLevel) {
                card.classList.add('is-selected');
            }
        }
    });

    // Kalkulasi biaya untuk level yang dipilih saat ini
    const addedLevels = Math.max(1, currentSelectedLevel - curLvl);
    const totalCost = addedLevels * costPerLevel;
    const newRent = baseRent * (rentMults[currentSelectedLevel] || 1);

    const me = (currentPlayers || []).find(p => parseInt(p.user_id) === USER_ID);
    const myPR = me ? parseInt(me.reputation_points || 0) : 0;
    const sisaPR = myPR - totalCost;

    const summaryEl = document.getElementById('upg-selected-summary');
    const costEl = document.getElementById('upg-cost-text');
    const rentEl = document.getElementById('upg-rent-text');
    const balanceEl = document.getElementById('upg-balance-text');
    const btnAction = document.getElementById('btn-upgrade-confirm-action');

    if (summaryEl) summaryEl.textContent = `Level ${currentSelectedLevel}`;
    if (costEl) costEl.textContent = `${totalCost} PR (${addedLevels} Tingkat)`;
    if (rentEl) rentEl.innerHTML = `<b style="color:#10b981;">${newRent} PR (${rentMults[currentSelectedLevel]}x)</b>`;
    if (balanceEl) balanceEl.innerHTML = `Sisa <b>${sisaPR} PR</b>`;
    if (btnAction) {
        btnAction.textContent = `🏗️ BANGUN LEVEL ${currentSelectedLevel} (${totalCost} PR)`;
    }
}

function openUpgradeModal(tileId) {
    tileId = parseInt(tileId);
    currentUpgradeTileId = tileId;
    const tile = tilesConfig ? tilesConfig[tileId] : null;
    if (!tile) return;

    const prop = (currentProperties && currentProperties[tileId]) ? currentProperties[tileId] : null;
    const curLvl = parseInt(prop ? prop.building_level : 0);

    // Default pilih level berikutnya
    currentSelectedLevel = Math.min(3, curLvl + 1);

    const iconEl = document.getElementById('upg-icon-badge');
    const nameEl = document.getElementById('upg-tile-name');
    if (iconEl) iconEl.textContent = tile.icon || '🍜';
    if (nameEl) nameEl.textContent = tile.name;

    updateUpgradeModalDisplay();

    const modal = document.getElementById('modal-upgrade-property');
    if (modal) modal.style.display = 'flex';
}

function closeUpgradeModal() {
    const modal = document.getElementById('modal-upgrade-property');
    if (modal) modal.style.display = 'none';
}

async function confirmUpgradeAction() {
    if (currentUpgradeTileId !== null && currentSelectedLevel !== null) {
        closeUpgradeModal();
        await upgradeProperty(currentUpgradeTileId, currentSelectedLevel);
    }
}

async function skipUpgradeAction() {
    closeUpgradeModal();
    await skipProperty();
}

async function upgradeProperty(tileId, targetLevel = 0) {
    playSound('buy');
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);
    formData.append('tile_id', tileId);
    if (targetLevel > 0) {
        formData.append('target_level', targetLevel);
    }

    const res = await fetch(BASE_URL + 'skenalitik/aksi_upgrade', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
        closeInspectModal();
        closeUpgradeModal();
    } else {
        alert(data.message || 'Gagal melakukan upgrade bangunan');
    }
}

async function skipProperty() {
    closeUpgradeModal();
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);

    const res = await fetch(BASE_URL + 'skenalitik/aksi_lewat', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
    }
}

async function endTurn() {
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);

    const res = await fetch(BASE_URL + 'skenalitik/aksi_selesai_giliran', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
    }
}

async function payJailFine() {
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);
    formData.append('method', 'fine');

    const res = await fetch(BASE_URL + 'skenalitik/aksi_karantina', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
    } else {
        alert(data.message || 'Gagal membayar denda');
    }
}

// ── Kartu Tangan & Card Reveal Actions ────────────────────────────
function openHandCardsModal() {
    renderHandCardsList();
    document.getElementById('modal-my-cards').style.display = 'flex';
}

function closeHandCardsModal() {
    document.getElementById('modal-my-cards').style.display = 'none';
}

function renderHandCardsList() {
    const listEl = document.getElementById('hand-cards-list');
    if (!listEl) return;

    if (!currentMyCards || currentMyCards.length === 0) {
        listEl.innerHTML = `
            <div style="text-align:center; padding:24px 12px; color:#64748b; font-size:0.85rem; line-height:1.5;">
                <div style="font-size:2.5rem; margin-bottom:8px;">📭</div>
                <b>Saku Kartu Kosong</b><br>
                Anda belum memiliki kartu yang disimpan di tangan.<br>
                Mendaratlah di petak <b>Manuver Politik</b> untuk meraih kartu sakti seperti <i>Intervensi Hakim</i>!
            </div>
        `;
        return;
    }

    listEl.innerHTML = currentMyCards.map(c => {
        const isHakim = (c.deck_type === 'politik' && parseInt(c.card_index) === 8);
        const isSaksi = (c.deck_type === 'politik' && parseInt(c.card_index) === 18);
        const me = (currentPlayers || []).find(p => parseInt(p.user_id) === USER_ID);
        const inJail = (me && (me.is_in_jail === true || me.is_in_jail === 1 || me.is_in_jail === '1'));

        let btnAction = '';
        if (isHakim) {
            btnAction = inJail
                ? `<button onclick="useJailCard()" style="background:linear-gradient(135deg, #f59e0b, #d97706);color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:0.75rem;font-weight:900;cursor:pointer;">⚖️ Bebas Sekarang</button>`
                : `<span style="font-size:0.7rem;color:#10b981;font-weight:700;">Siap dipakai saat di Karantina</span>`;
        } else if (isSaksi) {
            btnAction = `<button onclick="playHandCard(${c.id})" style="background:linear-gradient(135deg, #8b5cf6, #6d28d9);color:#fff;border:none;padding:6px 12px;border-radius:6px;font-size:0.75rem;font-weight:900;cursor:pointer;">🛡️ Aktifkan (+200 PR)</button>`;
        }

        return `
            <div class="hand-card-item type-${c.deck_type}">
                <div class="hand-card-info">
                    <div class="hand-card-name">
                        <span>${c.icon || '🃏'}</span>
                        <span>${c.title}</span>
                    </div>
                    <div class="hand-card-desc">${c.desc}</div>
                </div>
                ${btnAction ? `<div>${btnAction}</div>` : ''}
            </div>
        `;
    }).join('');
}

function showCardDrawnModal(card) {
    if (!card) return;
    playSound(card.deck_type === 'politik' ? 'buy' : 'step');
    const modal = document.getElementById('modal-card-drawn');
    if (!modal) return;

    const box = document.getElementById('card-reveal-box');
    const badge = document.getElementById('card-reveal-badge');
    const icon = document.getElementById('card-reveal-icon');
    const title = document.getElementById('card-reveal-title');
    const desc = document.getElementById('card-reveal-desc');
    const footer = document.getElementById('card-reveal-footer');

    if (box) box.className = `card-drawn-box type-${card.deck_type}`;
    if (badge) {
        badge.textContent = card.deck_type === 'politik' ? '🏛️ MANUVER POLITIK' : '⚡ GIMMICK SKENA';
        badge.className = `card-reveal-badge type-${card.deck_type}`;
    }
    if (icon) icon.textContent = card.icon || (card.deck_type === 'politik' ? '🏛️' : '⚡');
    if (title) title.textContent = card.title;
    if (desc) desc.textContent = card.desc;
    if (footer) {
        if (card.is_kept) {
            footer.innerHTML = `<span class="badge-card-kept">📥 Disimpan di Saku Anda! Buka menu Kartu Sakti untuk menggunakannya.</span>`;
        } else {
            footer.innerHTML = `<span class="badge-card-instant">⚡ Efek Kartu Langsung Diterapkan ke Papan Permainan!</span>`;
        }
    }
    modal.style.display = 'flex';
}

function closeCardDrawnModal() {
    const modal = document.getElementById('modal-card-drawn');
    if (modal) modal.style.display = 'none';
}

async function useJailCard() {
    playSound('start');
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);
    formData.append('method', 'card');

    const res = await fetch(BASE_URL + 'skenalitik/aksi_karantina', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
        closeHandCardsModal();
        alert(data.message || 'Bebas dari Karantina menggunakan kartu Intervensi Hakim!');
    } else {
        alert(data.message || 'Gagal menggunakan kartu');
    }
}

async function playHandCard(cardId) {
    const formData = new FormData();
    formData.append('room_code', ROOM_CODE);
    formData.append('card_id', cardId);

    const res = await fetch(BASE_URL + 'skenalitik/aksi_pakai_kartu', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.ok && data.state) {
        applyState(data.state, false);
        closeHandCardsModal();
        alert(data.message || 'Kartu berhasil diaktifkan!');
    } else {
        alert(data.message || 'Gagal mengaktifkan kartu');
    }
}

// ── Realtime Polling Loop ─────────────────────────────────────────
setInterval(async () => {
    try {
        const res = await fetch(`${BASE_URL}skenalitik/poll/${ROOM_CODE}?v=${clientVersion}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            return;
        }

        if (data && data.updated) {
            clientVersion = data.version;

            // Cek apakah ada pemain lain yang posisinya bergeser
            let movedPlayer = null;
            if (data.players && !isWalking) {
                data.players.forEach(p => {
                    const currentStored = playerPositions[p.user_id];
                    if (currentStored !== undefined && currentStored !== parseInt(p.position)) {
                        movedPlayer = p;
                    }
                });
            }

            if (movedPlayer && !isWalking) {
                animateWalk(movedPlayer.user_id, parseInt(movedPlayer.position), () => {
                    applyState(data, true);
                });
            } else if (!isWalking) {
                applyState(data, false);
            }
        }
    } catch (e) {
        // silent polling
    }
}, 2000);

// ── Render Dynamic State Updates ──────────────────────────────────
function applyState(state, keepPawnsAnimating = false) {
    if (!state) return;
    isMyTurn = state.is_my_turn;

    // Update Dadu
    if (state.game.dice_1) document.getElementById('cube-d1').textContent = state.game.dice_1;
    if (state.game.dice_2) document.getElementById('cube-d2').textContent = state.game.dice_2;

    // Update Pot Kas
    document.getElementById('pot-tax-info').innerHTML = `💰 Kas Denda VIP: <b>${parseInt(state.game.pot_tax || 0)} PR</b>`;

    // Update Koleksi Kartu di Tangan
    if (state.my_cards) {
        currentMyCards = state.my_cards;
        const cntEl = document.getElementById('hand-card-count');
        if (cntEl) cntEl.textContent = state.my_cards.length;
        const btnCards = document.getElementById('btn-hand-cards');
        if (btnCards) {
            if (state.my_cards.length > 0) btnCards.classList.add('has-cards');
            else btnCards.classList.remove('has-cards');
        }
    }

    // Update Posisi Bidak Pemain jika tidak sedang dalam animasi melangkah
    if (!keepPawnsAnimating && !isWalking) {
        document.querySelectorAll('.pawns-container').forEach(c => c.innerHTML = '');
        if (state.players) {
            state.players.forEach((ply, idx) => {
                playerPositions[ply.user_id] = parseInt(ply.position);
                const isElim = (ply.is_eliminated === true || ply.is_eliminated === 1 || ply.is_eliminated === '1');
                if (!isElim) {
                    const targetWrap = document.getElementById(`pawns-tile-${ply.position}`);
                    if (targetWrap) {
                        const order = ply.turn_order || (idx + 1);
                        const isMe = (parseInt(ply.user_id) === USER_ID);
                        const isTurn = (parseInt(state.game.current_turn_user_id) === parseInt(ply.user_id));
                        const span = document.createElement('span');
                        span.className = `pawn-icon pawn-user-${ply.user_id} pawn-user-order-${order} ${isMe ? 'is-my-pawn' : ''} ${isTurn ? 'is-turn-pawn' : ''}`;
                        span.id = `pawn-user-${ply.user_id}`;
                        span.textContent = ply.token_icon;
                        span.title = `${ply.username} (${ply.token_character})${isTurn ? ' — Sedang Giliran' : ''}`;
                        targetWrap.appendChild(span);
                    }
                }
            });
        }
    }

    // Update Tag Kepemilikan & Bangunan di Setiap Petak
    if (state.properties) {
        currentProperties = state.properties;
        if (state.players) currentPlayers = state.players;

        const playerMap = {};
        if (currentPlayers) {
            currentPlayers.forEach((pl, idx) => {
                playerMap[pl.user_id] = {
                    order: pl.turn_order || (idx + 1),
                    token: pl.token_icon || '⭐',
                    name: pl.username
                };
            });
        }

        Object.keys(state.properties).forEach(tileId => {
            const prop = state.properties[tileId];
            const tileEl = document.getElementById(`tile-${tileId}`);
            if (tileEl) {
                // Hapus kelas pemilik lama
                tileEl.classList.remove('is-owned', 'p-owner-1', 'p-owner-2', 'p-owner-3', 'p-owner-4', 'p-owner-5', 'p-owner-6');

                let seal = tileEl.querySelector('.tile-owner-seal');
                let banner = tileEl.querySelector('.tile-owner-banner');
                let bldBadge = tileEl.querySelector('.tile-building-badge');
                const priceEl = tileEl.querySelector('.tile-price');

                if (prop && prop.owner_user_id) {
                    const pInfo = playerMap[prop.owner_user_id] || {
                        order: prop.owner_order || 1,
                        token: prop.owner_token || '⭐',
                        name: prop.owner_name || 'Pemilik'
                    };

                    tileEl.classList.add('is-owned', `p-owner-${pInfo.order}`);

                    // 1. Stempel Ikon Pemilik di Pojok Kiri Atas
                    if (!seal) {
                        seal = document.createElement('div');
                        seal.className = 'tile-owner-seal';
                        tileEl.appendChild(seal);
                    }
                    seal.textContent = pInfo.token;
                    seal.title = `Milik: ${pInfo.name}`;

                    // 2. Banner Pemilik di Bawah
                    if (!banner) {
                        banner = document.createElement('div');
                        const inner = tileEl.querySelector('.tile-inner');
                        inner.appendChild(banner);
                    }
                    if (priceEl) priceEl.style.display = 'none';
                    banner.className = `tile-owner-banner p-banner-${pInfo.order}`;
                    banner.innerHTML = `<span>${pInfo.token}</span> <span>${pInfo.name}</span>`;
                    banner.title = `Milik: ${pInfo.name}`;

                    // 3. Miniatur Tingkat Bangunan
                    const lvl = parseInt(prop.building_level || 0);
                    if (lvl > 0) {
                        const lvlIcon = (lvl === 1) ? '🛖' : ((lvl === 2) ? '🏢' : '🏛️');
                        if (!bldBadge) {
                            bldBadge = document.createElement('span');
                            tileEl.appendChild(bldBadge);
                        }
                        bldBadge.className = `tile-building-badge level-${lvl}`;
                        bldBadge.innerHTML = `${lvlIcon} Lv ${lvl}`;
                        bldBadge.title = `Tingkat Bangunan: Level ${lvl}`;
                    } else if (bldBadge) {
                        bldBadge.remove();
                    }

                } else {
                    if (seal) seal.remove();
                    if (banner) banner.remove();
                    if (bldBadge) bldBadge.remove();
                    if (priceEl) priceEl.style.display = '';
                }
            }
        });
    }

    // Update HUD Pemain (S+ Character Card Wings)
    function renderPlayerCard(p, isCur, inDebt) {
        const isEliminated = (p.is_eliminated === true || p.is_eliminated === 1 || p.is_eliminated === '1');
        const isInJail = (p.is_in_jail === true || p.is_in_jail === 1 || p.is_in_jail === '1');
        return `
            <div class="player-item p-user-order-${p.turn_order} ${isCur ? 'is-active-turn' : ''} ${isEliminated ? 'is-eliminated' : ''}">
                <div class="p-token-avatar" title="${p.token_character || ''}">${p.token_icon || '👤'}</div>
                <div class="p-meta">
                    <div class="p-name">
                        <span>${p.username}</span>
                        <span style="font-size:0.62rem;opacity:0.8;font-weight:700;">(P${p.turn_order})</span>
                        ${isCur ? '🔥' : ''}
                    </div>
                    <div class="p-pr-balance ${inDebt ? 'in-debt' : ''}">
                        🟡 ${p.reputation_points} PR
                        ${inDebt ? '<span style="font-size:0.55rem; color:#ef4444;">(UTANG)</span>' : ''}
                    </div>
                </div>
                ${isInJail ? '<span class="p-badge badge-jail">Karantina</span>' : ''}
            </div>
        `;
    }

    const hudLeft = document.getElementById('hud-player-left');
    const hudRight = document.getElementById('hud-player-right');
    if (state.players) {
        const lefts = [];
        const rights = [];
        state.players.forEach((p, idx) => {
            if (idx % 2 === 0) lefts.push(p);
            else rights.push(p);
        });
        if (hudLeft) {
            hudLeft.innerHTML = lefts.map(p => {
                const isCur = (parseInt(state.game.current_turn_user_id) === parseInt(p.user_id));
                const inDebt = (parseInt(p.reputation_points) < 0);
                return renderPlayerCard(p, isCur, inDebt);
            }).join('');
        }
        if (hudRight) {
            hudRight.innerHTML = rights.map(p => {
                const isCur = (parseInt(state.game.current_turn_user_id) === parseInt(p.user_id));
                const inDebt = (parseInt(p.reputation_points) < 0);
                return renderPlayerCard(p, isCur, inDebt);
            }).join('');
        }
    }

    // Update Prompt Banners
    const pTitle = document.getElementById('prompt-card-title');
    if (pTitle) {
        if (isMyTurn) {
            pTitle.textContent = '🔥 GILIRAN ANDA';
        } else {
            const curPly = (state.players || []).find(p => parseInt(p.user_id) === parseInt(state.game.current_turn_user_id));
            const curName = curPly ? curPly.username : 'LAWAN';
            const curOrder = curPly ? ` (P${curPly.turn_order})` : '';
            pTitle.textContent = `⏳ GILIRAN @${curName.toUpperCase()}${curOrder}`;
        }
    }
    const pDesc = document.getElementById('prompt-card-desc');
    if (pDesc) pDesc.textContent = state.game.last_action_desc || 'Lempar dadu untuk melangkah!';

    // Update Action Buttons in Center Arena
    const btnGrid = document.getElementById('action-controls-grid');
    if (btnGrid) {
        if (state.game.winner_user_id || state.game.game_phase === 'ended') {
            btnGrid.innerHTML = `<div style="text-align:center;font-size:0.82rem;color:#f59e0b;font-weight:900;padding:4px;">🏆 PERMAINAN SELESAI!</div>`;
            
            // Tampilkan modal kemenangan / kekalahan yang jelas
            const winnerPlayer = (state.players || []).find(p => parseInt(p.user_id) === parseInt(state.game.winner_user_id));
            const winnerName = winnerPlayer ? winnerPlayer.username : 'Pemenang';
            const isMeWinner = (parseInt(state.game.winner_user_id) === USER_ID);

            const vicModal = document.getElementById('modal-victory');
            if (vicModal && vicModal.style.display !== 'flex') {
                const iconEl  = document.getElementById('victory-icon');
                const titleEl = document.getElementById('victory-title');
                const nameEl  = document.getElementById('victory-winner-name');
                const descEl  = document.getElementById('victory-desc');
                const boxEl   = document.getElementById('victory-modal-box');
                const btnBack = document.getElementById('btn-victory-back');

                if (iconEl) iconEl.textContent = isMeWinner ? '🏆' : '💀';
                if (titleEl) {
                    titleEl.textContent = isMeWinner ? '🎉 SELAMAT! ANDA MENANG!' : '💀 ANDA KALAH!';
                    titleEl.style.color = isMeWinner ? '#d97706' : '#dc2626';
                }
                if (nameEl) {
                    nameEl.textContent = isMeWinner ? '👑 Anda adalah Penguasa Tahta & Panggung Skena!' : `Pemenang: 👑 ${winnerName}`;
                }
                if (descEl) {
                    descEl.textContent = isMeWinner
                        ? 'Seluruh lawan telah deplatformed atau menyerah! Reputasi dan tahta Anda tak tergoyahkan.'
                        : `Tahta Skenalitik telah ditaklukkan oleh ${winnerName}. Anda telah tersisih atau ter-deplatformed!`;
                    descEl.style.background = isMeWinner ? '#fffbeb' : '#fef2f2';
                    descEl.style.borderColor = isMeWinner ? '#fde68a' : '#fecaca';
                }
                if (boxEl) {
                    boxEl.style.border = isMeWinner ? '2px solid #f59e0b' : '2px solid #ef4444';
                    boxEl.style.boxShadow = isMeWinner ? '0 10px 30px rgba(245,158,11,0.3)' : '0 10px 30px rgba(239,68,68,0.3)';
                }
                if (btnBack && !isMeWinner) {
                    btnBack.style.background = 'linear-gradient(135deg, #ef4444, #b91c1c)';
                }
                vicModal.style.display = 'flex';
                playSound(isMeWinner ? 'start' : 'rent');
            }
        } else if (isMyTurn && !isWalking) {
            if (state.game.game_phase === 'roll') {
                const me = state.players.find(p => parseInt(p.user_id) === USER_ID);
                let html = '';
                const meInJail = (me && (me.is_in_jail === true || me.is_in_jail === 1 || me.is_in_jail === '1'));
                if (meInJail) {
                    const hasHakimCard = (currentMyCards || []).some(c => c.deck_type === 'politik' && parseInt(c.card_index) === 8);
                    if (hasHakimCard) {
                        html += `<button class="btn-action-main btn-gold-jail" onclick="useJailCard()">⚖️ Bebas dengan Kartu Intervensi Hakim</button>`;
                    }
                    html += `<button class="btn-action-main btn-danger-jail" onclick="payJailFine()">💸 Bayar Denda Karantina (150 PR)</button>`;
                }
                html += `
                    <div class="dice-gauge-wrap">
                        <div class="dice-gauge-bar">
                            <div class="dice-gauge-fill"></div>
                            <span class="gauge-label">2-3</span>
                            <span class="gauge-label">4-9</span>
                            <span class="gauge-label">10-12</span>
                        </div>
                    </div>
                    <button class="btn-action-main btn-roll" id="btn-roll-dice" onclick="rollDice()">
                        🎲 KOCOK DADU!
                    </button>
                `;
                btnGrid.innerHTML = html;
            } else if (state.game.pending_action === 'buy_property') {
                const pendingTile = tilesConfig[state.game.pending_tile_id] || {};
                btnGrid.innerHTML = `
                    <div class="center-buy-card">
                        <div class="buy-card-header">
                            <span style="font-size:1.1rem;">${pendingTile.icon || '🏢'}</span>
                            <div class="buy-card-title">${pendingTile.name || 'Properti'}</div>
                            <div class="buy-card-price">🪙 ${pendingTile.price || 0} PR</div>
                        </div>
                        <div class="buy-card-buttons">
                            <button class="btn-action-main btn-buy" onclick="buyProperty()">🏷️ BELI (${pendingTile.price || 0} PR)</button>
                            <button class="btn-action-main btn-skip" onclick="skipProperty()">✕ Lewati</button>
                        </div>
                    </div>
                `;
            } else if (state.game.pending_action === 'upgrade_property') {
                const pendingTile = tilesConfig[state.game.pending_tile_id] || {};
                const cost = parseInt(pendingTile.upgrade_cost || 100);
                btnGrid.innerHTML = `
                    <div style="display:flex; gap:6px; width:100%;">
                        <button class="btn-action-main btn-upgrade" style="flex:2;" onclick="openUpgradeModal(${state.game.pending_tile_id})">
                            🏗️ BANGUN PROPERTI (${cost} PR)
                        </button>
                        <button class="btn-action-main btn-skip" style="flex:1;" onclick="skipProperty()">
                            ✕ Lewati
                        </button>
                    </div>
                `;

                if (!isWalking) {
                    openUpgradeModal(state.game.pending_tile_id);
                }
            } else {
                btnGrid.innerHTML = `<button class="btn-action-main btn-endturn" onclick="endTurn()">⏭️ SELESAI GILIRAN</button>`;
            }
        } else if (!isMyTurn) {
            const curPlayer = state.players.find(p => parseInt(p.user_id) === parseInt(state.game.current_turn_user_id));
            const curName = curPlayer ? curPlayer.username : 'pemain lain';
            btnGrid.innerHTML = `<div class="center-waiting-badge"><span>⏳</span> Giliran @${curName} melempar dadu...</div>`;
        }
    }

    // Update Logs
    const logWrap = document.getElementById('logs-ticker-wrap');
    if (logWrap && state.logs && state.logs.length > 0) {
        logWrap.innerHTML = `• ${state.logs[0].message}`;
    }
}

// ── SURRENDER MODAL & JS HANDLERS ─────────────────────────────────
function openSurrenderModal() {
    const modal = document.getElementById('modal-surrender');
    if (modal) modal.style.display = 'flex';
}

function closeSurrenderModal() {
    const modal = document.getElementById('modal-surrender');
    if (modal) modal.style.display = 'none';
}

async function confirmSurrender() {
    const btn = document.getElementById('btn-confirm-surrender');
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';
    }

    try {
        const formData = new FormData();
        formData.append('room_code', ROOM_CODE);
        const res = await fetch('<?= site_url('skenalitik/aksi_menyerah') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.ok) {
            window.location.href = data.redirect || '<?= site_url('lobby') ?>';
        } else {
            alert(data.message || 'Gagal menyerah.');
            if (btn) {
                btn.disabled = false;
                btn.textContent = '🏳️ Ya, Menyerah';
            }
        }
    } catch (e) {
        window.location.href = '<?= site_url('lobby') ?>';
    }
}
</script>
</body>
</html>
