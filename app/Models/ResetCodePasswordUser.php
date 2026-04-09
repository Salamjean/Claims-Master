<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordUser extends Model
{
    protected $fillable = [
        'code',
        'email',
    ];
}
