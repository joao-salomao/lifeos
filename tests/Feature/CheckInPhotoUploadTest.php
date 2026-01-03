<?php

use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
    $this->user = User::factory()->create();
});

test('user can create check-in with photos', function () {
    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
    ];

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => $photos,
    ]);

    $response->assertRedirect('/fit-tracker');

    $checkIn = CheckIn::first();
    expect($checkIn)->not->toBeNull();
    expect($checkIn->getMedia('photos'))->toHaveCount(3);
});

test('user can create check-in with maximum 5 photos', function () {
    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
        UploadedFile::fake()->image('photo4.jpg'),
        UploadedFile::fake()->image('photo5.jpg'),
    ];

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => $photos,
    ]);

    $response->assertRedirect('/fit-tracker');

    $checkIn = CheckIn::first();
    expect($checkIn->getMedia('photos'))->toHaveCount(5);
});

test('user cannot upload more than 5 photos', function () {
    $photos = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
        UploadedFile::fake()->image('photo3.jpg'),
        UploadedFile::fake()->image('photo4.jpg'),
        UploadedFile::fake()->image('photo5.jpg'),
        UploadedFile::fake()->image('photo6.jpg'),
    ];

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => $photos,
    ]);

    $response->assertSessionHasErrors('photos');
});

test('photo must be an image file', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => [$file],
    ]);

    $response->assertSessionHasErrors('photos.0');
});

test('photo must be within allowed mime types', function () {
    $file = UploadedFile::fake()->image('photo.gif');

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => [$file],
    ]);

    $response->assertSessionHasErrors('photos.0');
});

test('photo must not exceed max file size', function () {
    $file = UploadedFile::fake()->image('large.jpg')->size(11000); // 11MB

    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
        'photos' => [$file],
    ]);

    $response->assertSessionHasErrors('photos.0');
});

test('user can create check-in without photos', function () {
    $response = $this->actingAs($this->user)->post('/fit-tracker/check-ins', [
        'title' => 'Morning Workout',
        'description' => 'Great session',
        'checked_in_at' => now()->toISOString(),
    ]);

    $response->assertRedirect('/fit-tracker');

    $checkIn = CheckIn::first();
    expect($checkIn)->not->toBeNull();
    expect($checkIn->getMedia('photos'))->toHaveCount(0);
});
