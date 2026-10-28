<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_the_roulette_page_renders_all_sections(): void
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Kühlschrank&#8209;Friedhof.', false)
            ->assertSee('Was liegt noch rum?')
            ->assertSee('Chefkoch herausfordern')
            ->assertSee('Das Urteil des Chefkochs')
            ->assertSee('Die kulinarische Rettung')
            ->assertSee('name="csrf-token"', false)
            ->assertSee('recipes\/generate', false)
            ->assertSee(route('impressum'), false)
            ->assertSee(route('datenschutz'), false);
    }

    public function test_the_impressum_shows_the_configured_legal_data(): void
    {
        config(['legal.name' => 'Erika Musterfrau', 'legal.email' => 'kontakt@example.at']);

        $this->get(route('impressum'))
            ->assertStatus(200)
            ->assertSee('Impressum')
            ->assertSee('Erika Musterfrau')
            ->assertSee('kontakt@example.at')
            ->assertSee('§ 25 Mediengesetz');
    }

    public function test_the_privacy_policy_names_hosting_and_ai_provider(): void
    {
        $this->get(route('datenschutz'))
            ->assertStatus(200)
            ->assertSee('Datenschutz')
            ->assertSee('netcup GmbH')
            ->assertSee('Anthropic, PBC')
            ->assertSee('Datenschutzbehörde');
    }

    public function test_legal_data_is_escaped(): void
    {
        config(['legal.name' => '<script>alert(1)</script>']);

        $this->get(route('impressum'))
            ->assertStatus(200)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_the_generate_endpoint_only_accepts_post(): void
    {
        $this->getJson(route('recipes.generate'))->assertStatus(405);
    }
}
