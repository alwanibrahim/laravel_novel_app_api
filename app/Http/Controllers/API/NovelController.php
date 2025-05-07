<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Novel;
use Illuminate\Http\Request;
use App\Http\Resources\NovelResource;
use Illuminate\Support\Facades\Validator;

class NovelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Novel::with(['author', 'category', 'favorites.user' ,'reviews.user']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Filter by featured status
        if ($request->has('featured')) {
            $query->where('is_featured', $request->featured == 'true' ? true : false);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->per_page ?? 15;
        $novels = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
        // return NovelResource::collection($novels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'publication_date' => 'nullable|date',
            'page_count' => 'nullable|integer',
            'language' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle cover_image upload
        $data = $request->except('tags');
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('cover_images', 'public');
        }

        $novel = Novel::create($data);

        // Attach tags if provided
        if ($request->has('tags')) {
            $novel->tags()->attach($request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'Novel created successfully',
            'data' => $novel->load(['author', 'category', 'tags'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $novel = Novel::with(['author', 'category', 'tags', 'chapters'])->findOrFail($id);

        // Increment view count
        $novel->increment('view_count');

        return response()->json([
            'status' => true,
            'data' => $novel
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $novel = Novel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_id' => 'nullable|exists:authors,id',
            'publication_date' => 'nullable|date',
            'page_count' => 'nullable|integer',
            'language' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $novel->update($request->except('tags'));

        // Sync tags if provided
        if ($request->has('tags')) {
            $novel->tags()->sync($request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'Novel updated successfully',
            'data' => $novel->load(['author', 'category', 'tags'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $novel = Novel::findOrFail($id);
        $novel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Novel deleted successfully'
        ], 200);
    }

    /**
     * Get featured novels.
     */
    public function featured()
    {
        $novels = Novel::where('is_featured', true)
            ->with(['author', 'category'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
    }

    /**
     * Search novels by title or description.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->query('query');

        $novels = Novel::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with(['author', 'category'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
    }

    /**
     * Get novels by category.
     */
    public function byCategory(string $categoryId)
    {
        $novels = Novel::where('category_id', $categoryId)
            ->with(['author', 'category'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
    }

    /**
     * Get novels by author.
     */
    public function byAuthor(string $authorId)
    {
        $novels = Novel::where('author_id', $authorId)
            ->with(['category'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
    }

    /**
     * Get novels by tag.
     */
    public function byTag(string $tagId)
    {
        $novels = Novel::whereHas('tags', function($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
            ->with(['author', 'category'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $novels
        ], 200);
    }
}
