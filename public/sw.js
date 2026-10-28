/**
 * Service Worker von ScrapsRoulette.
 *
 * Macht die Seite als App installierbar. Rezepte brauchen immer Internet (KI),
 * daher wird nichts Dynamisches gecacht – nur eine Offline-Seite und die Icons.
 */
const CACHE = 'scrapsroulette-v2';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/app-icons/icon-192.png', '/app-icons/app-icon.svg'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Nur Seitenaufrufe abfangen: online normal laden, offline die Offline-Seite zeigen.
    if (request.method !== 'GET' || request.mode !== 'navigate') {
        return;
    }

    event.respondWith(fetch(request).catch(() => caches.match(OFFLINE_URL)));
});
