<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Novel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorites.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('novel.author')->get();

        return response()->json([
            'status' => true,
            'data' => $favorites
        ], 200);
    }

    /**
     * Add a novel to favorites.
     */
    public function store(string $novelId)
    {
        $novel = Novel::findOrFail($novelId);

        // Check if already favorited
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('novel_id', $novel->id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'status' => false,
                'message' => 'Novel already in favorites'
            ], 422);
        }

        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->novel_id = $novel->id;
        $favorite->created_at = now();
        $favorite->save();

        return response()->json([
            'status' => true,
            'message' => 'Novel added to favorites',
            'data' => $favorite->load('novel')
        ], 201);
    }

    /**
     * Remove a novel from favorites.
     */
    public function destroy(string $novelId)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('novel_id', $novelId)
            ->firstOrFail();

        $favorite->delete();

        return response()->json([
            'status' => true,
            'message' => 'Novel removed from favorites'
        ], 200);
    }

    /**
     * Check if a novel is in the user's favorites.
     */
    public function check(string $novelId)
    {
        $isFavorite = Favorite::where('user_id', Auth::id())
            ->where('novel_id', $novelId)
            ->exists();

        return response()->json([
            'status' => true,
            'data' => [
                'is_favorite' => $isFavorite
            ]
        ], 200);
    }
}
