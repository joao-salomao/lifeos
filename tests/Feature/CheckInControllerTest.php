<?php

use App\Enums\ActivityType;
use App\Models\CheckIn;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

it('creates a check-in with title and description', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Started the day with a great workout session',
        'checked_in_at' => '2026-01-01 08:00:00',
    ]);

    $response->assertRedirect();

    $checkIn = CheckIn::where('user_id', $user->id)->first();

    expect($checkIn)->not->toBeNull();
    expect($checkIn->title)->toBe('Morning Workout');
    expect($checkIn->description)->toBe('Started the day with a great workout session');
    expect($checkIn->checked_in_at->format('Y-m-d H:i:s'))->toBe('2026-01-01 08:00:00');
});

it('creates a check-in without description', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Quick Run',
        'checked_in_at' => '2026-01-01 10:30:00',
    ]);

    $response->assertRedirect();

    $checkIn = CheckIn::where('user_id', $user->id)->first();

    expect($checkIn)->not->toBeNull();
    expect($checkIn->title)->toBe('Quick Run');
    expect($checkIn->description)->toBeNull();
    expect($checkIn->checked_in_at->format('Y-m-d H:i:s'))->toBe('2026-01-01 10:30:00');
});

it('creates a check-in with activities', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Fitness Session',
        'description' => 'Mixed training session',
        'checked_in_at' => '2026-01-01 07:00:00',
        'activities' => [
            [
                'type' => ActivityType::RUNNING->value,
                'started_at' => '2026-01-01 08:00:00',
                'ended_at' => '2026-01-01 09:00:00',
                'distance' => 5.5,
                'calories_burned' => 350,
                'steps' => 7500,
            ],
            [
                'type' => ActivityType::WEIGHTLIFTING->value,
                'started_at' => '2026-01-01 09:30:00',
                'ended_at' => null,
                'distance' => null,
                'calories_burned' => 200,
                'steps' => null,
            ],
        ],
    ]);

    $response->assertRedirect();

    $checkIn = CheckIn::where('user_id', $user->id)->first();

    expect($checkIn)->not->toBeNull();
    expect($checkIn->activities)->toHaveCount(2);

    assertDatabaseHas('activities', [
        'check_in_id' => $checkIn->id,
        'type' => ActivityType::RUNNING->value,
        'distance' => 5.5,
        'calories_burned' => 350,
        'steps' => 7500,
    ]);

    assertDatabaseHas('activities', [
        'check_in_id' => $checkIn->id,
        'type' => ActivityType::WEIGHTLIFTING->value,
        'distance' => null,
        'calories_burned' => 200,
        'steps' => null,
    ]);
});

it('requires a title', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'checked_in_at' => '2026-01-01 08:00:00',
        'description' => 'No title provided',
    ]);

    $response->assertSessionHasErrors('title');
});

it('requires checked_in_at', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Missing Date',
        'description' => 'No date provided',
    ]);

    $response->assertSessionHasErrors('checked_in_at');
});

it('validates activity type', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Invalid Activity',
        'checked_in_at' => '2026-01-01 08:00:00',
        'activities' => [
            [
                'type' => 'invalid_type',
                'started_at' => '2026-01-01 08:00:00',
                'ended_at' => '2026-01-01 09:00:00',
            ],
        ],
    ]);

    $response->assertSessionHasErrors('activities.0.type');
});

it('validates ended_at is after started_at', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Invalid Time Range',
        'checked_in_at' => '2026-01-01 08:00:00',
        'activities' => [
            [
                'type' => ActivityType::RUNNING->value,
                'started_at' => '2026-01-01 09:00:00',
                'ended_at' => '2026-01-01 08:00:00',
            ],
        ],
    ]);

    $response->assertSessionHasErrors('activities.0.ended_at');
});

it('validates numeric fields are not negative', function ($field, $value) {
    $user = User::factory()->create();

    actingAs($user);

    $response = post('/fit-tracker/check-ins', [
        'title' => 'Negative Values',
        'checked_in_at' => '2026-01-01 08:00:00',
        'activities' => [
            array_merge([
                'type' => ActivityType::RUNNING->value,
                'started_at' => '2026-01-01 08:00:00',
                'ended_at' => '2026-01-01 09:00:00',
            ], [$field => $value]),
        ],
    ]);

    $response->assertSessionHasErrors("activities.0.{$field}");
})->with([
    ['distance', -5.5],
    ['calories_burned', -100],
    ['steps', -1000],
]);

it('requires authentication', function () {
    $response = post('/fit-tracker/check-ins', [
        'title' => 'Unauthorized',
    ]);

    $response->assertRedirect('/login');
});

