<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'novel_id',
        'title',
        'content',
        'chapter_number',
        'word_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'chapter_number' => 'integer',
        'word_count' => 'integer',
    ];

    /**
     * Get the novel that the chapter belongs to.
     */
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * Get the reading history entries for the chapter.
     */
    public function readingHistory()
    {
        return $this->hasMany(ReadingHistory::class);
    }
}
