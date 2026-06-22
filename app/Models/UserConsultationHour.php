<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConsultationHour extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
