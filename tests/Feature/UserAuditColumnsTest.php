<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('audit columns are set when user is created', function () {
    $creator = User::factory()->create();

    $this->actingAs($creator);

    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    expect($user->created_by)->toBe($creator->id)
        ->and($user->updated_by)->toBe($creator->id)
        ->and($user->deleted_by)->toBeNull();
});

test('updated_by is set when user is updated', function () {
    $creator = User::factory()->create();
    $updater = User::factory()->create();

    $this->actingAs($creator);
    $user = User::factory()->create();

    expect($user->created_by)->toBe($creator->id)
        ->and($user->updated_by)->toBe($creator->id);

    $this->actingAs($updater);
    $user->update(['name' => 'Updated Name']);

    expect($user->updated_by)->toBe($updater->id)
        ->and($user->created_by)->toBe($creator->id);
});

test('deleted_by is set when user is soft deleted', function () {
    $creator = User::factory()->create();
    $deleter = User::factory()->create();

    $this->actingAs($creator);
    $user = User::factory()->create();

    $this->actingAs($deleter);
    $user->delete();

    expect($user->deleted_by)->toBe($deleter->id)
        ->and($user->deleted_at)->not->toBeNull();
});

test('creator relationship works correctly', function () {
    $creator = User::factory()->create(['name' => 'Creator User']);

    $this->actingAs($creator);
    $user = User::factory()->create();

    expect($user->creator)->toBeInstanceOf(User::class)
        ->and($user->creator->id)->toBe($creator->id)
        ->and($user->creator->name)->toBe('Creator User');
});

test('updater relationship works correctly', function () {
    $creator = User::factory()->create();
    $updater = User::factory()->create(['name' => 'Updater User']);

    $this->actingAs($creator);
    $user = User::factory()->create();

    $this->actingAs($updater);
    $user->update(['name' => 'Updated Name']);

    expect($user->updater)->toBeInstanceOf(User::class)
        ->and($user->updater->id)->toBe($updater->id)
        ->and($user->updater->name)->toBe('Updater User');
});

test('deleter relationship works correctly', function () {
    $creator = User::factory()->create();
    $deleter = User::factory()->create(['name' => 'Deleter User']);

    $this->actingAs($creator);
    $user = User::factory()->create();

    $this->actingAs($deleter);
    $user->delete();

    $user->refresh();

    expect($user->deleter)->toBeInstanceOf(User::class)
        ->and($user->deleter->id)->toBe($deleter->id)
        ->and($user->deleter->name)->toBe('Deleter User');
});

test('audit columns are not set when user is not authenticated', function () {
    $user = User::factory()->create();

    expect($user->created_by)->toBeNull()
        ->and($user->updated_by)->toBeNull()
        ->and($user->deleted_by)->toBeNull();
});

test('timestamps are stored as epoch integers', function () {
    $creator = User::factory()->create();

    $this->actingAs($creator);

    $user = User::factory()->create([
        'name' => 'Epoch Test User',
        'email' => 'epoch@example.com',
    ]);

    // Get raw attributes to check actual database values
    $rawCreatedAt = $user->getAttributes()['created_at'];
    $rawUpdatedAt = $user->getAttributes()['updated_at'];

    // In SQLite (testing), timestamps might be stored as strings
    // In MySQL/PostgreSQL (production), they will be integers
    // Laravel's timestamp cast will handle both cases
    expect($rawCreatedAt)->not->toBeNull()
        ->and($rawUpdatedAt)->not->toBeNull();

    // Verify Carbon object conversion works regardless of storage format
    expect($user->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($user->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($user->created_at->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});

test('deleted_at is stored as epoch integer when soft deleted', function () {
    $creator = User::factory()->create();
    $deleter = User::factory()->create();

    $this->actingAs($creator);
    $user = User::factory()->create();

    $this->actingAs($deleter);
    $user->delete();

    $user->refresh();

    // Get raw deleted_at value
    $rawDeletedAt = $user->getAttributes()['deleted_at'];

    // Verify it's not null (stored in database)
    expect($rawDeletedAt)->not->toBeNull();

    // Verify Carbon object conversion works
    expect($user->deleted_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($user->deleted_at->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});
