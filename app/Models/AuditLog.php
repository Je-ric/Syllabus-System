<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'reference_id',
        'description',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public $timestamps = true;

    // Used in: logs() - AuditLog (Livewire);
    //          sharedData() - SyllabusPreviewService
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static helper used everywhere to record an audit entry
    public static function record(
        string $action,
        string $module,
        ?int $referenceId = null,
        ?string $description = null,
        ?int $userId = null
    ): ?self {
        try {
            return static::create([
                'user_id'      => $userId ?? Auth::id(),
                'action'       => $action,
                'module'       => $module,
                'reference_id' => $referenceId,
                'description'  => $description,
                'timestamp'    => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AuditLog::record failed: ' . $e->getMessage());
            return null;
        }
    }
}
