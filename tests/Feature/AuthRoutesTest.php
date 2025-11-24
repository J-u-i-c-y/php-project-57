<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    public function testAuthRoutesAreRegistered(): void
    {
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('register'));
        $this->assertTrue(Route::has('logout'));
        $this->assertTrue(Route::has('password.request'));
        $this->assertTrue(Route::has('password.email'));
        $this->assertTrue(Route::has('password.update'));
        $this->assertTrue(Route::has('verification.notice'));
    }

    public function testGuestRoutesAreAccessible(): void
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);
        $this->get(route('password.request'))->assertStatus(200);
    }

    public function testAuthRoutesRedirectGuests(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
        $this->get(route('password.confirm'))->assertRedirect(route('login'));
    }
}
