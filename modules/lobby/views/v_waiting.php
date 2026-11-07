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
        body { height: 100dvh; overflow: hidden; background: var(--bg-dark); display: flex; flex-direction: column; max-width: 480px; margin: 0 auto; }

        /* Header */
        .wroom-header {
            flex-shrink: 0;
            background: rgba(10,10,20,0.95);
            border-bottom: 1px solid var(--border-color);
            padding: 0.875rem 1rem;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .wroom-header h1 { font-size: 1rem; font-weight: 700; flex: 1; }
        .back-btn { color: var(--text-muted); text-decoration: none; font-size: 1.25rem; }

        /* Room Code Banner */
        .code-banner {
            flex-shrink: 0;
            background: linear-gradient(135deg, #0f0c29, #1a0a3a);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1rem;
            text-align: center;
        }
        .code-label { font-size: 0.7rem; letter-spacing: 3px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem; }
        .code-display {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: 0.6rem;
            color: var(--text-primary);
            text-shadow: 0 0 20px rgba(139,92,246,0.6);
        }
        .code-copy-btn {
            margin-top: 0.5rem;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 0.35rem 1rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.15s;
        }
        .code-copy-btn:active { color: #22c55e; border-color: #22c55e; }
        .game-type-pill {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.3rem 0.875rem;
            border-radius: var(--radius-full);
            font-size: 0.8rem; font-weight: 600;
            margin-top: 0.625rem;
        }
        .game-type-pill.uno { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        .game-type-pill.ular_tangga { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7; }
        .game-type-pill.skenalitik { background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); color: #fbbf24; }
        .game-type-pill.ludo { background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #60a5fa; }
        .game-type-pill.werewolf { background: rgba(139,92,246,0.15); border: 1px solid rgba(139,92,246,0.3); color: #c4b5fd; }

        /* Player Grid */
        .players-area {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 1rem;
        }
        .players-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; }
        .players-title { font-size: 0.875rem; font-weight: 700; color: var(--text-secondary); }
        .players-count { font-size: 0.8rem; color: var(--text-muted); }

        .player-row {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 0.875rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-bottom: 0.625rem;
            transition: border-color 0.2s;
        }
        .player-row.is-me { border-color: rgba(139,92,246,0.4); }
        .player-row.is-host .pname::after { content: ' 👑'; font-size: 0.8em; }
        .pavatar { width: 46px; height: 46px; border-radius: 50%; background: var(--card-bg-2); border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .pinfo { flex: 1; min-width: 0; }
        .pname { font-weight: 700; font-size: 0.9375rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pseat { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }
        .pstatus { font-size: 1.5rem; flex-shrink: 0; }
        .pstatus.ready { filter: drop-shadow(0 0 6px rgba(34,197,94,0.8)); }
        .pstatus.not-ready { opacity: 0.4; }

        /* Footer Actions */
        .wroom-footer {
            flex-shrink: 0;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            background: rgba(10,10,20,0.95);
            display: flex; flex-direction: column; gap: 0.625rem;
        }
        .min-info { font-size: 0.75rem; color: var(--text-muted); text-align: center; }

        /* Ready indicator */
        .ready-summary {
            padding: 0.625rem 1rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.875rem;
        }
        .ready-bar {
            flex: 1; height: 6px; border-radius: 3px;
            background: var(--border-color); overflow: hidden;
        }
        .ready-fill { height: 100%; background: #22c55e; border-radius: 3px; transition: width 0.4s ease; }
    </style>
</head>
<body>

<!-- Header -->
<div class="wroom-header">
    <a href="<?= site_url('lobby') ?>" class="back-btn">←</a>
    <h1><?= htmlspecialchars($room['room_name']) ?></h1>
    <span class="game-type-pill <?= $room['game_type'] ?>">
        <?php
            if ($room['game_type'] === 'uno') echo '🃏 UNO';
            elseif ($room['game_type'] === 'ludo') echo '🎯 Ludo';
            elseif ($room['game_type'] === 'ular_tangga') echo '🎲 Ular Tangga';
            elseif ($room['game_type'] === 'skenalitik') echo '🎙️ Skenalitik';
            else echo '🐺 Werewolf';
        ?>
    </span>
</div>

<!-- Room Code Banner -->
<div class="code-banner">
    <div class="code-label">Kode Room — Bagikan ke Temanmu!</div>
    <div class="code-display" id="room-code-display"><?= $room['room_code'] ?></div>
    <button class="code-copy-btn" onclick="copyCode()">📋 Salin Kode</button>
</div>

<!-- Players Area -->
<div class="players-area">
    <div class="players-header">
        <span class="players-title">👥 Pemain</span>
        <span class="players-count" id="player-count"><?= count($players) ?>/<?= $max_players ?> pemain</span>
    </div>

    <!-- Ready Summary Bar -->
    <?php
        $ready_count = 0;
        foreach ($players as $p) { if ($p['is_ready']) $ready_count++; }
        $total = count($players);
        $ready_pct = $total > 0 ? round(($ready_count / $total) * 100) : 0;
    ?>
    <div class="ready-summary mb-2" id="ready-summary">
        <span>Siap:</span>
        <div class="ready-bar">
            <div class="ready-fill" id="ready-fill" style="width: <?= $ready_pct ?>%"></div>
        </div>
        <span id="ready-text"><?= $ready_count ?>/<?= $total ?></span>
    </div>

    <!-- Player List -->
    <div id="player-list">
        <?php foreach ($players as $p): ?>
        <div class="player-row <?= ($p['user_id'] == $user_id) ? 'is-me' : '' ?> <?= ($p['user_id'] == $room['host_user_id']) ? 'is-host' : '' ?>"
             id="player-<?= $p['user_id'] ?>">
            <div class="pavatar">
                <?php if ($p['avatar'] && $p['avatar'] !== 'default.png'): ?>
                    <img src="<?= base_url('upload/avatars/' . $p['avatar']) ?>" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                    🎮
                <?php endif; ?>
            </div>
            <div class="pinfo">
                <div class="pname"><?= htmlspecialchars($p['username']) ?><?= ($p['user_id'] == $room['host_user_id']) ? ' 👑' : '' ?><?= ($p['user_id'] == $user_id) ? ' (Kamu)' : '' ?></div>
                <div class="pseat">Kursi #<?= $p['seat_order'] ?></div>
            </div>
            <div class="pstatus <?= $p['is_ready'] ? 'ready' : 'not-ready' ?>">
                <?= $p['is_ready'] ? '✅' : '⏳' ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Min player info -->
    <p class="min-info mt-2">
        Minimal <?= $min_players ?> pemain untuk memulai <?= strtoupper($room['game_type']) ?>
    </p>
</div>

<!-- Footer Actions -->
<div class="wroom-footer">
    <!-- Tombol Siap (semua pemain) -->
    <?php
        $my_ready = false;
        foreach ($players as $p) {
            if ($p['user_id'] == $user_id && $p['is_ready']) { $my_ready = true; break; }
        }
    ?>
    <button id="btn-siap"
            class="btn <?= $my_ready ? 'btn-secondary' : 'btn-success' ?>"
            onclick="toggleSiap()">
        <?= $my_ready ? '🔄 Batal Siap' : '✅ Aku Siap!' ?>
    </button>

    <!-- Tombol Mulai (Host Only) -->
    <?php if ($is_host): ?>
    <button id="btn-mulai" class="btn btn-primary" onclick="mulaiGame()">
        🚀 Mulai Game!
    </button>
    <?php endif; ?>

    <!-- Tombol Keluar -->
    <button class="btn btn-danger" onclick="keluarRoom()">🚪 Keluar Room</button>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
const BASE_URL    = '<?= site_url() ?>';
const ROOM_CODE   = '<?= $room['room_code'] ?>';
const IS_HOST     = <?= $is_host ? 'true' : 'false' ?>;
const MY_USER_ID  = <?= (int)$user_id ?>;
const HOST_ID     = <?= (int)$room['host_user_id'] ?>;
const GAME_TYPE   = '<?= $room['game_type'] ?>';
const MIN_PLAYERS = <?= (int)$min_players ?>;

let clientVersion = <?= (int)$room['state_version'] ?>;
let isSiap = <?= $my_ready ? 'true' : 'false' ?>;
let polling = true;

// ── Salin kode ────────────────────────────────────────────────
function copyCode() {
    navigator.clipboard.writeText(ROOM_CODE).then(() => {
        showToast('📋 Kode ' + ROOM_CODE + ' berhasil disalin!', 'success');
    }).catch(() => {
        showToast('Kode: ' + ROOM_CODE, 'info');
    });
}

// ── Toggle Siap ───────────────────────────────────────────────
async function toggleSiap() {
    try {
        const res = await fetch(BASE_URL + 'lobby/siap', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'room_code=' + ROOM_CODE,
        });
        const data = await res.json();
        if (!data.ok) { showToast(data.message || 'Gagal toggle siap.', 'error'); }
    } catch (e) {
        showToast('Koneksi bermasalah. Coba lagi.', 'error');
    }
}

// ── Mulai Game ────────────────────────────────────────────────
async function mulaiGame() {
    const btn = document.getElementById('btn-mulai');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Memulai...';

    try {
        const res = await fetch(BASE_URL + 'lobby/mulai', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'room_code=' + ROOM_CODE,
        });
        const data = await res.json();
        if (data.ok) {
            polling = false;
            showToast('🚀 Game dimulai! Mengarahkan...', 'success');
            setTimeout(() => window.location.href = data.redirect, 800);
        } else {
            showToast(data.message || 'Tidak bisa memulai.', 'error');
            btn.disabled = false;
            btn.innerHTML = '🚀 Mulai Game!';
        }
    } catch (e) {
        showToast('Koneksi bermasalah.', 'error');
        btn.disabled = false;
        btn.innerHTML = '🚀 Mulai Game!';
    }
}

// ── Keluar Room ───────────────────────────────────────────────
async function keluarRoom() {
    if (!confirm('Yakin mau keluar dari room ini?')) return;
    polling = false;
    await fetch(BASE_URL + 'lobby/keluar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'room_code=' + ROOM_CODE,
    }).catch(() => {});
    window.location.href = BASE_URL + 'lobby';
}

// ── Polling State ─────────────────────────────────────────────
async function pollState() {
    if (!polling) return;
    try {
        const res = await fetch(BASE_URL + 'lobby/poll_waiting/' + ROOM_CODE + '?v=' + clientVersion, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.started) {
            polling = false;
            showToast('🚀 Game dimulai! Mengarahkan...', 'success');
            setTimeout(() => window.location.href = data.redirect, 600);
            return;
        }
        if (data.finished) {
            window.location.href = BASE_URL + 'lobby';
            return;
        }
        if (data.updated) {
            clientVersion = data.version;
            updatePlayerList(data.players, data.host_id);
            updateReadyBar(data.players);
            updateSiapBtn(data.players);
            if (IS_HOST && data.all_ready && data.player_count >= MIN_PLAYERS) {
                const btn = document.getElementById('btn-mulai');
                if (btn) btn.classList.add('animate-pulse-glow');
            }
        }
    } catch (e) { /* silent */ }

    if (polling) setTimeout(pollState, 2000);
}

function updatePlayerList(players, hostId) {
    const list = document.getElementById('player-list');
    list.innerHTML = '';
    players.forEach(p => {
        const isMe   = p.user_id === MY_USER_ID;
        const isHost = p.user_id === hostId;
        list.innerHTML += `
        <div class="player-row ${isMe ? 'is-me' : ''} ${isHost ? 'is-host' : ''}">
            <div class="pavatar">🎮</div>
            <div class="pinfo">
                <div class="pname">${esc(p.username)}${isHost?' 👑':''}${isMe?' (Kamu)':''}</div>
                <div class="pseat">Kursi #${p.seat_order}</div>
            </div>
            <div class="pstatus ${p.is_ready ? 'ready' : 'not-ready'}">${p.is_ready ? '✅' : '⏳'}</div>
        </div>`;
    });
    document.getElementById('player-count').textContent = players.length + '/' + <?= (int)$max_players ?> + ' pemain';
}

function updateReadyBar(players) {
    const ready = players.filter(p => p.is_ready).length;
    const total = players.length;
    const pct   = total > 0 ? Math.round((ready/total)*100) : 0;
    document.getElementById('ready-fill').style.width = pct + '%';
    document.getElementById('ready-text').textContent  = ready + '/' + total;
}

function updateSiapBtn(players) {
    const me = players.find(p => p.user_id === MY_USER_ID);
    if (!me) return;
    isSiap = me.is_ready;
    const btn = document.getElementById('btn-siap');
    if (isSiap) {
        btn.textContent = '🔄 Batal Siap';
        btn.className = 'btn btn-secondary';
    } else {
        btn.textContent = '✅ Aku Siap!';
        btn.className = 'btn btn-success';
    }
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
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

// Mulai polling
setTimeout(pollState, 2000);

// Beritahu server saat user menutup tab
window.addEventListener('beforeunload', () => {
    navigator.sendBeacon(BASE_URL + 'lobby/keluar', 'room_code=' + ROOM_CODE);
});
</script>
</body>
</html>
