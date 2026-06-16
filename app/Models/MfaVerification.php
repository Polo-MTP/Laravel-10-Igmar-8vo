<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MfaVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'mfa_method_id',
        'code_hash',
        'used',
        'expires_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function mfaMethod()
    {
        return $this->belongsTo(MfaMethod::class);
    }
}
