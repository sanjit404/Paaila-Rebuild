<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
   
    public function create()
    {
        // Already set up — go home
        if (UserPreference::hasPreferences(auth()->id())) {
            return redirect()->route('home');
        }

        $trekTypes   = UserPreference::trekTypeOptions();
        $difficulties = UserPreference::difficultyOptions();
        $durations   = UserPreference::durationOptions();
        $budgets     = UserPreference::budgetOptions();

        return view('auth.preferences', compact(
            'trekTypes', 'difficulties', 'durations', 'budgets'
        ));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trek_types'   => 'nullable|array|min:1',
            'trek_types.*' => 'string|in:nature,historical,cultural,adventure,spiritual,scenic,wildlife,village',
            'difficulty'   => 'nullable|in:easy,moderate,hard,any',
            'duration'     => 'nullable|in:1-3,4-7,8-14,any',
            'budget'       => 'nullable|in:budget,mid,premium,any',
        ]);

        UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            array_merge($validated, [
                'preferences_set' => true,
                'difficulty'      => $validated['difficulty'] ?? 'any',
                'duration'        => $validated['duration']   ?? 'any',
                'budget'          => $validated['budget']     ?? 'any',
            ])
        );

        RecommendationService::bust(auth()->id());

        return redirect()
            ->route('tour.foryou')
            ->with('success', 'Your preferences are saved! Here are your personalised treks.');
    }

    
    public function edit()
    {
        $preferences = UserPreference::forUser(auth()->id());
        $trekTypes   = UserPreference::trekTypeOptions();
        $difficulties = UserPreference::difficultyOptions();
        $durations   = UserPreference::durationOptions();
        $budgets     = UserPreference::budgetOptions();

        return view('auth.preferences', compact(
            'preferences', 'trekTypes', 'difficulties', 'durations', 'budgets'
        ),);
        
    }
}
