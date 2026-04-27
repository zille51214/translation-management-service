<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_translation()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/translations', [
            'key' => 'welcome222',
            'locale' => 'en',
            'value' => 'Welcome222'
        ]);

        $response->assertStatus(200)
        ->assertJsonFragment([
            'key' => 'welcome222',
            'locale' => 'en'
        ]);

            
    }

    public function test_can_fetch_translations()
    {
        Sanctum::actingAs(User::factory()->create());

        Translation::factory()->create([
            'key' => 'hello',
            'locale' => 'en',
            'value' => 'Hello'
        ]);

        $response = $this->getJson('/api/translations');

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'key' => 'hello'
                ]);
    }

    public function test_export_returns_correct_json()
    {
        Sanctum::actingAs(User::factory()->create());

        Translation::factory()->create([
            'key' => 'welcome',
            'locale' => 'en',
            'value' => 'Welcome'
        ]);

        $response = $this->getJson('/api/export?locale=en');

        $response->assertStatus(200)
                ->assertJson([
                    'welcome' => 'Welcome'
                ]);
    }

    public function test_export_performance_is_fast()
    {
        Sanctum::actingAs(User::factory()->create());

        Translation::factory()->count(1000)->create([
            'locale' => 'en'
        ]);

        $start = microtime(true);

        $this->getJson('/api/export?locale=en');

        $time = microtime(true) - $start;

        $this->assertTrue($time < 0.5, "Export too slow: {$time}s");
    }

}