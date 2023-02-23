<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    public const STATUS_ON_GOING = 'on_going';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'started_at',
        'ended_at',
        'current_status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getAllValidStatus()
    {
        return [ self::STATUS_ON_GOING, self::STATUS_CANCELLED, self::STATUS_CLOSED ];
    }
}
