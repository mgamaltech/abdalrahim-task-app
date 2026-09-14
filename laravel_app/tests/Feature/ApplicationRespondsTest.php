<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationRespondsTest extends TestCase
{
    /**
     * The landing page boots and responds successfully.
     */
    public function test_the_home_page_responds_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    /**
     * The framework health endpoint confirms the app is up.
     */
    public function test_the_health_endpoint_responds_successfully(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
    }
}
