<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'novel_id',
        'rating',
        'comment',
        'likes_count',
        'is_spoiler',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'likes_count' => 'integer',
        'is_spoiler' => 'boolean',
    ];

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the novel that was reviewed.
     */
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * Get the comments on the review.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
