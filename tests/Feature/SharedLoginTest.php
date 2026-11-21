<?php

namespace Tests\Feature;

use App\Http\Responses\RoleLoginResponse;
use App\Services\LoginAccounts;
use Illuminate\Http\Request;
use Tests\TestCase;

class SharedLoginTest extends TestCase
{
    private function accounts(bool $ready): void
    {
        $this->app->instance(LoginAccounts::class, new class($ready) extends LoginAccounts
        {
            public function __construct(private bool $available) {}
            public function ready(): bool { return $this->available; }
        });
    }

    public function test_shared_login_renders_without_an_accounts_table(): void
    {
        $this->accounts(false);
        $this->get('/login')->assertOk()->assertSee('Welcome back')->assertSee('Volunteer')->assertSee('Director')
            ->assertSee('Account sign-in is not available yet.')->assertSee('disabled', false);
    }

    public function test_login_is_enabled_when_account_storage_is_ready(): void
    {
        $this->accounts(true);
        $this->get('/login')->assertOk()->assertDontSee('Account sign-in is not available yet.')
            ->assertSee('autocomplete="current-password"', false)->assertSee('name="_token"', false);
    }

    public function test_missing_account_storage_returns_a_validation_error_instead_of_a_database_error(): void
    {
        $this->accounts(false);
        $this->from('/login')->post('/login', ['email' => 'person@example.test', 'password' => 'test-only-password'])
            ->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_role_destinations_come_from_the_account_and_ignore_requested_role_and_intended_url(): void
    {
        $accounts = new LoginAccounts;
        foreach (['admin' => '/dashboard', 'director' => '/director', 'staff' => '/dashboard/staff', 'volunteer' => '/#events'] as $role => $destination) {
            $this->assertSame($destination, $accounts->destination($role));
            $request = Request::create('/login?role=admin');
            $request->setUserResolver(fn () => (object) ['role' => $role]);
            $request->setLaravelSession($this->app['session.store']);
            $request->session()->put('url.intended', '/dashboard');
            $response = (new RoleLoginResponse)->toResponse($request);
            $this->assertStringEndsWith($destination, $response->getTargetUrl());
            $this->assertFalse($request->session()->has('url.intended'));
        }
        $this->assertNull($accounts->destination('unknown'));
        $this->assertNull($accounts->destination(null));
    }
}
