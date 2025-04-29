<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reading_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'novel_id',
        'chapter_id',
        'last_page_read',
        'progress_percentage',
        'last_read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_page_read' => 'integer',
        'progress_percentage' => 'float',
        'last_read_at' => 'datetime',
    ];

    /**
     * Get the user who has the reading history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the novel that was read.
     */
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }

    /**
     * Get the chapter that was read.
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
