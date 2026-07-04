<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * `/` ist auth-gated (home -> EmployeeDashboardController). Unauthentifiziert
     * leitet die App zur Anmeldung um (302) — kein 200. Test an realen Stand angepasst.
     */
    public function test_root_redirects_when_unauthenticated(): void
    {
        $response = $this->get('/');

        $response->assertRedirect();
    }
}
