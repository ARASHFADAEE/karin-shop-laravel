<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'minimum_amount',
        'usage_limit',
        'usage_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at');
    }
}
