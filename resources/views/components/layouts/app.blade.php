@props([
    'title' => null,
    'description' => 'Tipp ein, was noch in deinem Kühlschrank liegt. Ein Sternekoch beschimpft dich dafür – und rettet dich mit einem echten Rezept.',
])

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#ec3013">
    <title>{{ $title ? $title.' · ' : '' }}ScrapsRoulette</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('app-icons/favicon-32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('app-icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ScrapsRoulette">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a href="#inhalt" class="sr-only z-50 bg-ink px-4 py-3 font-extrabold text-ground focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Zum Inhalt springen
    </a>

    <header class="border-b-2 border-ink print:hidden">
        <div class="mx-auto flex max-w-[1240px] items-stretch justify-between px-5 sm:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 py-4 text-ink no-underline hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent" aria-label="ScrapsRoulette – Startseite">
                <span class="grid h-9 w-9 place-items-center bg-accent text-lg font-extrabold text-ground" aria-hidden="true">S</span>
                <span class="font-display text-xl font-extrabold tracking-tight">Scraps<em class="italic text-orange-600">Roulette</em></span>
            </a>
            <p class="m-0 hidden items-center border-l-2 border-ink pl-6 text-sm text-ink/70 sm:flex">Restekoch mit Charakter · Beschwerden unerwünscht</p>
        </div>
    </header>

    <main id="inhalt" tabindex="-1" class="focus:outline-none">
        {{ $slot }}
    </main>

    <footer class="border-t-2 border-ink print:hidden">
        <div class="mx-auto flex max-w-[1240px] flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-6 text-sm text-ink/70 sm:px-8">
            <span>Scraps<em class="italic text-orange-600">Roulette</em> · Gebaut aus Resten</span>
            <span>Rezepte ohne Gewähr, Beleidigungen mit Absicht.</span>
            <nav aria-label="Rechtliches" class="flex gap-4">
                <a href="{{ route('impressum') }}" class="font-semibold underline underline-offset-4">Impressum</a>
                <a href="{{ route('datenschutz') }}" class="font-semibold underline underline-offset-4">Datenschutz</a>
            </nav>
        </div>
    </footer>
</body>
</html>
