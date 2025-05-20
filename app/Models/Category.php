<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    /**
     * Get the novels in this category.
     */
    public function novels()
    {
        return $this->hasMany(Novel::class);
    }

    public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function reviews()
{
    return $this->hasMany(Review::class);
}

public function chapters()
{
    return $this->hasMany(Chapter::class);
}

}
