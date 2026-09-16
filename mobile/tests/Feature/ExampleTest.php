<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_starts_at_the_login_screen_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
