<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Novel;
use App\Models\ReadingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(string $novelId)
    {
        $novel = Novel::findOrFail($novelId);
        $chapters = $novel->chapters()->orderBy('chapter_number')->get();

        return response()->json([
            'status' => true,
            'data' => $chapters
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $novelId)
    {
        $novel = Novel::findOrFail($novelId);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'chapter_number' => 'required|integer|min:1',
            'word_count' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate word count if not provided
        $data = $request->all();
        if (!isset($data['word_count'])) {
            $data['word_count'] = str_word_count(strip_tags($data['content']));
        }

        $data['novel_id'] = $novel->id;
        $chapter = Chapter::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Chapter created successfully',
            'data' => $chapter
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $novelId, string $chapterId)
    {
        $novel = Novel::findOrFail($novelId);
        $chapter = $novel->chapters()->findOrFail($chapterId);

        // Update reading history if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Calculate progress percentage
            $totalChapters = $novel->chapters()->count();
            $currentChapterNumber = $chapter->chapter_number;
            $progressPercentage = ($currentChapterNumber / $totalChapters) * 100;

            ReadingHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'novel_id' => $novel->id,
                    'chapter_id' => $chapter->id,
                ],
                [
                    'progress_percentage' => $progressPercentage,
                    'last_read_at' => now(),
                ]
            );
        }

        return response()->json([
            'status' => true,
            'data' => $chapter
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $novelId, string $chapterId)
    {
        $novel = Novel::findOrFail($novelId);
        $chapter = $novel->chapters()->findOrFail($chapterId);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'chapter_number' => 'nullable|integer|min:1',
            'word_count' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate word count if content is updated but word_count is not
        $data = $request->all();
        if (isset($data['content']) && !isset($data['word_count'])) {
            $data['word_count'] = str_word_count(strip_tags($data['content']));
        }

        $chapter->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Chapter updated successfully',
            'data' => $chapter
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $novelId, string $chapterId)
    {
        $novel = Novel::findOrFail($novelId);
        $chapter = $novel->chapters()->findOrFail($chapterId);
        $chapter->delete();

        return response()->json([
            'status' => true,
            'message' => 'Chapter deleted successfully'
        ], 200);
    }
}
