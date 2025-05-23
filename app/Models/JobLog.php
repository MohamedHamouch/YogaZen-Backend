<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    protected $fillable = [
        'action',
        'target_type',
        'target_id',
        'details',
        'status'
    ];

    public static function logAction($action, $targetType, $targetId, $details = null, $status = 'success')
    {
        return self::create([
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
            'status' => $status
        ]);
    }
}
