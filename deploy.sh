#!/usr/bin/env bash
#
# ScrapsRoulette – Deployment auf netcup Webhosting (SSH).
#
# Aufruf (in Git Bash oder WSL, im Projektordner):
#   ./deploy.sh               Tests + Checks, Build, Upload, Aktivierung
#   ./deploy.sh --skip-tests  ohne Tests/Checks (nur im Notfall)
#
# Ablauf:
#   1. Prüft, dass alles committet ist (deployt wird immer der Stand von HEAD).
#   2. Führt Tests, Pint und PHPStan aus und baut die Frontend-Assets.
#   3. Baut ein Release (ohne Dev-Abhängigkeiten) in einem temporären Ordner.
#   4. Lädt Release und .env.production per SSH hoch und aktiviert es:
#      Wartungsmodus → Dateien tauschen → Migrationen → Caches → online.
#
# Voraussetzungen:
#   - SSH-Zugang zum netcup Webhosting mit SSH-Key (keine Passwortabfrage).
#   - Lokale Datei .env.production (gitignored, wird auf dem Server zur .env). Sie enthält
#     auch die SSH-Zugangsdaten (DEPLOY_HOST, DEPLOY_USER, …), damit diese nicht im
#     öffentlichen Repository landen.
#   - In Plesk für die Subdomain:
#       Dokumentstamm  = <DEPLOY_PATH>/public
#       PHP            = 8.3 (mind. 8.2)
#       open_basedir   = {WEBSPACEROOT}{/}{:}{TMP}{/}   (sonst 500er, PHP sieht nur public/)
#       SSL            = Let's Encrypt + HTTP → HTTPS umleiten

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

step() { printf '\n\033[1;31m▶ %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31m✖ %s\033[0m\n' "$*" >&2; exit 1; }

[[ -f .env.production ]] || fail ".env.production fehlt (Vorlage: .env.example)."

# Liest einen Wert aus .env.production; Umgebungsvariablen haben Vorrang.
env_value() {
    local value
    value="$(grep -E "^$1=" .env.production | tail -n1 | cut -d= -f2- | sed -e 's/^["'\'']//' -e 's/["'\'']$//' || true)"
    printf '%s' "${!1:-$value}"
}

# ─── Konfiguration (aus .env.production) ────────────────────────────────────
DEPLOY_HOST="$(env_value DEPLOY_HOST)"                      # SSH-Host aus dem netcup CCP / Plesk
DEPLOY_USER="$(env_value DEPLOY_USER)"                      # SSH-Benutzer
DEPLOY_PORT="$(env_value DEPLOY_PORT)"; DEPLOY_PORT="${DEPLOY_PORT:-22}"
DEPLOY_PATH="$(env_value DEPLOY_PATH)"                      # App-Ordner relativ zum SSH-Home
DEPLOY_PATH="${DEPLOY_PATH:-scrapsroulette.caryssa.at/scrapsroulette-app}"
DEPLOY_PHP="$(env_value DEPLOY_PHP)"; DEPLOY_PHP="${DEPLOY_PHP:-php}"   # PHP-CLI (>= 8.2)
APP_URL="$(env_value APP_URL)"
# ────────────────────────────────────────────────────────────────────────────

SKIP_TESTS=0
[[ "${1:-}" == "--skip-tests" ]] && SKIP_TESTS=1

[[ -n "$DEPLOY_HOST" && -n "$DEPLOY_USER" ]] || fail "DEPLOY_HOST und DEPLOY_USER in .env.production eintragen."
[[ "$APP_URL" == https://* ]] || fail "APP_URL in .env.production muss mit https:// beginnen."
grep -qE '^ANTHROPIC_API_KEY=.+' .env.production || fail "ANTHROPIC_API_KEY ist in .env.production leer."
grep -qE '^APP_KEY=base64:' .env.production || fail "APP_KEY fehlt in .env.production (php artisan key:generate --show)."

step "Git-Stand prüfen"
if [[ -n "$(git status --porcelain)" ]]; then
    git status --short
    fail "Es gibt nicht committete Änderungen. Erst committen, dann deployen."
fi
REVISION="$(git rev-parse --short HEAD)"
echo "Deploye Commit $REVISION"

if [[ $SKIP_TESTS -eq 0 ]]; then
    step "Tests, Code-Style und statische Analyse"
    php artisan test
    ./vendor/bin/pint --test
    ./vendor/bin/phpstan analyse --memory-limit=1G --no-progress
fi

step "Frontend bauen"
npm install --no-audit --no-fund
npm run build

step "Release zusammenstellen"
STAGE="$(mktemp -d)"
trap 'rm -rf "${STAGE:?}" "${STAGE:?}.tar.gz"' EXIT
git archive HEAD | tar -x -C "$STAGE"
cp -r public/build "$STAGE/public/build"
rm -rf "${STAGE:?}/tests" "${STAGE:?}/UI_Mockups" "${STAGE:?}/.claude" "${STAGE:?}/node_modules" \
       "${STAGE:?}/deploy.sh" "${STAGE:?}"/*.bat "${STAGE:?}/phpunit.xml" "${STAGE:?}/phpstan.neon" "${STAGE:?}/CLAUDE.md"
composer install --working-dir="$STAGE" --no-dev --optimize-autoloader \
    --no-interaction --no-progress --no-scripts
echo "$REVISION" > "$STAGE/REVISION"
tar -czf "$STAGE.tar.gz" -C "$STAGE" .
echo "Paketgröße: $(du -h "$STAGE.tar.gz" | cut -f1)"

SSH_OPTS=(-p "$DEPLOY_PORT" -o BatchMode=yes -o ConnectTimeout=15)
TARGET="$DEPLOY_USER@$DEPLOY_HOST"

step "Hochladen nach $TARGET"
scp -P "$DEPLOY_PORT" -o BatchMode=yes -q "$STAGE.tar.gz" "$TARGET:scraps-release.tar.gz"
scp -P "$DEPLOY_PORT" -o BatchMode=yes -q .env.production "$TARGET:scraps-release.env"

step "Release auf dem Server aktivieren"
# shellcheck disable=SC2087 # Variablen werden bewusst lokal eingesetzt.
ssh "${SSH_OPTS[@]}" "$TARGET" bash -s <<REMOTE
set -euo pipefail
PHP="$DEPLOY_PHP"
"\$PHP" -r 'exit(PHP_VERSION_ID >= 80200 ? 0 : 1);' || { echo "PHP-CLI '\$PHP' ist älter als 8.2 – DEPLOY_PHP anpassen."; exit 1; }

mkdir -p "$DEPLOY_PATH"
cd "$DEPLOY_PATH"

if [ -f artisan ] && [ -f .env ]; then
    "\$PHP" artisan down --retry=15 || true
    trap '"\$PHP" artisan up || true' EXIT
fi

# Code ersetzen, Laufzeitdaten (storage, SQLite-Datenbank) behalten.
rm -rf app bootstrap/cache config database/migrations database/seeders database/factories \
       lang public/build resources routes vendor
tar -xzf ~/scraps-release.tar.gz -C .
mv -f ~/scraps-release.env .env
chmod 600 .env
rm -f ~/scraps-release.tar.gz

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
         storage/framework/views storage/logs bootstrap/cache
[ -f database/database.sqlite ] || touch database/database.sqlite
chmod -R u+rwX,g+rwX storage bootstrap/cache database

"\$PHP" artisan package:discover --ansi
"\$PHP" artisan migrate --force
# Bewusst keine Caches (config/route/view/event): Die SSH-Shell ist chrooted und sieht andere
# absolute Pfade als der Webserver. Selbst der Routen-Cache enthält solche Pfade (z. B. /up)
# und führt dann zu open_basedir-Fehlern. Für eine Seite dieser Größe ist das ohne Belang.
"\$PHP" artisan optimize:clear
"\$PHP" artisan up
trap - EXIT
echo "Release \$(cat REVISION) ist aktiv."
REMOTE

step "Erreichbarkeit prüfen"
if curl -fsS -o /dev/null --max-time 20 "$APP_URL/up"; then
    printf '\n\033[1;32m✔ ScrapsRoulette %s ist online: %s\033[0m\n' "$REVISION" "$APP_URL"
else
    fail "Health-Check $APP_URL/up fehlgeschlagen. Logs: storage/logs auf dem Server."
fi
