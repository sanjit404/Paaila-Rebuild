<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    
    public function index()
    {
        $packages = TourPackage::where('is_active', true)
            ->withCount('checkpoints')
            ->latest()
            ->get();

        return view('tours.index', compact('packages'));
    }

     public function foryou()
    {
        $packages = TourPackage::where('is_active', true)
            ->withCount('checkpoints')
            ->latest()
            ->get();

        return view('tours.foryou', compact('packages'));
    }
    
    public function show(TourPackage $package)
{
    $package->load(['checkpoints.facts']);

    $ratings = \App\Models\TrekRating::where('tour_package_id', $package->id)
        ->with('user:id,name')
        ->whereNotNull('review')
        ->latest()
        ->take(6)
        ->get();

    // also grab a few without reviews to pad if needed
    if ($ratings->count() < 3) {
        $ratings = \App\Models\TrekRating::where('tour_package_id', $package->id)
            ->with('user:id,name')
            ->latest()
            ->take(6)
            ->get();
    }

    return view('tours.show', compact('package', 'ratings'));
}

    
    public function routeData(TourPackage $package)
    {
        $checkpoints = $package->checkpoints()->with('facts')->get();

        return response()->json([
            'package' => $package,
            'checkpoints' => $checkpoints,
            'route' => $package->route_coordinates,
        ]);
    }
}
