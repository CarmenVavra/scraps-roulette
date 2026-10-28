# Handoff: ScrapsRoulette (Laravel + Tailwind)

## Overview
Single-Page-Interface für **ScrapsRoulette**: Der Nutzer tippt beliebig viele Lebensmittel-Reste ein, eine KI wählt die brauchbaren aus und liefert ein Rezept – im Ton eines arroganten, sarkastischen 5-Sterne-Kochs. Ergebnis in zwei klar getrennten Teilen: **A – Das Urteil des Chefkochs** (Rant) und **B – Die kulinarische Rettung** (sachliche Rezeptkarte).

## About the Design Files
`design/ScrapsLotto.dc.html` ist eine **HTML-Design-Referenz** (Prototyp mit Demo-Logik), kein Produktionscode. Aufgabe: das Design in der bestehenden Laravel-Umgebung nachbauen – Blade-Views, Tailwind-Build (Vite), Livewire oder Alpine.js für die Interaktion. Das Markup in der Datei ist bereits reines Tailwind (Utility-Klassen) und kann nahezu 1:1 in Blade übernommen werden; die Datei-Wrapper (`<x-dc>`, `{{ }}`-Platzhalter, `<sc-for>`, `<sc-if>`) und die JS-Klasse am Ende sind nur Prototyp-Mechanik und werden durch Blade/Livewire ersetzt.
(Der Dateiname enthält noch den alten Namen „ScrapsLotto“; der App-Name ist **ScrapsRoulette**.)

## Fidelity
**High-fidelity.** Farben, Typografie, Abstände und Zustände sind final. Pixelgenau mit Tailwind nachbauen.
Screenshots (`screenshots/`) sind bei ~909 px Viewport-Breite aufgenommen (unter dem `lg`-Breakpoint, also Einspalten-Layout). Ab ≥1024 px greifen die 12-Spalten-Layouts.

## Stil-Basis
Design-System **„Modernist“**: flach, architektonisch, sichtbares Raster, **Radius 0 überall**, 2-px-Trennlinien, alles linksbündig, Schrift ausschließlich **Archivo**. Der Akzent „Roulette“ im Logo ist Tailwind `orange-600` (bewusst außerhalb des Systems, auf Wunsch).

## Tailwind-Setup (in `tailwind.config.js` übernehmen)
```js
theme: { extend: {
  colors: {
    ink: 'rgb(32 30 29 / <alpha-value>)',     // #201e1d Text
    ground: '#f3f2f2',                         // Seitenhintergrund
    surface: '#eae9e9',
    accent: { DEFAULT: 'rgb(236 48 19 / <alpha-value>)', 100:'#fff2ef', 200:'#ffe0d9', 300:'#ffc4b8', 600:'#dd2b0f', 700:'#ae1800', 800:'#7c1405' },
  },
  fontFamily: { sans: ['Archivo','system-ui','sans-serif'], display: ['Archivo','system-ui','sans-serif'] },
  borderRadius: { DEFAULT:'0', sm:'0', md:'0', lg:'0', xl:'0', full:'0' },
}}
```
Font: Google Fonts `Archivo` Gewichte 400, 600, 800. Zusätzlich benötigte `@keyframes` (ins CSS):
```css
@keyframes shake { 0%,100%{transform:rotate(0)} 20%{transform:rotate(-6deg) translateX(-4px)} 40%{transform:rotate(5deg) translateX(4px)} 60%{transform:rotate(-4deg)} 80%{transform:rotate(3deg)} }
@keyframes rise { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:none} }
@media (prefers-reduced-motion:reduce){ *{animation:none!important} }
```
Hinweis: Dynamisch gesetzte Klassen (Ladezustand, abgehakte Schritte) müssen im Tailwind-`content`-Scan vorkommen – am besten vollständig als String im Blade/PHP ausschreiben (nicht zusammensetzen).

## Design Tokens
- Farben: Text `#201e1d`, Ground `#f3f2f2`, Surface `#eae9e9`, Weiß `#ffffff` (Eingabefeld, Tags, Rezeptkarte), Akzent `#ec3013`, Hover `#dd2b0f`, Pressed `#ae1800`/`#7c1405`, Akzent-Text auf hellem Grund `#ae1800` (accent-700), Tint `#fff2ef`, Orange (Logo) `#ea580c`.
- Linien: 2 px `border-ink` (Sektionstrenner, Karten); Zeilentrenner in der Karte `rgba(32,30,29,.15)`; Raster-Linie unter Tag-Header `rgba(32,30,29,.4)`.
- Container: `max-w-[1240px] mx-auto px-5 sm:px-8`.
- Radius: 0. Schatten: keine.
- Typo (Archivo): H1 `clamp(40px, 7.5vw, 104px)`/800, leading .95, tracking −0.03em · H2 36 px/800 · Kicker 14 px/800 uppercase tracking .08em · Body 16–20 px/400–600 · Label 12 px/800 uppercase tracking .08em.

## Screens / Views (Single Page, Sektionen von oben nach unten)

### Header
Höhe ~68 px, `border-b-2`. Links Logo: 36×36 Quadrat `bg-accent` mit „S“ (ground, 18 px/800) + Wortmarke „Scraps<em class=italic text-orange-600>Roulette</em>“ (20 px/800). Rechts (ab `sm`) Tagline „Restekoch mit Charakter · Beschwerden unerwünscht“ 14 px, `text-ink/60`, mit `border-l-2`.

### Hero (`01-hero.png`)
H1 „Zeig mir deinen **Kühlschrank‑Friedhof.**“ (zweiter Teil `text-accent`), volle Breite (`lg:col-span-12`), darunter Absatz „Tipp ein, was noch rumliegt. Ein Sternekoch beschimpft dich dafür – und rettet dich trotzdem.“ (18 px, `text-ink/70`, `max-w-md`). Padding `py-12 lg:py-20`, `border-b-2`.

### 01 – Zutaten‑Friedhof / Eingabe (`02-eingabe-zutaten-friedhof.png`)
Grid `lg:grid-cols-12 gap-8 lg:gap-12`, `py-10 lg:py-14`, `border-b-2`.
- **Links (col-span-4):** Kicker „01 — EINGABE“ (`text-accent-700`), H2 „Zutaten‑Friedhof“, Hilfetext, großer Zähler (60 px/800, tabular-nums) + Label „Leichen im Kühlschrank“ (Singular „Leiche …“ bei 1).
- **Rechts (col-span-8), `data-component="ingredient-input"`:**
  - Label „WAS LIEGT NOCH RUM?“ (12 px uppercase).
  - Input (`border-2 border-ink bg-white px-5 py-4 text-xl`, Placeholder „z. B. halber Blumenkohl, harte Brötchen …“) + Button „Beerdigen“ (`bg-ink text-ground`, Plus-Icon, linksbündiges Label, `sm:min-w-[170px]`; auf Mobil untereinander, ab `sm` in einer Reihe mit `-ml-[2px]`).
  - **Enter oder Komma** fügt hinzu; Komma-/Zeilenumbruch-getrennte Eingaben werden gesplittet, Duplikate ignoriert, Whitespace getrimmt.
  - Kopfzeile der Liste: „AUF DEM FRIEDHOF“ links; rechts Textlinks „Beispiel-Reste“ (`text-accent-700`) und „Alle löschen“ (`text-ink/60`), `border-b-2` (40 % ink).
  - **Tag-Raster:** `<ul class="flex flex-wrap gap-2">` – wächst unbegrenzt in Zeilen. Tag: `border-2 border-ink bg-white font-semibold`, Text `px-3 py-1.5`, rechts Löschen-Button 36 px breit mit `border-l-2`, `bg-surface`, X-Icon (14 px, stroke 3); Hover `bg-accent text-ground`, Active `bg-accent-700`. `aria-label="<Zutat> entfernen"`; Liste `aria-live="polite"`.
  - **Empty State:** `border-2 border-dashed border-ink/40`, Text „Hier liegt noch nichts. Traurig. Oder dein Kühlschrank ist erschreckend sauber.“

### 02 – Roulette / Action (`03-…idle.png`, `04-…loading.png`)
Gleiches 12er-Grid. Links Kicker „02 — AKTION“, H2 „Roulette“, Text „Die KI sortiert aus, was taugt. Der Rest wird kommentiert.“ Rechts der **Roulette-Button** (`id="roulette-btn"`, `data-state="idle|loading|done"`):
- Vollbreit, `border-2 border-accent bg-accent text-ground`, `px-6 py-6 sm:px-10 sm:py-8`, Label linksbündig; links Kicker (12 px/800 uppercase) + Label (30 px / `sm:48 px`, 800); rechts SVG-Rad 88×88 (8 Segmente: 4 gefüllte Keile, Ring, Mittelpunkt in Akzentfarbe).
- **Idle:** Kicker „ROULETTE DREHEN“, Label „Chefkoch herausfordern“; Hover `bg-accent-600`, Active `bg-accent-700`, Rad dreht bei Hover 135° (`transition-transform duration-500`).
- **Loading:** Kicker „EINEN MOMENT …“, Label „Chef kontrolliert deinen Müll …“, Button `animate-pulse bg-accent-700 border-accent-800`, Rad `animate-[spin_.7s_linear_infinite]`, Button `disabled`. Hinweis darunter: „Die KI zählt Schimmelsporen.“
- **Done:** Kicker „NEUER VERSUCH“, Label „Nochmal herausfordern“.
- **Disabled (<2 Zutaten):** `opacity-45`, Hinweis „Mindestens zwei Zutaten, sonst lacht der Chef nur.“ Sonst: „N Zutaten im Topf. Der Chef nimmt, was er braucht.“
- Fokus: `outline-2 outline-offset-4 outline-accent`.

### 03 – A: Das Urteil des Chefkochs (`05-…`, `06-…`)
Vollbreites **rotes Feld** (`bg-accent`, `border-b-2`, `py-12 lg:py-20`), Einblend-Animation `rise .5s`. Kicker „03 — DAS URTEIL DES CHEFKOCHS“ in `text-ground`.
- **Koch-Spalte (col-span-3):** 👨‍🍳 (120 px / `lg:180 px`) mit `shake .6s ×2`, 💢 oben rechts, 12° gedreht. Daneben „Chef Étienne Grandmal“ (24 px/800) und „3 Sterne · 0 Geduld“, Text `text-ground`.
- **Sprechblase (col-span-9):** `bg-ground border-2 border-ink p-6 sm:p-10`, um −0.6° gedreht; ab `lg` linkes Dreieck als „Schwanz“ (`clip-path: polygon(100% 0,100% 100%,0 50%)`, 40×48 px, `bg-ground`). Inhalt bewusst „chaotisch“: erste Zeile 48–60 px/800 UPPERCASE mit schwarzem, −2° gedrehtem Highlight („MEINE GÜTE.“, `bg-ink text-ground`); Fließtext 24–30 px/600 mit Markierungen: fett `text-accent-700`, Block „TATORT.“ (`bg-accent`, +1°), durchgestrichen „Gummibärchen“, großes „NEIN.“ (36–48 px/800, `accent-700`), unterstrichen mit 5-px-Akzentlinie („Schreib mit, du Genie.“). Fuß: `border-t-2`, „— CHEF ÉTIENNE GRANDMAL“ + gedrehter Stempel „URTEIL: UNGENÜGEND“ (`border-2 border-accent-700 text-accent-700`, −4°).
- Der Rant-Text kommt später aus der KI (Struktur: Absätze; Hervorhebungen idealerweise als Markup/Markdown-Marker vom Modell liefern lassen).

### 04 – B: Die kulinarische Rettung / Rezeptkarte (`07-…`, `08-…`, `09-…`)
Visuell komplett getrennt vom Rant: normaler Ground, Kicker „04 — DIE KULINARISCHE RETTUNG“ (`accent-700`), Karte `data-component="recipe-card"`: `bg-white border-2 border-ink`.
- **Kopf (`border-b-2`, 12er-Grid):** links (col-span-8) Titel 30–48 px/800 („Blumenkohl im Senf‑Brösel‑Mantel mit Eierstich“) + Untertitel („Aus sechs Resten wird ein Teller, den du nicht verdient hast.“); rechts (col-span-4, `border-l-2`) 3 Meta-Zellen **Zeit / Portionen / Level** (Label 12 px, Wert 24–30 px/800), getrennt durch `border-r-2`.
- **Körper (12er-Grid):** links col-span-4 (`border-r-2`): „VERWENDET · N“ – Liste Name (18 px/600) + Menge rechts (`text-ink/60`, tabular-nums), Zeilentrenner `border-b-2` 15 % ink; darunter „AUSSORTIERT · N“ – Zutat durchgestrichen (`line-through decoration-accent decoration-2`, `text-ink/50`) + Chef-Begründung (14 px) darunter. Rechts col-span-8: „ZUBEREITUNG“ + Fortschritt „2 / 5 erledigt“; **Schritte als Checkliste**: jede Zeile ein ganzflächiger Button (Hover `bg-ground`), 32-px-Checkbox (`border-2 border-ink bg-white`; erledigt: `bg-accent border-accent`, Haken in `text-ground`), Kicker „SCHRITT n“ (`accent-700`, 12 px/800) + Text 18–20 px; erledigt = `line-through text-ink/50`. `aria-pressed` gesetzt. Darunter Chef-Tipp-Box (`bg-accent-100 border-2 border-ink p-4`).
- **Fuß (`border-t-2`):** Buttons „Nochmal drehen“ (primary, `bg-accent text-ground`) und „Rezept drucken“ (outline, `border-ink`), Label linksbündig; Hover/Active über Akzent-Rampe bzw. `ink/10`, `ink/20`.

### Footer
`border-t-2`, 14 px, `text-ink/60`: links „Scraps*Roulette* · Gebaut aus Resten“, rechts „Rezepte ohne Gewähr, Beleidigungen mit Absicht.“

## Interactions & Behavior
- Hinzufügen/Entfernen von Tags sofort ohne Reload; Eingabefeld wird nach dem Hinzufügen geleert.
- Klick auf Roulette → Phase `loading` (Rad dreht, Button pulsiert, disabled) → Request ans Backend → bei Antwort Phase `done`, Ergebnisbereich (`#ergebnis`) einblenden und sanft zu `#urteil` scrollen (`window.scrollTo({behavior:'smooth'})`).
- Prototyp simuliert die KI mit 3 s Timeout und statischen Daten.
- Schritte abhaken ist rein clientseitig (Zähler „x / n erledigt“); Reset bei neuem Roulette.
- „Rezept drucken“ → `window.print()` (ggf. Print-CSS: Header/Eingabe ausblenden, nur Karte).
- Fehlerzustand fehlt im Prototyp: Vorschlag – Fehlermeldung als Chef-Zitat im Ergebnisbereich, Button zurück auf Idle.
- Reduced Motion: Animationen werden deaktiviert.
- Responsive: Mobile-first, Raster ab `lg` (1024 px) 12-spaltig; darunter einspaltig.

## State Management
`ingredients: string[]`, `input: string`, `phase: 'idle'|'loading'|'done'`, `doneSteps: Record<index, bool>`, `result: { rant, title, time, servings, level, used[{name,amount}], rejected[{name,why}], steps[], tip }`.
Livewire-Mapping: `wire:model="input"` + `wire:keydown.enter` / `wire:keydown.comma.prevent="add"`, `wire:click="remove(i)"`, `wire:click="spin"` mit `wire:loading`/`wire:target="spin"` (Button-Zustand über `data-state` oder `wire:loading.class`), Ergebnis via `@if($result)`. Schritte-Häkchen mit Alpine (`x-data`) genügen.
Backend: Endpoint nimmt die Zutatenliste, ruft die KI mit Prompt „arroganter 5-Sterne-Koch“ auf und sollte **strukturiertes JSON** (Felder wie oben) zurückgeben, damit Rant und Rezeptkarte sauber getrennt gerendert werden.

## Modularisierung (Blade-Vorschlag)
`resources/views/components/` → `ingredient-input.blade.php`, `roulette-button.blade.php`, `chef-verdict.blade.php`, `recipe-card.blade.php`; Seite `scraps.blade.php` setzt sie zusammen. Die `data-component`-Attribute und `id`s (`friedhof`, `roulette`, `urteil`, `rettung`, `roulette-btn`, `roulette-wheel`) markieren die Schnitte.

## Assets
Keine Bilddateien. Icons (Plus, X, Haken, Roulette-Rad) sind Inline-SVG (Lucide-Stil, stroke, eckige Linienenden). Der Koch ist das Emoji 👨‍🍳 + 💢 (auf Wunsch); später durch eine Illustration ersetzbar. Schrift: Archivo (Google Fonts).

## Files
- `design/ScrapsLotto.dc.html` – kompletter Prototyp (Markup + Demo-Logik, Tailwind-Klassen)
- `screenshots/01-hero.png` … `09-rezeptkarte-schritte-checkliste.png` – Referenzansichten (Hero, Eingabe, Roulette idle/loading, Urteil, Rezeptkarte)
