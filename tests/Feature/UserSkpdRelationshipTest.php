<?php

use App\Models\Skpd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can belong to skpd', function () {
    $skpd = Skpd::create(['nama' => 'Test SKPD']);
    $user = User::factory()->create(['skpd_id' => $skpd->id]);

    expect($user->skpd)->not->toBeNull()
        ->and($user->skpd->id)->toBe($skpd->id)
        ->and($user->skpd->nama)->toBe('Test SKPD');
});

test('skpd has many users', function () {
    $skpd = Skpd::create(['nama' => 'Test SKPD 2']);
    $user1 = User::factory()->create(['skpd_id' => $skpd->id]);
    $user2 = User::factory()->create(['skpd_id' => $skpd->id]);

    expect($skpd->users)->toHaveCount(2)
        ->and($skpd->users->first()->id)->toBe($user1->id);
});
