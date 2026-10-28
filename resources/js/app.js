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
