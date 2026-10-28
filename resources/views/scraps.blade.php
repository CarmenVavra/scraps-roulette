<x-layouts.app>
    <div x-data="scrapsRoulette({ endpoint: @js(route('recipes.generate')) })">
        {{-- Statusmeldungen für Screenreader (Tags hinzufügen, Laden, Fehler) --}}
        <p class="sr-only" role="status" aria-live="polite" x-text="status"></p>

        {{-- ░░ HERO ░░ --}}
        <section class="border-b-2 border-ink print:hidden" aria-labelledby="hero-title">
            <div class="mx-auto grid max-w-[1240px] gap-8 px-5 py-12 sm:px-8 lg:grid-cols-12 lg:py-20">
                <h1 id="hero-title" class="m-0 break-words font-display text-[clamp(40px,7.5vw,104px)] font-extrabold leading-[0.95] tracking-[-0.03em] lg:col-span-12">
                    Zeig mir deinen <span class="text-accent">Kühlschrank&#8209;Friedhof.</span>
                </h1>
                <p class="m-0 max-w-md self-end text-lg leading-snug text-ink/70 lg:col-span-6">
                    Tipp ein, was noch rumliegt. Ein Sternekoch beschimpft dich dafür – und rettet dich trotzdem.
                </p>
            </div>
        </section>

        <x-ingredient-input />
        <x-roulette-button />

        {{-- ░░ ERGEBNIS ░░ --}}
        <div id="ergebnis" x-show="phase === 'done' && result" x-cloak class="animate-rise">
            <template x-if="result">
                <div>
                    <x-chef-verdict />
                    <x-recipe-card />
                </div>
            </template>
        </div>

        <div x-show="phase === 'error'" x-cloak class="animate-rise">
            <x-chef-error />
        </div>
    </div>
</x-layouts.app>
