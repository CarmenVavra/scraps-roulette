# ScrapsRoulette

Kühlschrank-Reste rein, Rezept raus – mit Beschimpfung. Chef Étienne Grandmal (KI: Anthropic Claude) wählt aus deinen Resten die beste Kombination, beschimpft dich für den Rest und liefert trotzdem ein echtes Rezept.

Live: <https://scrapsroulette.caryssa.at>

## Stack

- Laravel 12 (PHP ≥ 8.2), Blade-Komponenten, Alpine.js, Tailwind CSS 4 (Vite)
- Anthropic PHP SDK (`anthropic-ai/sdk`) mit Structured Outputs (JSON-Schema), Modell Claude Haiku 5.5 (`ANTHROPIC_MODEL`)
- Schrift Archivo lokal eingebunden (`@fontsource/archivo`), keine externen Requests

## Lokal starten

```bat
start.bat
```

Öffnet Backend (`php artisan serve`, Port **8650**) und Vite (Port 8651) und dann <http://localhost:8650>. Beenden mit `stop.bat`.

Vorher in `.env` den `ANTHROPIC_API_KEY` eintragen. Ohne Key antwortet die App mit „Die Küche ist heute geschlossen …“.

## Architektur

| Teil | Datei |
| --- | --- |
| Routen | `routes/web.php` |
| JSON-Endpunkt | `App\Http\Controllers\RecipeController@generate` (`POST /recipes/generate`) |
| Validierung | `App\Http\Requests\GenerateRecipeRequest` |
| KI-Service | `App\Services\RecipeGeneratorService` |
| Fehler im Koch-Stil | `App\Exceptions\ChefRageException` |
| Seite | `resources/views/scraps.blade.php` + `resources/views/components/*` |
| Frontend-Logik | `resources/js/scraps-roulette.js` |
| Impressum / Datenschutz | `resources/views/legal/*`, Daten aus `config/legal.php` (`LEGAL_*` in `.env`) |

### API

`POST /recipes/generate` (CSRF-Token im Header `X-CSRF-TOKEN`, Rate-Limit 5/min und 40/Tag pro IP)

```json
{ "ingredients": ["halber Blumenkohl", "Eier", "Senf"] }
```

Antwort `200`:

```json
{
  "recipe": {
    "chef_rant": "Ach du ==meine Güte.==\n\n**Ein halber Blumenkohl** …",
    "recipe_title": "Blumenkohl im Senf-Brösel-Mantel",
    "used_ingredients": ["halber Blumenkohl", "Eier", "Senf"],
    "prep_time": "35 Min",
    "steps": ["Schritt 1 …", "Schritt 2 …"],
    "recipe_subtitle": "…",
    "servings": "2",
    "difficulty": "Leicht",
    "ingredient_amounts": [{ "name": "Eier", "amount": "3 Stk." }],
    "rejected_ingredients": [{ "name": "Gummibärchen", "reason": "…" }],
    "chef_tip": "…"
  }
}
```

Die ersten fünf Felder sind der Kern-Vertrag; die übrigen füllen die Rezeptkarte aus dem Mockup (Portionen, Level, Mengen, Aussortiert, Chef-Tipp). Im `chef_rant` markieren `**…**`, `==…==`, `!!…!!`, `~~…~~` und `__…__` Hervorhebungen; das Frontend rendert sie per `x-text`, also ohne HTML aus der KI.

Fehler: `422` (Validierung), `429` (Rate-Limit), `503` mit `{ "message": "Der Chefkoch hat vor Wut die Küche verlassen …" }`.

## Qualität

```bash
php artisan test
./vendor/bin/pint --test
./vendor/bin/phpstan analyse --memory-limit=1G
npm run build
```

## Deployment (netcup)

Einmalig in Plesk für `scrapsroulette.caryssa.at`:

1. **Dokumentstamm**: `scrapsroulette.caryssa.at/scrapsroulette-app/public`
2. **PHP 8.3** wählen und in den PHP-Einstellungen `open_basedir` auf `{WEBSPACEROOT}{/}{:}{TMP}{/}` setzen (sonst liefert jede Seite 500).
3. **SSL**: Let's Encrypt aktivieren, „HTTP → HTTPS umleiten“ einschalten.

Lokal in `.env.production` (nicht im Git) `ANTHROPIC_API_KEY`, die `LEGAL_*`-Angaben und die `DEPLOY_*`-Zugangsdaten (SSH-Host, User, Pfad, PHP-CLI) pflegen. SSH läuft über den hinterlegten SSH-Key.

Danach jedes Mal (Git Bash):

```bash
./deploy.sh
```

Das Skript testet, baut, lädt hoch, setzt die `.env`, migriert und prüft `/up`. Laravel-Caches (`config`, `route`, `view`, `event`) werden bewusst nicht verwendet, weil die SSH-Shell bei netcup chrooted ist und andere absolute Pfade sieht als der Webserver.
