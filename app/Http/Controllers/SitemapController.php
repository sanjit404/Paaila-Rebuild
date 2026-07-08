<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $packages = TourPackage::where('is_active', true)
            ->select('id', 'updated_at')
            ->latest('updated_at')
            ->get();

        $posts = Post::select('id', 'updated_at')
            ->latest('updated_at')
            ->get();

        $content = view('components.sitemap', compact('packages', 'posts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}