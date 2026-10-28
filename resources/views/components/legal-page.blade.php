@props(['kicker', 'title'])

{{-- Gemeinsamer Rahmen für Impressum und Datenschutzerklärung. --}}
<section class="border-b-2 border-ink">
    <div class="mx-auto max-w-[1240px] px-5 py-12 sm:px-8 lg:py-16">
        <p class="m-0 text-sm font-extrabold uppercase tracking-[0.08em] text-accent-700">{{ $kicker }}</p>
        <h1 class="m-0 mt-2 font-display text-5xl font-extrabold leading-none tracking-tight sm:text-6xl">{{ $title }}</h1>
    </div>
</section>
<div class="mx-auto max-w-[1240px] px-5 py-10 sm:px-8 lg:py-14">
    <div class="max-w-3xl text-base leading-relaxed sm:text-lg [&_h2]:mb-3 [&_h2]:mt-10 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-extrabold [&_h2]:leading-tight [&_li]:mt-1 [&_p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-6">
        {{ $slot }}
    </div>
    <p class="mt-12">
        <a href="{{ route('home') }}" class="font-extrabold underline underline-offset-4">← Zurück zum Roulette</a>
    </p>
</div>
