/**
 * Erzeugt die App-Icons (PWA, Apple, Favicon) aus resources/app-icon.svg.
 *
 *   npm run icons
 *
 * Ziel ist public/app-icons/ – bewusst nicht "icons/", weil netcup /icons/
 * serverweit für Apache-Symbole reserviert.
 */
import { copyFileSync, mkdirSync } from 'node:fs';
import sharp from 'sharp';

const source = 'resources/app-icon.svg';
const target = 'public/app-icons';

const sizes = {
    'icon-192.png': 192,
    'icon-512.png': 512,
    'icon-maskable-512.png': 512,
    'apple-touch-icon.png': 180,
    'favicon-32.png': 32,
};

mkdirSync(target, { recursive: true });

for (const [file, size] of Object.entries(sizes)) {
    await sharp(source, { density: 300 }).resize(size, size).png({ compressionLevel: 9 }).toFile(`${target}/${file}`);
    console.log(`✔ ${target}/${file} (${size}×${size})`);
}

copyFileSync(source, `${target}/app-icon.svg`);
console.log(`✔ ${target}/app-icon.svg`);
