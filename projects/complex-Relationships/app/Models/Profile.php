<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Profile extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = ['user_id', 'bio', 'website'];

    /**
     * Relasi inverse One-to-One dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
