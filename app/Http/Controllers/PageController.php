<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Die Roulette-Seite (Single Page).
     */
    public function home(): View
    {
        return view('scraps');
    }

    public function impressum(): View
    {
        $legal = config('legal');

        return view('legal.impressum', compact('legal'));
    }

    public function datenschutz(): View
    {
        $legal = config('legal');
        $model = config('services.anthropic.model');

        return view('legal.datenschutz', compact('legal', 'model'));
    }
}
