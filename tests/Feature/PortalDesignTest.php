<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortalDesignTest extends TestCase
{
    public function test_public_and_role_pages_render_with_compiled_tailwind_design(): void
    {
        foreach (['/', '/dashboard', '/director', '/dashboard/staff', '/staff/inventory', '/staff/donations', '/director/donations', '/staff-volunteers'] as $path) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee('css/design-utilities.css', false);
            $response->assertSee('</html>', false);
        }

        $utilities = file_get_contents(public_path('css/design-utilities.css'));
        $this->assertStringContainsString('--tw-font-weight-bold:', $utilities);
        $this->assertStringContainsString('tailwindcss', $utilities);
        $this->assertStringNotContainsString('@apply', file_get_contents(public_path('css/portal.css')));
    }
}
