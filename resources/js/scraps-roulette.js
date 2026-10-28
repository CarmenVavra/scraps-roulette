/**
 * Alpine-Komponente für die ScrapsRoulette-Seite.
 *
 * Verwaltet die Zutaten-Tags, schickt sie per fetch() an
 * POST /recipes/generate und verteilt die JSON-Antwort auf
 * „Das Urteil des Chefkochs“ (chef_rant) und die Rezeptkarte.
 */

const DEMO_INGREDIENTS = [
    'Blumenkohl (halber)',
    'Eier (3)',
    'Senf',
    'Harte Brötchen',
    'Zwiebel',
    'Butter',
    'Gummibärchen',
    'Joghurt (abgelaufen)',
    'Reste vom Reis',
    'Salatgurke (weich)',
    'Parmesanrinde',
    'Dosenmais',
];

const FALLBACK_ERROR = 'Der Chefkoch hat vor Wut die Küche verlassen. Bitte versuche es noch einmal.';
const REQUEST_TIMEOUT_MS = 120_000;
const MAX_INGREDIENTS = 50;
const MAX_LENGTH = 80;

/**
 * Hervorhebungs-Markierungen im Rant → Stil.
 * Klassen stehen vollständig ausgeschrieben, damit Tailwind sie findet.
 */
const MARKERS = [
    { open: '**', kind: 'bold' },
    { open: '==', kind: 'box' },
    { open: '!!', kind: 'stamp' },
    { open: '~~', kind: 'strike' },
    { open: '__', kind: 'underline' },
];

const TOKEN_CLASSES = {
    text: '',
    bold: 'font-extrabold text-accent-700',
    box: 'inline-block -rotate-2 bg-ink px-2 text-ground',
    stamp: 'inline-block rotate-1 bg-accent px-2 font-extrabold uppercase text-ink',
    strike: 'line-through decoration-[3px]',
    underline: 'font-extrabold underline decoration-accent decoration-[5px] underline-offset-4',
};

/**
 * Zerlegt einen Absatz in Text-Token mit Stil. Erzeugt nur Daten,
 * gerendert wird per x-text – KI-Text landet also nie als HTML im DOM.
 */
export function tokenize(paragraph) {
    const pattern = /(\*\*[^*]+?\*\*|==[^=]+?==|!![^!]+?!!|~~[^~]+?~~|__[^_]+?__)/g;

    return paragraph
        .split(pattern)
        .filter((part) => part !== '')
        .map((part) => {
            const marker = MARKERS.find((m) => part.length > 4 && part.startsWith(m.open) && part.endsWith(m.open));

            return marker
                ? { text: part.slice(2, -2), cls: TOKEN_CLASSES[marker.kind] }
                : { text: part, cls: TOKEN_CLASSES.text };
        });
}

export function splitIngredients(raw) {
    return raw
        .split(/[,\n;]/)
        .map((s) => s.trim().slice(0, MAX_LENGTH))
        .filter(Boolean);
}

export default function scrapsRoulette({ endpoint }) {
    return {
        ingredients: [],
        input: '',
        phase: 'idle', // idle | loading | done | error
        result: null,
        error: null,
        doneSteps: {},
        status: '',

        get count() {
            return this.ingredients.length;
        },

        get countLabel() {
            return this.count === 1 ? 'Leiche im Kühlschrank' : 'Leichen im Kühlschrank';
        },

        get canSpin() {
            return this.count >= 2 && this.phase !== 'loading';
        },

        get btnKicker() {
            if (this.phase === 'loading') return 'Einen Moment …';
            if (this.phase === 'done' || this.phase === 'error') return 'Neuer Versuch';
            return 'Roulette drehen';
        },

        get btnLabel() {
            if (this.phase === 'loading') return 'Chef kontrolliert deinen Müll …';
            if (this.phase === 'done' || this.phase === 'error') return 'Nochmal herausfordern';
            return 'Chefkoch herausfordern';
        },

        get btnHint() {
            if (this.count < 2) return 'Mindestens zwei Zutaten, sonst lacht der Chef nur.';
            if (this.phase === 'loading') return 'Die KI zählt Schimmelsporen.';
            return `${this.count} Zutaten im Topf. Der Chef nimmt, was er braucht.`;
        },

        /** Absätze des Rants; der erste ist die große Überschrift. */
        get rantParagraphs() {
            if (!this.result) return [];

            return this.result.chef_rant
                .split(/\n\s*\n/)
                .map((p) => p.trim())
                .filter(Boolean)
                .map((p) => tokenize(p));
        },

        /** Verwendete Zutaten samt Menge (Mengen sind optional). */
        get usedRows() {
            if (!this.result) return [];
            const amounts = this.result.ingredient_amounts ?? [];

            return this.result.used_ingredients.map((name, i) => ({
                name,
                amount: amounts.find((a) => a.name === name)?.amount ?? amounts[i]?.amount ?? '',
            }));
        },

        get rejectedRows() {
            return this.result?.rejected_ingredients ?? [];
        },

        get progress() {
            const total = this.result?.steps.length ?? 0;
            const done = Object.values(this.doneSteps).filter(Boolean).length;
            return `${done} / ${total} erledigt`;
        },

        add() {
            const parts = splitIngredients(this.input);
            const known = new Set(this.ingredients.map((i) => i.toLowerCase()));
            const added = [];

            for (const part of parts) {
                if (this.ingredients.length >= MAX_INGREDIENTS) {
                    this.status = `Mehr als ${MAX_INGREDIENTS} Zutaten nimmt der Chef nicht an.`;
                    break;
                }
                if (!known.has(part.toLowerCase())) {
                    known.add(part.toLowerCase());
                    this.ingredients.push(part);
                    added.push(part);
                }
            }

            if (added.length) this.status = `${added.join(', ')} beerdigt.`;
            this.input = '';
        },

        onKeydown(event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                this.add();
            }
        },

        remove(index) {
            const [removed] = this.ingredients.splice(index, 1);
            this.status = `${removed} entfernt.`;
            this.$nextTick(() => this.$refs.input.focus());
        },

        fillDemo() {
            this.ingredients = [...DEMO_INGREDIENTS];
            this.status = 'Beispiel-Reste geladen.';
        },

        clearAll() {
            this.ingredients = [];
            this.status = 'Friedhof geleert.';
            this.$refs.input.focus();
        },

        toggleStep(index) {
            this.doneSteps = { ...this.doneSteps, [index]: !this.doneSteps[index] };
        },

        async spin() {
            if (!this.canSpin) return;

            this.phase = 'loading';
            this.error = null;
            this.doneSteps = {};
            this.status = 'Der Chefkoch prüft deine Zutaten.';

            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ ingredients: this.ingredients }),
                    signal: controller.signal,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.recipe) {
                    throw new Error(this.errorMessage(response.status, data));
                }

                this.result = data.recipe;
                this.phase = 'done';
                this.status = `Rezept fertig: ${data.recipe.recipe_title}`;
            } catch (e) {
                this.result = null;
                this.phase = 'error';
                this.error = e.name === 'AbortError' || e instanceof TypeError ? FALLBACK_ERROR : e.message;
                this.status = this.error;
            } finally {
                clearTimeout(timer);
            }

            // x-if rendert das Ergebnis erst nach dem nächsten Tick – kurz warten, dann scrollen.
            this.$nextTick(() => setTimeout(() => this.reveal(), 80));
        },

        errorMessage(status, data) {
            if (status === 419) return 'Die Seite ist eingeschlafen. Lade sie neu und versuch es noch einmal.';
            if (status === 422 && data.errors) return Object.values(data.errors).flat()[0];
            return data.message || FALLBACK_ERROR;
        },

        /** Scrollt sanft zum Urteil (bzw. zur Fehlermeldung) und setzt den Fokus dorthin. */
        reveal() {
            const target = document.getElementById(this.phase === 'error' ? 'urteil-fehler' : 'urteil');
            if (!target) return;

            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({
                top: target.getBoundingClientRect().top + window.scrollY - 8,
                behavior: reduced ? 'auto' : 'smooth',
            });
            target.focus({ preventScroll: true });
        },

        print() {
            window.print();
        },
    };
}
