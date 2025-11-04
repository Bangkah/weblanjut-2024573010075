<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Menampilkan daftar semua posts dengan user dan tags terkait
     */
    public function index()
    {
        $posts = Post::with('user', 'tags')->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Menampilkan detail post tertentu
     */
    public function show(Post $post)
    {
        // Menggunakan route model binding, Laravel akan otomatis
        // mencari post berdasarkan ID yang diberikan
        return view('posts.show', compact('post'));
    }
}
