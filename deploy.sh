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
#   - Lokale Datei .env.production (wird auf dem Server zur .env).
#   - In Plesk: Document Root der Subdomain auf "<DEPLOY_PATH>/public" setzen, PHP >= 8.2.
#
# Alle Werte unten lassen sich per Umgebungsvariable überschreiben, z. B.
#   DEPLOY_HOST=hosting123456.a2e1c.netcup.net DEPLOY_USER=hosting123456 ./deploy.sh

set -euo pipefail

# ─── Konfiguration ──────────────────────────────────────────────────────────
DEPLOY_HOST="${DEPLOY_HOST:-HOSTNAME.netcup.net}"           # SSH-Host aus dem netcup CCP / Plesk
DEPLOY_USER="${DEPLOY_USER:-hostingXXXXXX}"                 # SSH-Benutzer
DEPLOY_PORT="${DEPLOY_PORT:-22}"
DEPLOY_PATH="${DEPLOY_PATH:-scrapsroulette.caryssa.at}"     # App-Ordner relativ zum SSH-Home
DEPLOY_PHP="${DEPLOY_PHP:-php}"                             # PHP-CLI auf dem Server (>= 8.2)
APP_URL="${APP_URL:-https://scrapsroulette.caryssa.at}"
# ────────────────────────────────────────────────────────────────────────────

SKIP_TESTS=0
[[ "${1:-}" == "--skip-tests" ]] && SKIP_TESTS=1

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

step() { printf '\n\033[1;31m▶ %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31m✖ %s\033[0m\n' "$*" >&2; exit 1; }

if [[ "$DEPLOY_HOST" == "HOSTNAME.netcup.net" || "$DEPLOY_USER" == "hostingXXXXXX" ]]; then
    fail "Bitte DEPLOY_HOST und DEPLOY_USER oben in deploy.sh eintragen."
fi
[[ -f .env.production ]] || fail ".env.production fehlt (Vorlage: .env.example)."
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
trap 'rm -rf "$STAGE"' EXIT
git archive HEAD | tar -x -C "$STAGE"
cp -r public/build "$STAGE/public/build"
rm -rf "$STAGE/tests" "$STAGE/UI_Mockups" "$STAGE/.claude" "$STAGE/node_modules" \
       "$STAGE/deploy.sh" "$STAGE"/*.bat "$STAGE/phpunit.xml" "$STAGE/phpstan.neon" "$STAGE/CLAUDE.md"
composer install --working-dir="$STAGE" --no-dev --optimize-autoloader --classmap-authoritative \
    --no-interaction --no-progress --no-scripts
echo "$REVISION" > "$STAGE/REVISION"
tar -czf "$STAGE.tar.gz" -C "$STAGE" .
echo "Paketgröße: $(du -h "$STAGE.tar.gz" | cut -f1)"

SSH_OPTS=(-p "$DEPLOY_PORT" -o BatchMode=yes -o ConnectTimeout=15)
TARGET="$DEPLOY_USER@$DEPLOY_HOST"

step "Hochladen nach $TARGET"
scp -P "$DEPLOY_PORT" -o BatchMode=yes -q "$STAGE.tar.gz" "$TARGET:scraps-release.tar.gz"
scp -P "$DEPLOY_PORT" -o BatchMode=yes -q .env.production "$TARGET:scraps-release.env"
rm -f "$STAGE.tar.gz"

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
"\$PHP" artisan optimize:clear
"\$PHP" artisan optimize
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
