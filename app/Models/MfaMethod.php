<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaMethod extends Model
{
    use HasFactory, HasUuids; 

    protected $fillable = ['user_id', 'mfa_type_id', 'secret', 'factor_step', 'is_verified', 'is_active'];

    public function type()
    {
        return $this->belongsTo(MfaType::class, 'mfa_type_id');
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

}
