<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MfaType extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'type', 'is_active'];


        public function mfaMethods()
    {
        return $this->hasMany(MfaMethod::class, 'mfa_type_id');
    }


}
