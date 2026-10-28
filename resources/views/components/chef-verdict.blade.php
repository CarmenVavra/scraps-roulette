{{-- 03 – A: Das Urteil des Chefkochs (result.chef_rant). Rendering nur per x-text → kein XSS. --}}
<section id="urteil" tabindex="-1" class="relative overflow-hidden border-b-2 border-ink bg-accent focus:outline-none print:hidden" aria-labelledby="urteil-title">
    <div class="mx-auto max-w-[1240px] px-5 py-12 sm:px-8 lg:py-20">
        <h2 id="urteil-title" class="m-0 mb-8 text-sm font-extrabold uppercase tracking-[0.08em] text-ground">03 — Das Urteil des Chefkochs</h2>
        <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-16">

            {{-- Koch --}}
            <div class="flex items-end gap-6 lg:col-span-3 lg:flex-col lg:items-start">
                <div class="relative">
                    <div class="inline-block origin-[50%_90%] animate-shake select-none text-[120px] leading-none lg:text-[180px]" aria-hidden="true">👨‍🍳</div>
                    <div class="absolute -right-2 -top-2 rotate-12 select-none text-6xl leading-none lg:-right-6 lg:text-7xl" aria-hidden="true">💢</div>
                </div>
                <div class="text-ground">
                    <p class="m-0 font-display text-2xl font-extrabold leading-tight">Chef Étienne<br>Grandmal</p>
                    <p class="m-0 mt-1 text-base">3 Sterne · 0 Geduld</p>
                </div>
            </div>

            {{-- Sprechblase --}}
            <div class="relative lg:col-span-9">
                <div class="absolute -left-9 top-14 hidden h-12 w-10 bg-ground [clip-path:polygon(100%_0,100%_100%,0_50%)] lg:block" aria-hidden="true"></div>
                <blockquote class="m-0 -rotate-[0.6deg] border-2 border-ink bg-ground p-6 sm:p-10">
                    <template x-for="(paragraph, p) in rantParagraphs" x-bind:key="p">
                        <p
                            x-bind:class="p === 0
                                ? 'm-0 font-display text-3xl font-extrabold uppercase leading-[1.05] tracking-tight break-words sm:text-5xl lg:text-6xl'
                                : 'm-0 mt-6 text-xl font-semibold leading-snug sm:text-2xl lg:text-3xl'"
                        ><template x-for="(token, t) in paragraph" x-bind:key="t"><span x-bind:class="token.cls" x-text="token.text"></span></template></p>
                    </template>
                    <footer class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t-2 border-ink pt-4 text-sm font-extrabold uppercase tracking-[0.08em]">
                        <span>— Chef Étienne Grandmal</span>
                        <span class="inline-block rotate-[-4deg] border-2 border-accent-700 px-2 py-0.5 text-accent-700">Urteil: ungenügend</span>
                    </footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>
