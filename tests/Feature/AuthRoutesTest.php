<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class AuthRoutesTest extends TestCase
{
    public function test_auth_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('register'));
        $this->assertTrue(Route::has('logout'));
        $this->assertTrue(Route::has('password.request'));
        $this->assertTrue(Route::has('password.email'));
        $this->assertTrue(Route::has('password.update'));
        $this->assertTrue(Route::has('verification.notice'));
    }

    public function test_guest_routes_are_accessible(): void
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);
        $this->get(route('password.request'))->assertStatus(200);
    }

    public function test_auth_routes_redirect_guests(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
        $this->get(route('password.confirm'))->assertRedirect(route('login'));
    }
}
