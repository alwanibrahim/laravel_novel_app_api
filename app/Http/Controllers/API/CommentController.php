<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $comments = $review->comments()->with('user')->get();

        return response()->json([
            'status' => true,
            'data' => $comments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['review_id'] = $review->id;

        $comment = Comment::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Comment created successfully',
            'data' => $comment->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $reviewId, string $commentId)
    {
        $review = Review::findOrFail($reviewId);
        $comment = $review->comments()->with('user')->findOrFail($commentId);

        return response()->json([
            'status' => true,
            'data' => $comment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $reviewId, string $commentId)
    {
        $review = Review::findOrFail($reviewId);
        $comment = $review->comments()->findOrFail($commentId);

        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this comment'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment->load('user')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $reviewId, string $commentId)
    {
        $review = Review::findOrFail($reviewId);
        $comment = $review->comments()->findOrFail($commentId);

        // Check if the authenticated user is the owner of the comment or an admin
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete this comment'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Comment deleted successfully'
        ], 200);
    }

    /**
     * Like a comment.
     */
    public function like(string $reviewId, string $commentId)
    {
        $review = Review::findOrFail($reviewId);
        $comment = $review->comments()->findOrFail($commentId);

        $comment->increment('likes_count');

        return response()->json([
            'status' => true,
            'message' => 'Comment liked successfully',
            'data' => [
                'likes_count' => $comment->likes_count
            ]
        ], 200);
    }
}
