<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testProfilePageIsDisplayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function testProfilePageRequiresAuthentication(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function testProfileInformationCanBeUpdated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function testEmailVerificationStatusIsUnchangedWhenTheEmailAddressIsUnchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function testEmailVerificationStatusIsResetWhenEmailIsChanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'newemail@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('newemail@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function testProfileUpdateValidatesNameIsRequired(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => '',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasErrors('name')
            ->assertRedirect('/profile');

        $this->assertNotSame('test@example.com', $user->fresh()->email);
    }

    public function testProfileUpdateValidatesEmailIsRequired(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => '',
            ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect('/profile');

        $this->assertNotSame('Test User', $user->fresh()->name);
    }

    public function testProfileUpdateValidatesEmailIsValid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'invalid-email',
            ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect('/profile');
    }

    public function testProfileUpdateValidatesEmailIsUnique(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'existing@example.com',
            ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect('/profile');
    }

    public function testProfileUpdateAllowsSameEmailForSameUser(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Updated Name',
                'email' => 'user@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('user@example.com', $user->email);
    }

    public function testUserCanDeleteTheirAccount(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function testCorrectPasswordMustBeProvidedToDeleteAccount(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function testPasswordIsRequiredToDeleteAccount(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => '',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function testProfileUpdateDoesNotAffectPassword(): void
    {
        $originalPassword = 'original-password';
        $user = User::factory()->create([
            'password' => Hash::make($originalPassword)
        ]);

        $hashedPasswordBefore = $user->password;

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame($hashedPasswordBefore, $user->password);
        $this->assertTrue(Hash::check($originalPassword, $user->password));
    }

    public function testGuestCannotAccessProfilePage(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function testGuestCannotUpdateProfile(): void
    {
        $response = $this->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    public function testGuestCannotDeleteAccount(): void
    {
        $response = $this->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
    }

    public function testUserCanUpdateProfileWithMaximumNameLength(): void
    {
        $user = User::factory()->create();
        $longName = str_repeat('a', 255); // Максимальная длина для строки

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $longName,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame($longName, $user->fresh()->name);
    }

    public function testUserCannotUpdateProfileWithExceedingNameLength(): void
    {
        $user = User::factory()->create();
        $tooLongName = str_repeat('a', 256); // Превышает максимальную длину

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => $tooLongName,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasErrors('name')
            ->assertRedirect('/profile');
    }

    public function testProfileUpdateWithTrimming(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => '  Test User  ', // С пробелами
                'email' => '  test@example.com  ',
            ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Test User', $user->name); // Без пробелов
        $this->assertSame('test@example.com', $user->email); // Без пробелов
    }

    public function testAccountDeletionRedirectsToHomePage(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function testAccountDeletionInvalidatesSession(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
