<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // --- ADD THESE LINES ---
    protected $primaryKey = 'user_id'; // Tell Laravel the primary key is 'user_id'
    public $incrementing = false;      // Tell Laravel the ID is NOT auto-incrementing (since it's a string)
    protected $keyType = 'string';     // Tell Laravel the ID is a string
    // -----------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', // Add this if you want to be able to mass-assign the ID during creation
        'name',
        'email',
        'password',
        'role',    // Good practice to add 'role' here too given your new migration
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}