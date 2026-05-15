<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    

    private function rules(): array
    {
        return [
            'trek_types'   => 'nullable|array',
            'trek_types.*' => 'string|in:nature,historical,cultural,adventure,spiritual,scenic,wildlife,village',
            'difficulty'   => 'nullable|in:easy,moderate,hard,any',
            'duration'     => 'nullable|in:1-3,4-7,8-14,any',
            'budget'       => 'nullable|in:budget,mid,premium,any',
        ];
    }

  

    public function create()
    {
        $preferences  = UserPreference::forUser(auth()->id()); // null if first time
        $trekTypes    = UserPreference::trekTypeOptions();
        $difficulties = UserPreference::difficultyOptions();
        $durations    = UserPreference::durationOptions();
        $budgets      = UserPreference::budgetOptions();

        return view('auth.preferences', compact(
            'preferences', 'trekTypes', 'difficulties', 'durations', 'budgets'
        ));
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'trek_types'      => $validated['trek_types'] ?? null,
                'difficulty'      => $validated['difficulty'] ?? 'any',
                'duration'        => $validated['duration']   ?? 'any',
                'budget'          => $validated['budget']     ?? 'any',
                'preferences_set' => true,
            ]
        );

        RecommendationService::bust(auth()->id());

        return redirect()
            ->route('home')
            ->with('success', 'Preferences saved! Your recommendations are ready.');
    }

   

    public function edit()
    {
        $preferences  = UserPreference::forUser(auth()->id());
        $trekTypes    = UserPreference::trekTypeOptions();
        $difficulties = UserPreference::difficultyOptions();
        $durations    = UserPreference::durationOptions();
        $budgets      = UserPreference::budgetOptions();

        return view('auth.preferences', compact(
            'preferences', 'trekTypes', 'difficulties', 'durations', 'budgets'
        ));
    }

    

    public function update(Request $request)
    {
        $validated = $request->validate($this->rules());

        UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'trek_types'      => $validated['trek_types'] ?? null,
                'difficulty'      => $validated['difficulty'] ?? 'any',
                'duration'        => $validated['duration']   ?? 'any',
                'budget'          => $validated['budget']     ?? 'any',
                'preferences_set' => true,
            ]
        );

        RecommendationService::bust(auth()->id());

        return redirect()
            ->route('preferences.edit')
            ->with('success', 'Preferences updated! Your recommendations will refresh shortly.');
    }
}