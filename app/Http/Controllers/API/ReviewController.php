<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Novel;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $novelId)
    {
        $novel = Novel::findOrFail($novelId);
        $reviews = $novel->reviews()->with('user')->get();

        return response()->json([
            'status' => true,
            'data' => $reviews
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $novelId)
    {
        $novel = Novel::findOrFail($novelId);

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_spoiler' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already reviewed this novel
        $existingReview = Review::where('user_id', Auth::id())
            ->where('novel_id', $novel->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'status' => false,
                'message' => 'You have already reviewed this novel',
                'data' => $existingReview
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['novel_id'] = $novel->id;

        $review = Review::create($data);

        // Update novel's average rating
        $this->updateNovelAverageRating($novel);

        return response()->json([
            'status' => true,
            'message' => 'Review created successfully',
            'data' => $review->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $novelId, string $reviewId)
    {
        $novel = Novel::findOrFail($novelId);
        $review = $novel->reviews()->with(['user', 'comments.user'])->findOrFail($reviewId);

        return response()->json([
            'status' => true,
            'data' => $review
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $novelId, string $reviewId)
    {
        $novel = Novel::findOrFail($novelId);
        $review = $novel->reviews()->findOrFail($reviewId);

        // Check if the authenticated user is the owner of the review
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this review'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_spoiler' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->all());

        // Update novel's average rating
        $this->updateNovelAverageRating($novel);

        return response()->json([
            'status' => true,
            'message' => 'Review updated successfully',
            'data' => $review->load('user')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $novelId, string $reviewId)
    {
        $novel = Novel::findOrFail($novelId);
        $review = $novel->reviews()->findOrFail($reviewId);

        // Check if the authenticated user is the owner of the review or an admin
        if ($review->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete this review'
            ], 403);
        }

        $review->delete();

        // Update novel's average rating
        $this->updateNovelAverageRating($novel);

        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully'
        ], 200);
    }

    /**
     * Like a review.
     */
    public function like(string $novelId, string $reviewId)
    {
        $novel = Novel::findOrFail($novelId);
        $review = $novel->reviews()->findOrFail($reviewId);

        $review->increment('likes_count');

        return response()->json([
            'status' => true,
            'message' => 'Review liked successfully',
            'data' => [
                'likes_count' => $review->likes_count
            ]
        ], 200);
    }

    public function myReviews()
    {
        $reviews = Review::with([
                'novel.author',
                'novel.category',
                'novel.favorites.user',
                'novel.reviews.user',
                'novel.chapters',
                'user'
            ])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'My reviewed novels',
            'data' => $reviews
        ], 200);
    }


    /**
     * Update novel's average rating.
     */
    private function updateNovelAverageRating(Novel $novel)
    {
        $averageRating = $novel->reviews()->avg('rating');
        $novel->average_rating = $averageRating ?? 0;
        $novel->save();
    }
}
