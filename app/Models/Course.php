<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level',
        'duration',
        'price',
        'teacher_id'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
