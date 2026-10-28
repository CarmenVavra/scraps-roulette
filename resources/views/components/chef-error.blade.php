{{-- Fehlerzustand: Fehlermeldung als Chef-Zitat, Button steht wieder auf „Nochmal herausfordern“. --}}
<section id="urteil-fehler" tabindex="-1" class="border-b-2 border-ink bg-ink focus:outline-none print:hidden" aria-labelledby="fehler-title">
    <div class="mx-auto max-w-[1240px] px-5 py-12 sm:px-8 lg:py-16">
        <h2 id="fehler-title" class="m-0 mb-8 text-sm font-extrabold uppercase tracking-[0.08em] text-ground">03 — Küche geschlossen</h2>
        <div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-16">
            <div class="flex items-center gap-4 lg:col-span-3" aria-hidden="true">
                <span class="select-none text-[96px] leading-none">👨‍🍳</span>
                <span class="select-none text-5xl leading-none">🚪</span>
            </div>
            <blockquote class="m-0 -rotate-[0.6deg] border-2 border-ground bg-ground p-6 sm:p-10 lg:col-span-9" role="alert">
                <p class="m-0 font-display text-2xl font-extrabold leading-snug sm:text-3xl" x-text="error"></p>
                <footer class="mt-6 border-t-2 border-ink pt-4 text-sm font-extrabold uppercase tracking-[0.08em]">— Die Küchenbrigade</footer>
            </blockquote>
        </div>
    </div>
</section>
