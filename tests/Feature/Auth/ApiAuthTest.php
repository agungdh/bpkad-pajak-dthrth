<?php

use App\Models\User;

test('user can login via api', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->postJson('/api/auth/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['uuid', 'name', 'username', 'email'],
            'token',
        ]);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->postJson('/api/auth/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
});

test('user cannot login with missing credentials', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username', 'password']);
});

test('user can logout via api', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    $response
        ->assertStatus(200)
        ->assertJson(['message' => 'Berhasil logout.']);

    // Verify token is revoked
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

test('user can get own info', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me');

    $response
        ->assertStatus(200)
        ->assertJson([
            'uuid' => $user->uuid,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ]);
});

test('unauthenticated user cannot access protected routes', function () {
    $response = $this->getJson('/api/skpd');

    $response->assertStatus(401);
});

test('authenticated user can access protected routes', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/skpd');

    $response->assertStatus(200);
});
