<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $preferences = \App\Models\UserPreference::forUser($user->id);

        $stats = [
            'total' => \App\Models\TourBooking::where('user_id', $user->id)->count(),
            'completed' => \App\Models\TourBooking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'active' => \App\Models\TourBooking::where('user_id', $user->id)->where('status', 'active')->count(),
            'ratings' => \App\Models\TrekRating::where('user_id', $user->id)->count(),
        ];

        $recentBookings = \App\Models\TourBooking::where('user_id', $user->id)
            ->with('tourPackage')
            ->latest()
            ->take(5)
            ->get();

        $myRatings = \App\Models\TrekRating::where('user_id', $user->id)
            ->with('tourPackage')
            ->latest()
            ->take(5)
            ->get();

        return view('profile.show', compact('user', 'preferences', 'stats', 'recentBookings', 'myRatings'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            
        ]);

        if (!empty($validated['email']) && !empty($user->email) && $validated['email'] !== $user->email) {
            return back()->withErrors([
                'email' => 'Email cannot be changed after it has been set.',
            ])->withInput();
        }

        if (!empty($validated['phone']) && !empty($user->phone) && $validated['phone'] !== $user->phone) {
            return back()->withErrors([
                'phone' => 'Phone cannot be changed after it has been set.',
            ])->withInput();
        }

        $user->update([
            'name' => $validated['name'],
            'email' => empty($user->email) ? ($validated['email'] ?? null) : $user->email,
            'phone' => empty($user->phone) ? ($validated['phone'] ?? null) : $user->phone,
            'address' => $validated['address'] ?? null,
           
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}