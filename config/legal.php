<?php

/*
|--------------------------------------------------------------------------
| Rechtliche Angaben (Impressum & Datenschutzerklärung)
|--------------------------------------------------------------------------
|
| Die Angaben werden ausschließlich über die .env-Datei gepflegt, damit
| keine persönlichen Daten im Repository landen.
|
*/

return [
    'name' => env('LEGAL_NAME', '[Vor- und Nachname]'),
    'street' => env('LEGAL_STREET', '[Straße und Hausnummer]'),
    'city' => env('LEGAL_CITY', '[PLZ Ort]'),
    'country' => env('LEGAL_COUNTRY', 'Österreich'),
    'email' => env('LEGAL_EMAIL', '[E-Mail-Adresse]'),
    'updated_at' => env('LEGAL_UPDATED_AT', 'Oktober 2026'),
];
