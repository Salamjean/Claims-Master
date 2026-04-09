<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstatPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'constat_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function constat()
    {
        return $this->belongsTo(Constat::class);
    }
}
