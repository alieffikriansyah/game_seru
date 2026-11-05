<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="description" content="Lobby Game Seru — pilih room dan mulai bermain UNO atau Werewolf bersama teman">
    <meta name="theme-color" content="#0a0a14">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/game.css') ?>">
    <style>
        body { background: var(--bg-dark); min-height: 100dvh; padding-bottom: 5rem; }

        .hero-strip {
            background: linear-gradient(160deg, #0f0c29 0%, #1a0a3a 50%, #0f0c29 100%);
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }
        .hero-welcome { font-size: 0.8rem; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 0.25rem; }
        .hero-name { font-size: 1.4rem; font-weight: 800; color: var(--text-primary); }

        /* Quick Actions */
        .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem; padding: 1.25rem; }
        .quick-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 0.5rem; padding: 1.25rem 1rem; border-radius: var(--radius-lg);
            cursor: pointer; border: none; text-align: center; transition: all 0.2s ease;
            min-height: 100px;
        }
        .quick-btn:active { transform: scale(0.97); }
        .quick-btn .qb-icon { font-size: 2rem; }
        .quick-btn .qb-label { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); }
        .quick-btn .qb-sub { font-size: 0.7rem; color: var(--text-muted); }
        .quick-btn-create {
            background: var(--gradient-purple);
            box-shadow: 0 4px 20px rgba(109,40,217,0.4);
        }
        .quick-btn-join {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }
        .quick-btn-join:hover { border-color: var(--accent-primary); }

        /* Room List */
        .rooms-section { padding: 0 1.25rem; }
        .room-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block;
        }
        .room-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            border-radius: 3px 0 0 3px;
        }
        .room-card.uno::before { background: var(--color-uno-red); }
        .room-card.ular_tangga::before { background: #10b981; }
        .room-card.skenalitik::before { background: #f59e0b; }
        .room-card.werewolf::before { background: var(--accent-primary); }
        .badge-ular_tangga { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-skenalitik { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
        .room-card:active { transform: scale(0.99); }
        .room-card-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.625rem; }
        .room-game-icon { font-size: 1.75rem; }
        .room-name { font-size: 1rem; font-weight: 700; color: var(--text-primary); flex: 1; }
        .room-meta { display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap; }
        .room-host { font-size: 0.75rem; color: var(--text-muted); }
        .room-players { font-size: 0.75rem; color: var(--text-muted); margin-left: auto; }
        .room-card:hover { border-color: var(--border-bright); transform: translateY(-1px); box-shadow: var(--shadow-card); }

        /* Join Modal */
        #modal-join { display: none; }

        /* Bottom FAB */
        .fab-wrap {
            position: fixed;
            bottom: 1.5rem;
            right: 1.25rem;
            z-index: 200;
        }
        .fab {
            width: 56px; height: 56px;
            background: var(--gradient-purple);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; box-shadow: 0 4px 20px rgba(109,40,217,0.6);
            cursor: pointer; border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .fab:active { transform: scale(0.93); }

        /* Alert */
        .alert {
            padding: 0.875rem 1rem; border-radius: var(--radius-md);
            font-size: 0.875rem; margin: 0 1.25rem 1rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .alert-error { background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.35); color: #86efac; }

        /* Install PWA */
        #pwa-install-btn {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 0.4rem 1rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            cursor: pointer;
        }

        /* Players bar */
        .player-bar { display: flex; align-items: center; gap: 0.25rem; }
        .player-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--border-color); }
        .player-dot.filled { background: #22c55e; }

        /* Tabs untuk filter */
        .filter-tabs { display: flex; gap: 0.5rem; padding: 0 1.25rem; margin-bottom: 1rem; overflow-x: auto; scrollbar-width: none; }
        .filter-tabs::-webkit-scrollbar { display: none; }
        .filter-tab { padding: 0.4rem 1rem; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 600; cursor: pointer; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-muted); white-space: nowrap; transition: all 0.15s; }
        .filter-tab.active { background: var(--accent-primary); border-color: var(--accent-primary); color: #fff; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="<?= site_url('lobby') ?>" class="navbar-brand">🎮 GAME SERU</a>
    <div class="navbar-actions">
        <button id="pwa-install-btn">📲 Pasang</button>
        <a href="<?= site_url('auth/profil') ?>" class="avatar navbar-avatar" title="Profil Saya">
            <span>👤</span>
        </a>
    </div>
</nav>

<!-- Hero -->
<div class="hero-strip">
    <div class="hero-welcome">Selamat datang,</div>
    <div class="hero-name"><?= htmlspecialchars($username) ?> 👋</div>
</div>

<!-- Flash Messages -->
<?php if ($flash_error): ?>
<div class="alert alert-error mt-2"><span>⚠️</span> <?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>
<?php if ($flash_success): ?>
<div class="alert alert-success mt-2"><span>✅</span> <?= htmlspecialchars($flash_success) ?></div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="quick-actions">
    <button class="quick-btn quick-btn-create" onclick="showModalBuat()">
        <span class="qb-icon">➕</span>
        <span class="qb-label">Buat Room</span>
        <span class="qb-sub">Jadilah host!</span>
    </button>
    <button class="quick-btn quick-btn-join" onclick="showModalGabung()">
        <span class="qb-icon">🔑</span>
        <span class="qb-label">Masuk Room</span>
        <span class="qb-sub">Punya kode? Masuk!</span>
    </button>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <button class="filter-tab active" onclick="filterRooms('all', this)">Semua</button>
    <button class="filter-tab" onclick="filterRooms('skenalitik', this)">🎙️ Skenalitik</button>
    <button class="filter-tab" onclick="filterRooms('uno', this)">🃏 UNO</button>
    <button class="filter-tab" onclick="filterRooms('ular_tangga', this)">🎲 Ular Tangga</button>
    <button class="filter-tab" onclick="filterRooms('werewolf', this)">🐺 Werewolf</button>
    <button class="filter-tab" onclick="filterRooms('waiting', this)">⏳ Bisa Gabung</button>
</div>

<!-- Room List -->
<div class="rooms-section">
    <div class="section-header">
        <span class="section-title">🏠 Room Aktif</span>
        <span class="section-count" id="room-count"><?= count($rooms) ?> room</span>
    </div>

    <?php if (empty($rooms)): ?>
    <div class="empty-state">
        <div class="empty-icon">🎯</div>
        <h3>Belum ada room aktif</h3>
        <p>Jadilah yang pertama membuat room!</p>
        <br>
        <button class="btn btn-primary" onclick="showModalBuat()">➕ Buat Room Sekarang</button>
    </div>
    <?php else: ?>
    <div id="room-list">
        <?php foreach ($rooms as $room): ?>
        <?php
            $icon = ($room['game_type'] === 'uno') ? '🃏' : (($room['game_type'] === 'ular_tangga') ? '🎲' : (($room['game_type'] === 'skenalitik') ? '🎙️' : '🐺'));
            $badge_class = 'badge-' . $room['game_type'];
            $game_display_title = ($room['game_type'] === 'ular_tangga') ? 'ULAR TANGGA' : (($room['game_type'] === 'skenalitik') ? 'SKENALITIK' : strtoupper($room['game_type']));
            $status_badge = $room['status'] === 'waiting' ? 'badge-waiting' : 'badge-playing';
            $status_label = $room['status'] === 'waiting' ? '⏳ Menunggu' : '🔥 Bermain';
            $count = (int)$room['player_count'];
            $max = (int)$room['max_players'];
        ?>
        <div class="room-card <?= $room['game_type'] ?>"
             data-type="<?= $room['game_type'] ?>"
             data-status="<?= $room['status'] ?>"
             onclick="joinRoom('<?= $room['room_code'] ?>', '<?= $room['status'] ?>')">
            <div class="room-card-header">
                <span class="room-game-icon"><?= $icon ?></span>
                <span class="room-name"><?= htmlspecialchars($room['room_name']) ?></span>
                <span class="badge <?= $status_badge ?>"><?= $status_label ?></span>
            </div>
            <div class="room-meta">
                <span class="badge <?= $badge_class ?>"><?= $game_display_title ?></span>
                <span class="room-host">Host: <?= htmlspecialchars($room['host_name']) ?></span>
                <span class="room-code-small"><?= $room['room_code'] ?></span>
                <span class="room-players">👥 <?= $count ?>/<?= $max ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal: Buat Room -->
<div class="modal-overlay" id="modal-buat" style="display:none;" onclick="if(event.target===this)closeModal('modal-buat')">
    <div class="modal-box animate-bounce-in">
        <h3>🎮 Buat Room Baru</h3>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:1.5rem;">Pilih game dan beri nama room kamu.</p>
        <form action="<?= site_url('lobby/buat') ?>" method="POST" id="form-buat">
            <div class="form-group">
                <label class="form-label">Jenis Game</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                    <label class="game-pick" id="pick-sk">
                        <input type="radio" name="game_type" value="skenalitik" checked style="display:none;" onchange="updateGamePick()">
                        <span style="font-size:1.6rem;">🎙️</span>
                        <span style="font-weight:700;font-size:0.85rem;">Skenalitik</span>
                        <span style="font-size:0.65rem;color:var(--text-muted);">Min <?= $sk_min ?> • Papan Skena</span>
                    </label>
                    <label class="game-pick" id="pick-uno">
                        <input type="radio" name="game_type" value="uno" style="display:none;" onchange="updateGamePick()">
                        <span style="font-size:1.6rem;">🃏</span>
                        <span style="font-weight:700;font-size:0.85rem;">UNO</span>
                        <span style="font-size:0.65rem;color:var(--text-muted);">Min <?= $uno_min ?></span>
                    </label>
                    <label class="game-pick" id="pick-ut">
                        <input type="radio" name="game_type" value="ular_tangga" style="display:none;" onchange="updateGamePick()">
                        <span style="font-size:1.6rem;">🎲</span>
                        <span style="font-weight:700;font-size:0.85rem;">Ular Tangga</span>
                        <span style="font-size:0.65rem;color:var(--text-muted);">Min <?= $ut_min ?></span>
                    </label>
                    <label class="game-pick" id="pick-ww">
                        <input type="radio" name="game_type" value="werewolf" style="display:none;" onchange="updateGamePick()">
                        <span style="font-size:1.6rem;">🐺</span>
                        <span style="font-weight:700;font-size:0.85rem;">Werewolf</span>
                        <span style="font-size:0.65rem;color:var(--text-muted);">Min <?= $ww_min ?></span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="room_name">Nama Room</label>
                <input type="text" id="room_name" name="room_name" class="form-input"
                       placeholder="contoh: Mabar Malam Minggu" maxlength="50" required>
            </div>
            <div style="display:flex;gap:0.75rem;">
                <button type="button" class="btn btn-secondary" style="flex:1" onclick="closeModal('modal-buat')">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Buat! 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Gabung Room -->
<div class="modal-overlay" id="modal-gabung" style="display:none;" onclick="if(event.target===this)closeModal('modal-gabung')">
    <div class="modal-box animate-bounce-in">
        <h3>🔑 Masuk ke Room</h3>
        <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:1.5rem;">Masukkan kode 6 huruf dari temanmu.</p>
        <form action="<?= site_url('lobby/gabung') ?>" method="POST" id="form-gabung">
            <div class="form-group">
                <label class="form-label" for="room_code_input">Kode Room</label>
                <input type="text" id="room_code_input" name="room_code" class="form-input"
                       placeholder="contoh: AB3XYZ"
                       maxlength="6" minlength="6"
                       style="text-align:center;font-size:1.5rem;letter-spacing:0.5rem;font-weight:700;text-transform:uppercase;"
                       oninput="this.value=this.value.toUpperCase()" required>
            </div>
            <div style="display:flex;gap:0.75rem;">
                <button type="button" class="btn btn-secondary" style="flex:1" onclick="closeModal('modal-gabung')">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Masuk 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<style>
.game-pick {
    display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
    padding: 1rem; border-radius: var(--radius-md); border: 2px solid var(--border-color);
    cursor: pointer; transition: all 0.2s; background: var(--card-bg);
}
.game-pick.active { border-color: var(--accent-primary); background: rgba(139,92,246,0.1); }
</style>

<script src="<?= base_url('assets/js/pwa-install.js') ?>"></script>
<script>
const BASE_URL = '<?= site_url() ?>';

function showModalBuat() {
    document.getElementById('modal-buat').style.display = 'flex';
    setTimeout(() => document.getElementById('room_name').focus(), 100);
}
function showModalGabung() {
    document.getElementById('modal-gabung').style.display = 'flex';
    setTimeout(() => document.getElementById('room_code_input').focus(), 100);
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function updateGamePick() {
    const selected = document.querySelector('input[name="game_type"]:checked')?.value;
    document.getElementById('pick-sk').classList.toggle('active', selected === 'skenalitik');
    document.getElementById('pick-uno').classList.toggle('active', selected === 'uno');
    document.getElementById('pick-ut').classList.toggle('active', selected === 'ular_tangga');
    document.getElementById('pick-ww').classList.toggle('active', selected === 'werewolf');
}
updateGamePick(); // init

function filterRooms(type, btn) {
    // Update tab styles
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    const cards = document.querySelectorAll('.room-card');
    let visible = 0;
    cards.forEach(card => {
        const match = type === 'all'
            || (type === 'waiting' && card.dataset.status === 'waiting')
            || card.dataset.type === type;
        card.style.display = match ? 'block' : 'none';
        if (match) visible++;
    });
    document.getElementById('room-count').textContent = visible + ' room';
}

function joinRoom(code, status) {
    if (status === 'playing') {
        showToast('⚠️ Room ini sedang bermain, tidak bisa bergabung.', 'error');
        return;
    }
    // Submit form gabung
    const form = document.getElementById('form-gabung');
    document.getElementById('room_code_input').value = code;
    form.submit();
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

// Auto-refresh daftar room setiap 10 detik
setInterval(() => {
    fetch(BASE_URL + 'lobby', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .catch(() => {}); // silent refresh (bisa dikembangkan)
}, 10000);
</script>
</body>
</html>
