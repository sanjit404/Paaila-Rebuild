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

    
    public function show(TourPackage $package)
    {
        $package->load(['checkpoints.facts']);

        return view('tours.show', compact('package'));
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
