<?php

namespace Tests\Feature;

use Anthropic\Client;
use Anthropic\RequestOptions;
use App\Exceptions\ChefRageException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RecipeGenerationTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @var array<int, array<string, mixed>> */
    private array $sentRequests = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.anthropic.api_key' => 'test-key']);
        RateLimiter::clear('min:127.0.0.1');
        RateLimiter::clear('day:127.0.0.1');
    }

    public function test_it_returns_the_recipe_as_structured_json(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody($this->validRecipe()))]);

        $response = $this->postJson(route('recipes.generate'), [
            'ingredients' => ['Blumenkohl', 'Eier', 'Gummibärchen'],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('recipe.recipe_title', 'Blumenkohl im Senf-Brösel-Mantel')
            ->assertJsonPath('recipe.used_ingredients', ['Blumenkohl', 'Eier'])
            ->assertJsonPath('recipe.prep_time', '35 Min')
            ->assertJsonPath('recipe.rejected_ingredients.0.name', 'Gummibärchen')
            ->assertJsonStructure(['recipe' => [
                'chef_rant', 'recipe_title', 'used_ingredients', 'prep_time', 'steps',
                'recipe_subtitle', 'servings', 'difficulty', 'ingredient_amounts', 'rejected_ingredients', 'chef_tip',
            ]]);
    }

    public function test_it_sends_ingredients_with_json_schema_to_claude(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody($this->validRecipe()))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Zwiebel <script>']])->assertOk();

        $payload = $this->sentRequests[0];
        $this->assertSame('claude-haiku-5-5', $payload['model']);
        $this->assertSame('json_schema', $payload['output_config']['format']['type']);
        $this->assertSame(
            ['chef_rant', 'recipe_title', 'used_ingredients', 'prep_time', 'steps'],
            array_slice($payload['output_config']['format']['schema']['required'], 0, 5),
        );
        $this->assertStringContainsString('- Senf', $payload['messages'][0]['content']);
        // Spitze Klammern werden entfernt, damit niemand aus dem <zutaten>-Block ausbrechen kann.
        $this->assertStringContainsString('- Zwiebel script', $payload['messages'][0]['content']);
        $this->assertStringContainsString('Chef Étienne Grandmal', $payload['system'][0]['text']);
        $this->assertStringContainsString('Du duzt die Person immer', $payload['system'][0]['text']);
    }

    public function test_ingredients_are_required(): void
    {
        $this->postJson(route('recipes.generate'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ingredients');
    }

    public function test_ingredients_must_not_be_empty(): void
    {
        $this->postJson(route('recipes.generate'), ['ingredients' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ingredients');
    }

    public function test_ingredients_must_be_an_array(): void
    {
        $this->postJson(route('recipes.generate'), ['ingredients' => 'Blumenkohl, Eier'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ingredients');
    }

    public function test_every_ingredient_must_be_a_string(): void
    {
        $this->postJson(route('recipes.generate'), ['ingredients' => ['Blumenkohl', ['nested'], 42]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ingredients.1', 'ingredients.2']);
    }

    public function test_ingredients_must_not_be_too_long(): void
    {
        $this->postJson(route('recipes.generate'), ['ingredients' => [str_repeat('a', 81)]])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ingredients.0');
    }

    public function test_a_timeout_returns_a_charming_chef_message(): void
    {
        $this->fakeClaude([
            new ConnectException('cURL error 28: Operation timed out', new Request('POST', 'https://api.anthropic.com/v1/messages')),
        ]);

        $response = $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']]);

        $response->assertStatus(503);
        $this->assertContains($response->json('message'), [ChefRageException::TOO_SLOW, ChefRageException::LEFT_KITCHEN]);
    }

    public function test_invalid_json_from_the_model_is_handled(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody('Das ist kein JSON, Monsieur.'))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::GIBBERISH);
    }

    public function test_json_with_missing_fields_is_handled(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody(['chef_rant' => 'Nein.']))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::GIBBERISH);
    }

    public function test_a_refusal_is_handled(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody('', 'refusal'))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::REFUSED);
    }

    public function test_api_server_errors_are_handled(): void
    {
        $this->fakeClaude([new Response(529, ['Content-Type' => 'application/json'], json_encode([
            'type' => 'error',
            'error' => ['type' => 'overloaded_error', 'message' => 'Overloaded'],
        ], JSON_THROW_ON_ERROR))]);

        $response = $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']]);

        $response->assertStatus(503);
        $this->assertNotEmpty($response->json('message'));
    }

    public function test_an_invalid_api_key_is_handled(): void
    {
        $this->fakeClaude([new Response(401, ['Content-Type' => 'application/json'], json_encode([
            'type' => 'error',
            'error' => ['type' => 'authentication_error', 'message' => 'invalid x-api-key'],
        ], JSON_THROW_ON_ERROR))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::LEFT_KITCHEN);
    }

    public function test_an_unreadable_api_response_does_not_crash_the_app(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], '{kaputt')]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::LEFT_KITCHEN);
    }

    public function test_duplicate_ingredients_are_sent_only_once(): void
    {
        $this->fakeClaude([new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody($this->validRecipe()))]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', ' senf ', 'Eier']])->assertOk();

        $this->assertSame(1, substr_count($this->sentRequests[0]['messages'][0]['content'], '- Senf'));
        $this->assertStringNotContainsString('- senf', $this->sentRequests[0]['messages'][0]['content']);
    }

    public function test_a_missing_api_key_closes_the_kitchen(): void
    {
        config(['services.anthropic.api_key' => null]);

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(503)
            ->assertJsonPath('message', ChefRageException::CLOSED);
    }

    public function test_requests_are_rate_limited(): void
    {
        $this->fakeClaude(array_map(
            fn (): Response => new Response(200, ['Content-Type' => 'application/json'], $this->claudeBody($this->validRecipe())),
            range(1, 5),
        ));

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])->assertOk();
        }

        $this->postJson(route('recipes.generate'), ['ingredients' => ['Senf', 'Eier']])
            ->assertStatus(429)
            ->assertJsonStructure(['message']);
    }

    /**
     * Ersetzt den Anthropic-Client durch einen Client mit gefälschtem HTTP-Transport.
     *
     * @param  array<int, Response|\Throwable>  $queue
     */
    private function fakeClaude(array $queue): void
    {
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::mapRequest(function ($request) {
            $this->sentRequests[] = json_decode((string) $request->getBody(), true);

            return $request;
        }));

        $this->app->instance(Client::class, new Client(
            apiKey: 'test-key',
            requestOptions: RequestOptions::with(maxRetries: 0, transporter: new GuzzleClient(['handler' => $stack])),
        ));
    }

    /**
     * Baut eine Messages-API-Antwort mit dem übergebenen Text als einzigem Textblock.
     *
     * @param  array<string, mixed>|string  $text
     */
    private function claudeBody(array|string $text, string $stopReason = 'end_turn'): string
    {
        return json_encode([
            'id' => 'msg_test',
            'type' => 'message',
            'role' => 'assistant',
            'model' => 'claude-haiku-5-5',
            'content' => $stopReason === 'refusal' ? [] : [
                ['type' => 'text', 'text' => is_array($text) ? json_encode($text, JSON_THROW_ON_ERROR) : $text],
            ],
            'stop_reason' => $stopReason,
            'stop_sequence' => null,
            'stop_details' => $stopReason === 'refusal'
                ? ['type' => 'refusal', 'category' => null, 'explanation' => null]
                : null,
            'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    private function validRecipe(): array
    {
        return [
            'chef_rant' => "Ach du ==meine Güte.==\n\n**Blumenkohl** und ~~Gummibärchen~~? Das ist ein !!Tatort.!!\n\n__Schreib mit, du Genie.__",
            'recipe_title' => 'Blumenkohl im Senf-Brösel-Mantel',
            'used_ingredients' => ['Blumenkohl', 'Eier'],
            'prep_time' => '35 Min',
            'steps' => ['Ofen vorheizen.', 'Blumenkohl blanchieren.', 'Backen.'],
            'recipe_subtitle' => 'Ein Teller, den du nicht verdient hast.',
            'servings' => '2',
            'difficulty' => 'Leicht',
            'ingredient_amounts' => [['name' => 'Blumenkohl', 'amount' => '1/2 Kopf'], ['name' => 'Eier', 'amount' => '3 Stk.']],
            'rejected_ingredients' => [['name' => 'Gummibärchen', 'reason' => 'Das ist Dekoration für Kinder.']],
            'chef_tip' => 'Salz. Mehr Salz.',
        ];
    }
}
