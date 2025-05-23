<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'started_at',
        'expires_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isActive()
    {
        return $this->expires_at > now();
    }

    public function isExpiringSoon($days = 3)
    {
        return $this->expires_at <= now()->addDays($days) && $this->isActive();
    }
}
