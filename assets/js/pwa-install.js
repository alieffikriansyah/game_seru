/**
 * GAME SERU — PWA Service Worker Registration & Install Prompt Handler
 */

(function () {
    'use strict';

    let deferredPrompt = null;
    const installButtons = document.querySelectorAll('#pwa-install-btn');

    // Sembunyikan tombol secara default sampai prompt tersedia
    function hideButtons() {
        installButtons.forEach(btn => {
            btn.style.display = 'none';
        });
    }

    function showButtons() {
        installButtons.forEach(btn => {
            btn.style.display = 'inline-flex';
        });
    }

    // Inisialisasi awal: sembunyikan jika belum ada event prompt
    hideButtons();

    // 1. Registrasi Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            // Path relatif terhadap root project
            const swUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') 
                ? document.querySelector('meta[name="base-url"]').getAttribute('content') + 'service-worker.js'
                : './service-worker.js';

            navigator.serviceWorker.register(swUrl)
                .then(function (registration) {
                    console.log('🎮 [PWA] ServiceWorker terdaftar dengan scope:', registration.scope);
                })
                .catch(function (error) {
                    console.warn('⚠️ [PWA] Gagal mendaftarkan ServiceWorker:', error);
                });
        });
    }

    // 2. Tangkap event beforeinstallprompt
    window.addEventListener('beforeinstallprompt', function (e) {
        // Cegah mini-infobar browser default di mobile
        e.preventDefault();
        deferredPrompt = e;

        // Tampilkan tombol pasang di navbar atau login page
        showButtons();
        console.log('📲 [PWA] Aplikasi siap dipasang ke Home Screen.');
    });

    // 3. Tangani klik tombol pasang
    installButtons.forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!deferredPrompt) return;

            // Tampilkan native prompt install browser
            deferredPrompt.prompt();

            // Tunggu pilihan user
            const choiceResult = await deferredPrompt.userChoice;
            if (choiceResult.outcome === 'accepted') {
                console.log('✅ [PWA] Pengguna menyetujui pemasangan Game Seru.');
                hideButtons();
            } else {
                console.log('❌ [PWA] Pengguna membatalkan pemasangan.');
            }

            // Reset deferredPrompt
            deferredPrompt = null;
        });
    });

    // 4. Deteksi aplikasi telah terpasang
    window.addEventListener('appinstalled', function () {
        console.log('🎉 [PWA] Game Seru berhasil dipasang di perangkat!');
        hideButtons();
        deferredPrompt = null;
    });

    // 5. Cek apakah sudah running dalam mode standalone / PWA terpasang
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        hideButtons();
    }
})();
