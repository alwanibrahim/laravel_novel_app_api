<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'category_id' => $this->category_id,
            'author_id' => $this->author_id,
            'publication_date' => $this->publication_date,
            'page_count' => $this->page_count,
            'language' => $this->language,
            'is_featured' => $this->is_featured,
            'average_rating' => $this->average_rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Menampilkan hanya 'name' dari relasi 'author'
            'author' => [
                [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'bio' => $this->author->bio,
                ]
            ],  // Membuat author menjadi array (list)

            // Menjadikan category sebagai array meskipun hanya ada satu objek
            'category' => [
                [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ]
            ],  // Membu
            'favorites'=> $this->favorites,
            'reviews' => $this->reviews
        ];
    }
}
