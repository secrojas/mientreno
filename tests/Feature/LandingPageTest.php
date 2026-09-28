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

    public function test_v3_url_redirects_permanently_to_home(): void
    {
        $this->get('/v3')->assertStatus(301)->assertRedirect('/');
    }

    public function test_previous_landing_versions_are_still_available(): void
    {
        $this->get(route('welcome.v1'))->assertOk()->assertViewIs('welcomev1');
        $this->get(route('welcome.v2'))->assertOk()->assertViewIs('welcomev2');
    }
}
