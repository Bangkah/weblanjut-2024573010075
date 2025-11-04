<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user dengan profile dan posts terkait
     */
    public function index()
    {
        $users = User::with('profile', 'posts')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan detail user tertentu
     */
    public function show(User $user)
    {
        // Menggunakan route model binding, Laravel akan otomatis 
        // mencari user berdasarkan ID yang diberikan
        return view('users.show', compact('user'));
    }
}
