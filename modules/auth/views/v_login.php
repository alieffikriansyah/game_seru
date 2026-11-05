<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="description" content="Login ke Game Seru — platform multiplayer UNO dan Werewolf berbahasa Indonesia">
    <meta name="theme-color" content="#0f0c29">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/game.css') ?>">
    <style>
        body {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-dark);
            padding: 1.25rem;
            overflow-x: hidden;
        }
        .auth-container {
            width: 100%;
            max-width: 420px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo .logo-title {
            font-family: 'Cinzel', serif;
            font-size: 2.2rem;
            font-weight: 900;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            letter-spacing: 2px;
        }
        .auth-logo .logo-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.4rem;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .auth-logo .logo-emoji {
            font-size: 3.5rem;
            display: block;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 0 20px rgba(255,200,0,0.5));
            animation: float 3s ease-in-out infinite;
        }
        .auth-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 2rem;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-card);
        }
        .auth-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .auth-card p.subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
        }
        .alert {
            padding: 0.875rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #86efac;
        }
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .auth-links a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .pwa-install-wrap {
            text-align: center;
            margin-top: 1.5rem;
        }
        #pwa-install-btn {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        #pwa-install-btn:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .game-badges {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
        }
        .game-badge {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 2rem;
            padding: 0.3rem 0.8rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Logo -->
        <div class="auth-logo">
            <span class="logo-emoji">🎮</span>
            <div class="logo-title">GAME SERU</div>
            <div class="logo-sub">Multiplayer · Indonesia</div>
            <div class="game-badges">
                <span class="game-badge">🃏 UNO</span>
                <span class="game-badge">🐺 Werewolf</span>
            </div>
        </div>

        <!-- Card Login -->
        <div class="auth-card">
            <h2>Masuk 👋</h2>
            <p class="subtitle">Masuk ke akunmu dan mulai bermain!</p>

            <?php if ($flash_error): ?>
            <div class="alert alert-error">
                <span>⚠️</span> <?= htmlspecialchars($flash_error) ?>
            </div>
            <?php endif; ?>

            <?php if ($flash_success): ?>
            <div class="alert alert-success">
                <span>✅</span> <?= htmlspecialchars($flash_success) ?>
            </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/proses_login') ?>" method="POST" id="form-login">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        required
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div style="position:relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                            style="padding-right: 3rem;"
                        >
                        <button type="button" onclick="togglePwd('password', this)" class="pwd-toggle">👁</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full" id="btn-login">
                    <span>Masuk ke Game</span>
                    <span class="btn-icon">🚀</span>
                </button>
            </form>

            <div class="auth-links">
                Belum punya akun? <a href="<?= site_url('auth/register') ?>">Daftar Gratis</a>
            </div>
        </div>

        <!-- PWA Install -->
        <div class="pwa-install-wrap">
            <button id="pwa-install-btn">📲 Pasang Aplikasi di HP</button>
        </div>
    </div>

    <script src="<?= base_url('assets/js/pwa-install.js') ?>"></script>
    <script>
        function togglePwd(inputId, btn) {
            const inp = document.getElementById(inputId);
            if (inp.type === 'password') {
                inp.type = 'text';
                btn.textContent = '🙈';
            } else {
                inp.type = 'password';
                btn.textContent = '👁';
            }
        }

        // Loading state saat submit
        document.getElementById('form-login').addEventListener('submit', function() {
            const btn = document.getElementById('btn-login');
            btn.innerHTML = '<span>Memverifikasi...</span>';
            btn.disabled = true;
        });
    </script>
</body>
</html>
