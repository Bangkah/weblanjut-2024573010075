<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Membuat 10 user menggunakan factory
    User::factory(10)->create();

    // Membuat profile untuk setiap user
    foreach (User::all() as $user) {
        $user->profile()->create([
            'bio' => 'Ini adalah bio untuk user ' . $user->id,
            'website' => 'https://ilmudata.id/user/' . $user->id,
        ]);
    }

    // Membuat post untuk setiap user
    foreach (User::all() as $user) {
        $user->posts()->create([
            'title' => 'Judul Post untuk user ' . $user->id,
            'content' => 'Ini adalah konten dari post untuk user ' . $user->id,
        ]);
    }

    // Membuat tag dan mengasosiasikannya dengan posts
    foreach (Post::all() as $post) {
        $tag = Tag::create(['name' => 'Tag untuk post ' . $post->id]);
        $post->tags()->attach($tag->id);
    }        
}
}
