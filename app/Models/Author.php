<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'bio',
        'profile_picture',
    ];

    /**
     * Get the novels written by the author.
     */
    public function novels()
    {
        return $this->hasMany(Novel::class);
    }
}
