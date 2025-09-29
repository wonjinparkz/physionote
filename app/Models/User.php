<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'avatar',
        'phone',
        'birth_date',
        'occupation',
        'workplace',
        'experience_years',
        'bio',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get user statistics.
     */
    public function getStatistics()
    {
        // Placeholder for statistics
        // TODO: Implement actual statistics when quiz tracking is ready

        return [
            'total_attempts' => 0,
            'total_questions' => 0,
            'correct_answers' => 0,
            'wrong_answers' => 0,
            'skipped_answers' => 0,
            'average_score' => 0,
            'total_time_spent' => 0,
        ];
    }
}
