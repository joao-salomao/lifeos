<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckInRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckInController extends Controller
{
    public function index(): Response
    {
        $checkIns = auth()->user()
            ->checkIns()
            ->with([
                'activities',
                'media' => fn($q) => $q->where('collection_name', 'photos'),
            ])
            ->orderBy('checked_in_at', 'desc')
            ->get();

        return Inertia::render('fit-tracker/index', [
            'checkIns' => $checkIns,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('fit-tracker/check-in');
    }

    public function store(StoreCheckInRequest $request): RedirectResponse
    {
        $checkIn = $request->user()->checkIns()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'checked_in_at' => $request->input('checked_in_at'),
        ]);

        if ($request->has('activities')) {
            foreach ($request->input('activities') as $activityData) {
                $checkIn->activities()->create([
                    'type' => $activityData['type'],
                    'started_at' => $activityData['started_at'],
                    'ended_at' => $activityData['ended_at'],
                    'distance' => $activityData['distance'] ?? null,
                    'calories_burned' => $activityData['calories_burned'] ?? null,
                    'steps' => $activityData['steps'] ?? null,
                ]);
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $checkIn->addMedia($photo)->toMediaCollection('photos');
            }
        }

        return redirect()->route('fit-tracker.index')->with('success', 'Check-in created successfully.');
    }

    public function showPhoto(Request $request, int $checkInId, int $mediaId)
    {
        $checkIn = auth()->user()->checkIns()->findOrFail($checkInId);
        $mediaItem = $checkIn->getMedia('photos')->where('id', $mediaId)->firstOrFail();

        return $mediaItem->toInlineResponse($request);
    }
}
