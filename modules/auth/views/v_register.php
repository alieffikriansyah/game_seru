<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <meta name="description" content="Daftar akun baru di Game Seru — platform multiplayer UNO dan Werewolf">
    <meta name="theme-color" content="#0f0c29">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/game.css') ?>">
    <style>
        body { min-height: 100dvh; display: flex; align-items: center; justify-content: center; background: var(--bg-dark); padding: 1.25rem; }
        .auth-container { width: 100%; max-width: 420px; padding: 1rem 0; }
        .auth-logo { text-align: center; margin-bottom: 1.5rem; }
        .auth-logo .logo-title { font-family: 'Cinzel', serif; font-size: 1.8rem; font-weight: 900; background: var(--gradient-gold); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .auth-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2rem; backdrop-filter: blur(20px); box-shadow: var(--shadow-card); }
        .auth-card h2 { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.4rem; }
        .auth-card p.subtitle { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem; }
        .alert { padding: 0.875rem 1rem; border-radius: var(--radius-md); font-size: 0.875rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.5rem; }
        .alert-error { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); color: #86efac; }
        .auth-links { text-align: center; margin-top: 1.25rem; font-size: 0.875rem; color: var(--text-muted); }
        .auth-links a { color: var(--accent-primary); text-decoration: none; font-weight: 600; }
        .hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; }
        .pwd-strength { height: 4px; border-radius: 2px; margin-top: 0.5rem; background: var(--border-color); overflow: hidden; }
        .pwd-strength-bar { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <div class="logo-title">🎮 GAME SERU</div>
        </div>
        <div class="auth-card">
            <h2>Buat Akun 🎉</h2>
            <p class="subtitle">Gratis! Bergabung dan mulai bermain bersama teman.</p>

            <?php if ($flash_error): ?>
            <div class="alert alert-error"><span>⚠️</span><span><?= htmlspecialchars($flash_error) ?></span></div>
            <?php endif; ?>
            <?php if ($flash_success): ?>
            <div class="alert alert-success"><span>✅</span><span><?= htmlspecialchars($flash_success) ?></span></div>
            <?php endif; ?>

            <form action="<?= site_url('auth/proses_register') ?>" method="POST" id="form-register">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="contoh: BomberUNO99" autocomplete="username" maxlength="20" required>
                    <div class="hint">3-20 karakter. Huruf, angka, dan underscore saja.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="nama@email.com" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div style="position:relative;">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter" autocomplete="new-password" minlength="6" required style="padding-right:3rem;" oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePwd('password', this)" class="pwd-toggle">👁</button>
                    </div>
                    <div class="pwd-strength"><div class="pwd-strength-bar" id="strength-bar"></div></div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirm">Konfirmasi Password</label>
                    <div style="position:relative;">
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Ulangi password" autocomplete="new-password" required style="padding-right:3rem;">
                        <button type="button" onclick="togglePwd('password_confirm', this)" class="pwd-toggle">👁</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full" id="btn-register">
                    <span>Daftar Sekarang</span><span class="btn-icon">🎮</span>
                </button>
            </form>
            <div class="auth-links">Sudah punya akun? <a href="<?= site_url('auth') ?>">Masuk</a></div>
        </div>
    </div>
    <script>
        function togglePwd(id, btn) {
            const inp = document.getElementById(id);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            btn.textContent = inp.type === 'password' ? '👁' : '🙈';
        }
        function checkStrength(pwd) {
            const bar = document.getElementById('strength-bar');
            let score = 0;
            if (pwd.length >= 6) score++;
            if (pwd.length >= 10) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^a-zA-Z0-9]/.test(pwd)) score++;
            const colors = ['#ef4444','#f97316','#eab308','#22c55e','#10b981'];
            bar.style.width = ((score/5)*100) + '%';
            bar.style.background = colors[Math.max(0, score-1)] || '#ef4444';
        }
        document.getElementById('form-register').addEventListener('submit', function(e) {
            const pwd = document.getElementById('password').value;
            const cfm = document.getElementById('password_confirm').value;
            if (pwd !== cfm) { e.preventDefault(); alert('⚠️ Konfirmasi password tidak cocok!'); return; }
            const btn = document.getElementById('btn-register');
            btn.innerHTML = '<span>Mendaftarkan...</span>'; btn.disabled = true;
        });
    </script>
</body>
</html>
