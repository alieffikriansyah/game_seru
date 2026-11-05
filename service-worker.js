// =============================================================
// Game Seru PWA — Service Worker
// Version: 1.0.0 (Real-Time Multiplayer PWA)
// =============================================================

const CACHE_VERSION = 'game-seru-v1.2.0';
const BASE_PATH = new URL('./', self.location).pathname;
const OFFLINE_PAGE = BASE_PATH + 'offline.html';

// Static assets pre-cached on install
const STATIC_ASSETS = [
    BASE_PATH,
    BASE_PATH + 'manifest.json',
    BASE_PATH + 'offline.html',
    BASE_PATH + 'assets/gambargame.png',
    BASE_PATH + 'assets/css/game.css',
    BASE_PATH + 'assets/js/pwa-install.js',
    BASE_PATH + 'assets/icons/icon-72.png',
    BASE_PATH + 'assets/icons/icon-144.png',
    BASE_PATH + 'assets/icons/icon-192.png',
    BASE_PATH + 'assets/icons/icon-512.png',
];

// ===== Install: Cache core assets and activate immediately =====
self.addEventListener('install', event => {
    console.log('🎮 [SW] Menginstal Game Seru SW:', CACHE_VERSION);
    event.waitUntil(
        caches.open(CACHE_VERSION).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.warn('⚠️ [SW] Beberapa aset gagal di-cache awal:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// ===== Activate: Purge previous caches =====
self.addEventListener('activate', event => {
    console.log('🎮 [SW] Mengaktifkan Game Seru SW:', CACHE_VERSION);
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(k => {
                    if (k !== CACHE_VERSION) {
                        console.log('🧹 [SW] Menghapus cache versi lama:', k);
                        return caches.delete(k);
                    }
                })
            )
        ).then(() => self.clients.claim())
    );
});

// ===== Fetch: Static vs Dynamic Routing =====
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // 1. Abaikan selain GET (POST, PUT, DELETE selalu langsung ke server)
    if (event.request.method !== 'GET') return;

    // 2. Abaikan external / cross-origin
    if (url.origin !== location.origin) return;

    // 3. JANGAN PERNAH CACHE endpoint real-time polling atau JSON API
    if (
        url.pathname.includes('/poll') || 
        url.pathname.includes('/poll_waiting') ||
        url.searchParams.has('client_version') ||
        url.searchParams.has('last_chat_id') ||
        url.searchParams.has('_t')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    // 4. Static assets (CSS, JS, Fonts, Images) -> Cache-first
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // 5. HTML / Halaman View Navigasi -> Network-first dengan fallback offline
    event.respondWith(networkFirst(event.request));
});

function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|ico|webp)$/i.test(pathname);
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok && response.status === 200) {
            const cache = await caches.open(CACHE_VERSION);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('Aset tidak tersedia secara offline.', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok && response.status === 200 && response.type === 'basic') {
            const cache = await caches.open(CACHE_VERSION);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        const offline = await caches.match(OFFLINE_PAGE);
        if (offline) return offline;

        return new Response('Halaman tidak tersedia secara offline.', {
            status: 503,
            headers: { 'Content-Type': 'text/plain; charset=utf-8' }
        });
    }
}

// Allow clients to trigger skipWaiting
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
