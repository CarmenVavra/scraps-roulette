{{-- 02 – Roulette. data-state: idle | loading | done | error --}}
<section id="roulette" class="border-b-2 border-ink print:hidden" aria-labelledby="roulette-title">
    <div class="mx-auto grid max-w-[1240px] gap-8 px-5 py-10 sm:px-8 lg:grid-cols-12 lg:gap-12 lg:py-14">
        <div class="lg:col-span-4">
            <p class="m-0 text-sm font-extrabold uppercase tracking-[0.08em] text-accent-700">02 — Aktion</p>
            <h2 id="roulette-title" class="m-0 mt-2 font-display text-4xl font-extrabold leading-none tracking-tight">Roulette</h2>
            <p class="m-0 mt-4 max-w-xs text-base text-ink/70">Die KI sortiert aus, was taugt. Der Rest wird kommentiert.</p>
        </div>
        <div class="lg:col-span-8">
            <button
                id="roulette-btn"
                type="button"
                x-on:click="spin()"
                x-bind:data-state="phase"
                x-bind:disabled="!canSpin"
                x-bind:aria-busy="phase === 'loading'"
                aria-describedby="roulette-hint"
                class="group flex w-full cursor-pointer items-center justify-between gap-6 border-2 px-6 py-6 text-left text-ground focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent disabled:cursor-not-allowed sm:px-10 sm:py-8"
                x-bind:class="phase === 'loading' ? 'animate-pulse border-accent-800 bg-accent-700' : 'border-accent bg-accent hover:bg-accent-600 active:bg-accent-700 disabled:opacity-45 disabled:hover:bg-accent'"
            >
                <span class="flex flex-col items-start gap-1 text-left">
                    <span class="text-xs font-extrabold uppercase tracking-[0.1em] opacity-90" x-text="btnKicker">Roulette drehen</span>
                    <span class="font-display text-3xl font-extrabold leading-none tracking-tight sm:text-5xl" x-text="btnLabel">Chefkoch herausfordern</span>
                </span>
                {{-- Rotierendes Rad: Klasse am SVG tauschen = Ladeanimation --}}
                <svg
                    id="roulette-wheel"
                    class="h-16 w-16 shrink-0 sm:h-[88px] sm:w-[88px]"
                    x-bind:class="phase === 'loading' ? 'animate-[spin_.7s_linear_infinite]' : 'transition-transform duration-500 group-hover:rotate-[135deg]'"
                    viewBox="0 0 48 48"
                    fill="none"
                    aria-hidden="true"
                >
                    <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="2.5"/>
                    <path fill="currentColor" d="M24 24L24 2A22 22 0 0 1 39.56 8.44ZM24 24L46 24A22 22 0 0 1 39.56 39.56ZM24 24L24 46A22 22 0 0 1 8.44 39.56ZM24 24L2 24A22 22 0 0 1 8.44 8.44Z"/>
                    <circle cx="24" cy="24" r="4.5" class="fill-accent" stroke="currentColor" stroke-width="2.5"/>
                </svg>
            </button>
            <p id="roulette-hint" class="m-0 mt-3 text-sm text-ink/70" x-text="btnHint">Mindestens zwei Zutaten, sonst lacht der Chef nur.</p>
        </div>
    </div>
</section>
