<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Post;

class Tag extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = ['name'];

    /**
     * Relasi Many-to-Many dengan Post
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
