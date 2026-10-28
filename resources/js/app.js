import '@fontsource/archivo/400.css';
import '@fontsource/archivo/600.css';
import '@fontsource/archivo/800.css';
import '@fontsource/archivo/400-italic.css';
import '@fontsource/archivo/800-italic.css';

import Alpine from 'alpinejs';
import scrapsRoulette from './scraps-roulette';

Alpine.data('scrapsRoulette', scrapsRoulette);

window.Alpine = Alpine;
Alpine.start();

// Service Worker → die Seite lässt sich am Handy als App installieren.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Ohne Service Worker funktioniert die Seite ganz normal weiter.
        });
    });
}
