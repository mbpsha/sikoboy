<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Use a JSON route to avoid requiring a built Vite manifest during tests.
        $response = $this->get('/template-dokumen');

        $response->assertStatus(200);
    }

    public function test_dokumen_page_shows_empty_state_when_no_templates_exist(): void
    {
        $response = $this->get('/template-dokumen');

        $response->assertStatus(200);
        $response->assertSee('&quot;dokumenGroups&quot;:[]', false);
    }
}
