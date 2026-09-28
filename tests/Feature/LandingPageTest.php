<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_v3_is_public_and_highlights_health_and_shoes(): void
    {
        $response = $this->get(route('welcome.v3'));

        $response->assertOk();
        $response->assertViewIs('welcomev3');
        $response->assertSee('id="salud"', false);
        $response->assertSee('id="zapatillas"', false);
        $response->assertSee(route('register'));
        $response->assertSee(route('login'));
    }

    public function test_landing_v2_is_still_available(): void
    {
        $this->get(route('welcome.v2'))->assertOk()->assertViewIs('welcomev2');
    }
}
