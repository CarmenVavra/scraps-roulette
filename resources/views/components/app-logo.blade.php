{{-- Bildmarke = App-Icon (Quelle: resources/app-icon.svg). Größe über class steuern. --}}
<svg {{ $attributes->merge(['class' => 'h-9 w-9 shrink-0']) }} viewBox="0 0 512 512" aria-hidden="true" focusable="false">
    <rect width="512" height="512" fill="#201e1d"/>
    <circle cx="256" cy="256" r="150" fill="#ec3013" stroke="#f3f2f2" stroke-width="18"/>
    <path fill="#f3f2f2" d="M256 256L256 106A150 150 0 0 1 362.07 149.93ZM256 256L406 256A150 150 0 0 1 362.07 362.07ZM256 256L256 406A150 150 0 0 1 149.93 362.07ZM256 256L106 256A150 150 0 0 1 149.93 149.93Z"/>
    <circle cx="256" cy="256" r="31" fill="#ec3013" stroke="#f3f2f2" stroke-width="18"/>
</svg>
