<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinusNotification extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'event_key', 'category', 'title', 'message', 'url', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
