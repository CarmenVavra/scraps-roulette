<?php

namespace App\Http\Controllers;

use App\Exceptions\ChefRageException;
use App\Http\Requests\GenerateRecipeRequest;
use App\Services\RecipeGeneratorService;
use Illuminate\Http\JsonResponse;

class RecipeController extends Controller
{
    /**
     * Nimmt die Zutaten entgegen und liefert Rant + Rezept als JSON.
     *
     * Erfolg: 200 { "recipe": { chef_rant, recipe_title, used_ingredients, prep_time, steps, ... } }
     * Fehler: 503 { "message": "<Chefkoch-Fehlermeldung>" }
     */
    public function generate(GenerateRecipeRequest $request, RecipeGeneratorService $generator): JsonResponse
    {
        try {
            $recipe = $generator->generate($request->ingredients());
        } catch (ChefRageException $e) {
            $message = $e->getMessage();

            return response()->json(compact('message'), 503);
        }

        return response()->json(compact('recipe'));
    }
}
