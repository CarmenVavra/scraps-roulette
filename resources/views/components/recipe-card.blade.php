{{-- 04 – B: Die kulinarische Rettung (Rezeptkarte). Schritte abhaken ist rein clientseitig. --}}
<section id="rettung" class="py-12 lg:py-20 print:py-0" aria-labelledby="rettung-kicker">
    <div class="mx-auto max-w-[1240px] px-5 sm:px-8 print:px-0">
        <p id="rettung-kicker" class="m-0 mb-8 text-sm font-extrabold uppercase tracking-[0.08em] text-accent-700 print:hidden">04 — Die kulinarische Rettung</p>

        <article class="border-2 border-ink bg-white" data-component="recipe-card" aria-labelledby="rezept-title">
            <header class="grid border-b-2 border-ink lg:grid-cols-12">
                <div class="p-6 sm:p-10 lg:col-span-8">
                    <h2 id="rezept-title" class="m-0 break-words font-display text-3xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl" x-text="result.recipe_title"></h2>
                    <p class="m-0 mt-3 max-w-xl text-lg text-ink/70" x-show="result.recipe_subtitle" x-text="result.recipe_subtitle"></p>
                </div>
                <dl class="m-0 grid grid-cols-3 border-t-2 border-ink lg:col-span-4 lg:border-l-2 lg:border-t-0">
                    <div class="border-r-2 border-ink p-4 sm:p-6">
                        <dt class="text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Zeit</dt>
                        <dd class="m-0 mt-1 font-display text-2xl font-extrabold sm:text-3xl" x-text="result.prep_time"></dd>
                    </div>
                    <div class="border-r-2 border-ink p-4 sm:p-6">
                        <dt class="text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Portionen</dt>
                        <dd class="m-0 mt-1 font-display text-2xl font-extrabold sm:text-3xl" x-text="result.servings || '–'"></dd>
                    </div>
                    <div class="p-4 sm:p-6">
                        <dt class="text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Level</dt>
                        <dd class="m-0 mt-1 font-display text-2xl font-extrabold sm:text-3xl" x-text="result.difficulty || '–'"></dd>
                    </div>
                </dl>
            </header>

            <div class="grid lg:grid-cols-12">
                {{-- Zutaten --}}
                <div class="border-b-2 border-ink p-6 sm:p-10 lg:col-span-4 lg:border-b-0 lg:border-r-2">
                    <h3 class="m-0 text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Verwendet · <span x-text="usedRows.length"></span></h3>
                    <ul class="m-0 mt-4 list-none p-0">
                        <template x-for="(row, i) in usedRows" x-bind:key="i">
                            <li class="flex items-baseline justify-between gap-4 border-b-2 border-ink/15 py-3 text-lg">
                                <span class="font-semibold" x-text="row.name"></span>
                                <span class="text-right text-ink/70 tabular-nums" x-text="row.amount"></span>
                            </li>
                        </template>
                    </ul>

                    <template x-if="rejectedRows.length > 0">
                        <div>
                            <h3 class="m-0 mt-8 text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Aussortiert · <span x-text="rejectedRows.length"></span></h3>
                            <ul class="m-0 mt-4 flex list-none flex-col gap-3 p-0">
                                <template x-for="(row, i) in rejectedRows" x-bind:key="i">
                                    <li class="text-base">
                                        <span class="font-semibold text-ink/60 line-through decoration-accent decoration-2"><span class="sr-only">Aussortiert: </span><span x-text="row.name"></span></span>
                                        <span class="block text-sm text-ink/70" x-text="'„' + row.reason + '“'"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>

                {{-- Schritte --}}
                <div class="p-6 sm:p-10 lg:col-span-8">
                    <div class="flex items-baseline justify-between gap-4">
                        <h3 class="m-0 text-xs font-extrabold uppercase tracking-[0.08em] text-ink/70">Zubereitung</h3>
                        <p class="m-0 text-sm font-semibold tabular-nums text-ink/70 print:hidden" x-text="progress"></p>
                    </div>
                    <ol class="m-0 mt-4 list-none p-0">
                        <template x-for="(step, i) in result.steps" x-bind:key="i">
                            <li class="border-b-2 border-ink/15">
                                <button
                                    type="button"
                                    x-on:click="toggleStep(i)"
                                    x-bind:aria-pressed="doneSteps[i] ? 'true' : 'false'"
                                    class="flex w-full cursor-pointer items-start gap-4 border-0 bg-transparent px-0 py-5 text-left text-ink hover:bg-ground focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                                >
                                    <span
                                        class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center border-2"
                                        x-bind:class="doneSteps[i] ? 'border-accent bg-accent text-ground' : 'border-ink bg-white text-transparent'"
                                        aria-hidden="true"
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="square"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-xs font-extrabold uppercase tracking-[0.08em] text-accent-700" x-text="'Schritt ' + (i + 1)"></span>
                                        <span
                                            class="block text-lg leading-snug sm:text-xl"
                                            x-bind:class="doneSteps[i] ? 'text-ink/60 line-through' : 'text-ink'"
                                            x-text="step"
                                        ></span>
                                    </span>
                                </button>
                            </li>
                        </template>
                    </ol>
                    <p class="m-0 mt-6 border-2 border-ink bg-accent-100 p-4 text-base" x-show="result.chef_tip">
                        <strong>Chef-Tipp:</strong> <span x-text="result.chef_tip"></span>
                    </p>
                </div>
            </div>

            <footer class="flex flex-wrap gap-3 border-t-2 border-ink p-4 sm:p-6 print:hidden">
                <button type="button" x-on:click="spin()" x-bind:disabled="!canSpin" class="flex cursor-pointer items-center gap-2 border-2 border-accent bg-accent px-5 py-3 text-left text-sm font-extrabold text-ground hover:bg-accent-600 active:bg-accent-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent disabled:cursor-not-allowed disabled:opacity-45">Nochmal drehen</button>
                <button type="button" x-on:click="print()" class="flex cursor-pointer items-center gap-2 border-2 border-ink bg-transparent px-5 py-3 text-left text-sm font-extrabold text-ink hover:bg-ink/10 active:bg-ink/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">Rezept drucken</button>
            </footer>
        </article>
    </div>
</section>
