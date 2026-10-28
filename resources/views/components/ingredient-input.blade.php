{{-- 01 – Zutaten-Friedhof. Nutzt den Alpine-Scope von scrapsRoulette(). --}}
<section id="friedhof" class="border-b-2 border-ink print:hidden" aria-labelledby="friedhof-title">
    <div class="mx-auto grid max-w-[1240px] gap-8 px-5 py-10 sm:px-8 lg:grid-cols-12 lg:gap-12 lg:py-14">
        <div class="lg:col-span-4">
            <p class="m-0 text-sm font-extrabold uppercase tracking-[0.08em] text-accent-700">01 — Eingabe</p>
            <h2 id="friedhof-title" class="m-0 mt-2 font-display text-4xl font-extrabold leading-none tracking-tight">Zutaten&#8209;<br>Friedhof</h2>
            <p class="m-0 mt-4 max-w-xs text-base text-ink/70">Unbegrenzt viele Reste. Enter oder Komma schickt sie unter die Erde.</p>
            <p class="m-0 mt-6 font-display text-6xl font-extrabold leading-none tabular-nums" aria-hidden="true" x-text="count">0</p>
            <p class="m-0 mt-1 text-sm text-ink/70">
                <span class="sr-only" x-text="count"></span>
                <span x-text="countLabel">Leichen im Kühlschrank</span>
            </p>
        </div>

        <div class="lg:col-span-8" data-component="ingredient-input">
            <form x-on:submit.prevent="add()" novalidate>
                <label for="zutat" class="mb-2 block text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Was liegt noch rum?</label>
                <div class="flex flex-col sm:flex-row">
                    <input
                        id="zutat"
                        type="text"
                        x-ref="input"
                        x-model="input"
                        x-on:keydown="onKeydown($event)"
                        autocomplete="off"
                        maxlength="400"
                        aria-describedby="zutat-hilfe"
                        placeholder="z. B. halber Blumenkohl, harte Brötchen …"
                        class="min-w-0 flex-1 border-2 border-ink bg-white px-5 py-4 text-xl text-ink placeholder:text-ink/50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                    <button type="submit" class="-mt-[2px] flex cursor-pointer items-center justify-start gap-2 border-2 border-ink bg-ink px-6 py-4 text-left text-base font-extrabold text-ground hover:bg-accent-700 active:bg-accent-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent sm:-ml-[2px] sm:mt-0 sm:min-w-[170px]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        Beerdigen
                    </button>
                </div>
                <p id="zutat-hilfe" class="sr-only">Mehrere Zutaten mit Komma trennen. Enter fügt sie hinzu.</p>
            </form>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-b-2 border-ink/40 pb-2">
                <p class="m-0 text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Auf dem Friedhof</p>
                <div class="flex gap-4">
                    <button type="button" x-on:click="fillDemo()" class="cursor-pointer border-0 bg-transparent p-0 text-sm font-semibold text-accent-700 underline underline-offset-4 hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">Beispiel-Reste</button>
                    <button type="button" x-on:click="clearAll()" x-bind:disabled="count === 0" class="cursor-pointer border-0 bg-transparent p-0 text-sm font-semibold text-ink/70 underline underline-offset-4 hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent disabled:cursor-not-allowed disabled:opacity-50">Alle löschen</button>
                </div>
            </div>

            {{-- Tag-Raster: flex-wrap, wächst unbegrenzt --}}
            <ul id="tag-list" class="m-0 mt-4 flex list-none flex-wrap gap-2 p-0" aria-label="Eingegebene Zutaten" x-show="count > 0" x-cloak>
                <template x-for="(item, index) in ingredients" x-bind:key="item">
                    <li class="flex items-stretch border-2 border-ink bg-white text-base font-semibold">
                        <span class="px-3 py-1.5 break-all" x-text="item"></span>
                        <button type="button" x-on:click="remove(index)" x-bind:aria-label="item + ' entfernen'" class="grid w-9 cursor-pointer place-items-center border-0 border-l-2 border-ink bg-surface text-ink hover:bg-accent hover:text-ground active:bg-accent-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="square" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                    </li>
                </template>
            </ul>
            <p x-show="count === 0" class="m-0 mt-4 border-2 border-dashed border-ink/40 px-5 py-8 text-lg text-ink/70">
                Hier liegt noch nichts. Traurig. Oder dein Kühlschrank ist erschreckend sauber.
            </p>
        </div>
    </div>
</section>
