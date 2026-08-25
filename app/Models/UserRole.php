<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    // Used in: (pivot model — direct queries via User::roles() or Role::users())
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Used in: (pivot model — direct queries via User::roles() or Role::users())
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
