<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('displays the fit tracker index page', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get('/fit-tracker');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('fit-tracker/Index')
        ->has('checkIns')
    );
});

it('displays the check-in create page', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get('/fit-tracker/check-in');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('fit-tracker/CheckIn')
    );
});

it('requires authentication for fit tracker index', function () {
    $response = get('/fit-tracker');

    $response->assertRedirect('/login');
});

it('requires authentication for check-in create', function () {
    $response = get('/fit-tracker/check-in');

    $response->assertRedirect('/login');
});

