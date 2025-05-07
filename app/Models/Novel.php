<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Novel extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'category_id',
        'author_id',
        'publication_date',
        'page_count',
        'language',
        'is_featured',
        'average_rating',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date',
        'is_featured' => 'boolean',
        'average_rating' => 'float',
        'view_count' => 'integer',
        'page_count' => 'integer',
    ];

    /**
     * Get the category that the novel belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author of the novel.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the chapters of the novel.
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get the reviews of the novel.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the users who favorited the novel.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritesCount()
    {
        return $this->favorites()->count();
    }
    /**
     * Get the reading history entries for the novel.
     */
    public function readingHistory()
    {
        return $this->hasMany(ReadingHistory::class);
    }

    /**
     * Get the tags associated with the novel.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'novel_tags');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'username']);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('covers')->singleFile();
    }
}


