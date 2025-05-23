<?php

namespace App\Models;

use App\Models\Course;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Teacher extends Authenticatable  implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'specialties'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'specialties' => 'array'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['type' => 'teacher'];
    }
}
