<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at');
    }
}
