<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_home_shows_the_v3_landing_highlighting_health_and_shoes(): void
    {
        $response = $this->get(route('welcome'));

        $response->assertOk();
        $response->assertViewIs('welcome');
        $response->assertSee('id="salud"', false);
        $response->assertSee('id="zapatillas"', false);
        $response->assertSee(route('register'));
        $response->assertSee(route('login'));
    }

    public function test_previous_landing_urls_redirect_permanently_to_home(): void
    {
        foreach (['/v1', '/v2', '/v3'] as $previousLandingUrl) {
            $this->get($previousLandingUrl)->assertStatus(301)->assertRedirect('/');
        }
    }
}
