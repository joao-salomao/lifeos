<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckInRequest;
use App\Models\CheckIn;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CheckInController extends Controller
{
    public function index(): Response
    {
        $checkIns = auth()->user()
            ->checkIns()
            ->with('activities')
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

        return redirect()->route('fit-tracker.index')->with('success', 'Check-in created successfully.');
    }
}
