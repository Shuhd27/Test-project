<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('email verification status is unchanged when the email address is unchanged', function () {
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
});

test('user can delete their account', function () {
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
});

test('correct password must be provided to delete account', function () {
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
});
test('profile information can be updated', function () {
    $user = User::factory()->create();

    // Use a unique email to avoid conflicts
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'unique_'.time().'@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertMatchesRegularExpression('/unique_\d+@example\.com/', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('cannot update profile with email that already exists', function () {
    // Create a user with a specific email
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    
    // Create another user who will try to update their email
    $user = User::factory()->create();
    
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
        ]);
    
    $response
        ->assertSessionHasErrors('email')
        ->assertRedirect();
    
    $user->refresh();
    $this->assertNotSame('existing@example.com', $user->email);
});

test('profile name can be updated while keeping same email', function () {
    $user = User::factory()->create();
    $originalEmail = $user->email;
    
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'New Name',
            'email' => $originalEmail,
        ]);
    
    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');
    
    $user->refresh();
    $this->assertSame('New Name', $user->name);
    $this->assertSame($originalEmail, $user->email);
});

test('profile update with invalid email format fails', function () {
    $user = User::factory()->create();
    
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'not-a-valid-email',
        ]);
    
    $response
        ->assertSessionHasErrors('email')
        ->assertRedirect();
    
    $user->refresh();
    $this->assertNotSame('not-a-valid-email', $user->email);
});
