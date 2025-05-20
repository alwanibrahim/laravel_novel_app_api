<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Chapter;


class ReadingHistoryController extends Controller
{
    /**
     * Display a listing of the user's reading history.
     */
    public function index()
    {
        $readingHistory = Auth::user()->readingHistory()
            ->with([
                'novel.author',
                'novel.category',
                'novel.favorites.user',
                'novel.reviews.user',
                'novel.chapters',
                'chapter',
                'user'
            ])
            ->orderBy('last_read_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $readingHistory
        ], 200);
    }


    /**
     * Get user's reading history for a specific novel.
     */
    public function show(string $novelId)
    {
        $readingHistory = ReadingHistory::where('user_id', Auth::id())
            ->where('novel_id', $novelId)
            ->with('chapter')
            ->first();

        if (!$readingHistory) {
            return response()->json([
                'status' => false,
                'message' => 'No reading history found for this novel'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $readingHistory
        ], 200);
    }

    /**
     * Update user's reading progress.
     */
    public function store(Request $request, string $novelId)
    {
        // Validasi kiriman chapter_number, bukan chapter_id
        $validator = Validator::make($request->all(), [
            'chapter_number' => 'required|exists:chapters,chapter_number',
            'last_page_read' => 'nullable|integer|min:1',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari ID chapter dari chapter_number
        $chapter = Chapter::where('novel_id', $novelId)
        ->where('chapter_number', $request->chapter_number)
        ->first();


        if (!$chapter) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter tidak ditemukan.'
            ], 404);
        }

        $data = [
            'user_id' => Auth::id(),
            'novel_id' => $novelId,
            'chapter_id' => $chapter->id, // gunakan ID asli
        ];

        $attributes = [
            'last_page_read' => $request->last_page_read ?? 1,
            'progress_percentage' => $request->progress_percentage ?? 0,
            'last_read_at' => now(),
        ];

        $readingHistory = ReadingHistory::updateOrCreate($data, $attributes);

        return response()->json([
            'status' => true,
            'message' => 'Reading history updated successfully',
            'data' => $readingHistory
        ], 200);
    }

    public function update(Request $request, string $novelId)
{
    // Validasi kiriman chapter_number, bukan chapter_id
    $validator = Validator::make($request->all(), [
        'chapter_number' => 'required|exists:chapters,chapter_number',
        'last_page_read' => 'nullable|integer|min:1',
        'progress_percentage' => 'nullable|numeric|min:0|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Cari ID chapter dari chapter_number
    $chapter = Chapter::where('novel_id', $novelId)
        ->where('chapter_number', $request->chapter_number)
        ->first();


    if (!$chapter) {
        return response()->json([
            'status' => false,
            'message' => 'Chapter tidak ditemukan.'
        ], 404);
    }

    // Cek apakah reading history sudah ada
    $readingHistory = ReadingHistory::where('user_id', Auth::id())
        ->where('novel_id', $novelId)
        ->where('chapter_id', $chapter->id)
        ->first();

    if (!$readingHistory) {
        return response()->json([
            'status' => false,
            'message' => 'Reading history tidak ditemukan.'
        ], 404);
    }

    // Update data yang sudah ada
    $readingHistory->update([
        'last_page_read' => $request->last_page_read ?? $readingHistory->last_page_read,
        'progress_percentage' => $request->progress_percentage ?? $readingHistory->progress_percentage,
        'last_read_at' => now(),
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Reading history berhasil diperbarui',
        'data' => $readingHistory
    ], 200);
}

    /**
     * Remove the specified reading history.
     */
    public function destroy(string $novelId)
    {
        $readingHistory = ReadingHistory::where('user_id', Auth::id())
            ->where('novel_id', $novelId)
            ->firstOrFail();

        $readingHistory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reading history deleted successfully'
        ], 200);
    }

    /**
     * Clear all reading history for the authenticated user.
     */
    public function clearAll()
    {
        ReadingHistory::where('user_id', Auth::id())->delete();

        return response()->json([
            'status' => true,
            'message' => 'All reading history cleared successfully'
        ], 200);
    }
}
