<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validiert die Zutatenliste für das Rezept-Roulette.
 */
class GenerateRecipeRequest extends FormRequest
{
    /**
     * Das Roulette ist öffentlich, Missbrauch wird über das Rate-Limit gebremst.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ingredients' => ['required', 'array', 'min:1', 'max:50'],
            'ingredients.*' => ['required', 'string', 'min:1', 'max:80'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ingredients.required' => 'Ohne Zutaten kocht nicht mal der Chef.',
            'ingredients.array' => 'Die Zutaten müssen als Liste kommen.',
            'ingredients.min' => 'Ohne Zutaten kocht nicht mal der Chef.',
            'ingredients.max' => 'Mehr als :max Zutaten? Das ist kein Kühlschrank, das ist ein Supermarkt.',
            'ingredients.*.required' => 'Leere Zutaten gibt es nicht. Auch nicht bei dir.',
            'ingredients.*.string' => 'Jede Zutat muss ein Text sein.',
            'ingredients.*.max' => 'Eine Zutat darf höchstens :max Zeichen lang sein.',
        ];
    }

    /**
     * Bereinigte Zutatenliste ohne Duplikate (Groß-/Kleinschreibung egal).
     *
     * @return list<string>
     */
    public function ingredients(): array
    {
        /** @var array<int, string> $ingredients */
        $ingredients = $this->validated('ingredients');

        return collect($ingredients)
            ->map(fn (string $item): string => trim($item))
            ->unique(fn (string $item): string => mb_strtolower($item))
            ->values()
            ->all();
    }
}
