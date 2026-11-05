<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="theme-color" content="#0f0c29">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/game.css') ?>">
    <style>
        body { min-height: 100dvh; background: var(--bg-dark); padding: 1.25rem; }
        .profil-header { display: flex; align-items: center; gap: 1rem; padding: 1.25rem 0 1.5rem; }
        .profil-header a { color: var(--text-muted); text-decoration: none; font-size: 1.5rem; }
        .profil-header h1 { font-size: 1.2rem; font-weight: 700; color: var(--text-primary); }
        .profil-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2rem; text-align: center; margin-bottom: 1.25rem; box-shadow: var(--shadow-card); }
        .avatar-wrap { position: relative; display: inline-block; margin-bottom: 1rem; }
        .avatar-img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-primary); box-shadow: 0 0 25px rgba(139,92,246,0.4); }
        .avatar-emoji { width: 90px; height: 90px; border-radius: 50%; background: var(--card-bg-2); border: 3px solid var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; box-shadow: 0 0 25px rgba(139,92,246,0.4); margin: 0 auto; }
        .profil-username { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); }
        .profil-email { font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem; }
        .profil-join { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
        .stat-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; text-align: center; box-shadow: var(--shadow-card); }
        .stat-card .stat-icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .stat-card .stat-label { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; }
        .stat-card.uno { border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.05); }
        .stat-card.uno .stat-value { background: linear-gradient(135deg, #ef4444, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .stat-card.ww { border-color: rgba(139,92,246,0.3); background: rgba(139,92,246,0.05); }
        .stat-card.ww .stat-value { background: linear-gradient(135deg, #8b5cf6, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-row { display: flex; flex-direction: column; gap: 0.75rem; }
        .alert-success { padding: 0.875rem 1rem; border-radius: var(--radius-md); font-size: 0.875rem; margin-bottom: 1rem; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); color: #86efac; }
    </style>
</head>
<body>
    <div style="max-width: 480px; margin: 0 auto;">
        <!-- Header -->
        <div class="profil-header">
            <a href="<?= site_url('lobby') ?>" title="Kembali ke Lobby">←</a>
            <h1>Profil Saya</h1>
        </div>

        <?php if ($flash_success): ?>
        <div class="alert-success">✅ <?= htmlspecialchars($flash_success) ?></div>
        <?php endif; ?>

        <!-- Avatar & Info -->
        <div class="profil-card">
            <div class="avatar-wrap">
                <?php if ($profil->avatar && $profil->avatar !== 'default.png' && file_exists(ROOTPATH . 'upload/avatars/' . $profil->avatar)): ?>
                    <img src="<?= base_url('upload/avatars/' . $profil->avatar) ?>" alt="Avatar" class="avatar-img">
                <?php else: ?>
                    <div class="avatar-emoji">🎮</div>
                <?php endif; ?>
            </div>
            <div class="profil-username"><?= htmlspecialchars($profil->username) ?></div>
            <div class="profil-email">📧 <?= htmlspecialchars($profil->email) ?></div>
            <div class="profil-join">🗓 Bergabung sejak <?= date('d M Y', strtotime($profil->created_at)) ?></div>
        </div>

        <!-- Statistik -->
        <div class="stats-grid">
            <div class="stat-card uno">
                <div class="stat-icon">🃏</div>
                <div class="stat-value"><?= (int)$profil->uno_wins ?></div>
                <div class="stat-label">Kemenangan UNO</div>
            </div>
            <div class="stat-card ww">
                <div class="stat-icon">🐺</div>
                <div class="stat-value"><?= (int)$profil->ww_wins ?></div>
                <div class="stat-label">Kemenangan Werewolf</div>
            </div>
        </div>

        <!-- Total -->
        <div class="profil-card" style="margin-bottom:1.25rem;">
            <div style="font-size:1.8rem; font-weight:800; color:var(--text-primary);">
                <?= (int)$profil->uno_wins + (int)$profil->ww_wins ?>
            </div>
            <div style="font-size:0.875rem; color:var(--text-muted); margin-top:0.25rem;">Total Kemenangan 🏆</div>
        </div>

        <!-- Tombol -->
        <div class="btn-row">
            <a href="<?= site_url('lobby') ?>" class="btn btn-secondary">🏠 Kembali ke Lobby</a>
            <a href="<?= site_url('auth/logout') ?>"
               class="btn"
               style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); color: #fca5a5;"
               onclick="return confirm('Yakin mau keluar?')">
                🚪 Keluar dari Akun
            </a>
        </div>
    </div>
</body>
</html>
