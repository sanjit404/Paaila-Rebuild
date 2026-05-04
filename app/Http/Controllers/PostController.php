<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Rating;
use App\Services\UserIdentifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Post::with('trek');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->ofType($request->type);
        }

        if ($request->has('trending')) {
            $query->trending();
        } else {
            $query->latest();
        }

        $posts = $query->paginate(12);

        $highlighted = Post::highlighted()->with('trek')->first();

        $identifier = UserIdentifierService::getIdentifier();

        $likedPostIds = Like::where('identifier', $identifier)
            ->pluck('post_id')
            ->toArray();

        $userRatings = Rating::where('identifier', $identifier)
            ->pluck('rating', 'post_id')
            ->toArray();

        return view('feed.index', compact(
            'posts',
            'highlighted',
            'identifier',
            'likedPostIds',
            'userRatings'
        ));
    }

  
    public function show(Post $post)
    {
        $post->load('trek', 'ratings');
        
        $identifier = UserIdentifierService::getIdentifier();
        $userRating = $post->getRatingBy($identifier);
        $hasLiked = $post->isLikedBy($identifier);

        return view('feed.show', compact('post', 'userRating', 'hasLiked', 'identifier'));
    }

    
    public function like(Post $post)
    {
        $identifier = UserIdentifierService::getIdentifier();

        try {
            DB::beginTransaction();

            $existingLike = Like::where('post_id', $post->id)
                ->where('identifier', $identifier)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $action = 'unliked';
            } else {
                Like::create([
                    'post_id' => $post->id,
                    'identifier' => $identifier,
                ]);
                $action = 'liked';
            }

            DB::commit();

            $post->refresh();

            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $post->likes_count,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process like',
            ], 500);
        }
    }

   
    public function rate(Request $request, Post $post)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $identifier = UserIdentifierService::getIdentifier();

        try {
            DB::beginTransaction();

            Rating::updateOrCreate(
                [
                    'post_id' => $post->id,
                    'identifier' => $identifier,
                ],
                [
                    'rating' => $request->rating,
                ]
            );

            DB::commit();

            $post->refresh();

            return response()->json([
                'success' => true,
                'rating_avg' => $post->rating_avg,
                'rating_count' => $post->rating_count,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save rating',
            ], 500);
        }
    }
}