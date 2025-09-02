<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'first_name',
        'last_name',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($address) {
            if ($address->is_default) {
                // Remove default from other addresses of the same user
                static::where('user_id', $address->user_id)
                      ->update(['is_default' => false]);
            }
        });
        
        static::updating(function ($address) {
            if ($address->is_default && $address->isDirty('is_default')) {
                // Remove default from other addresses of the same user
                static::where('user_id', $address->user_id)
                      ->where('id', '!=', $address->id)
                      ->update(['is_default' => false]);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Helper methods
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->state . 
               ($this->postal_code ? ', کد پستی: ' . $this->postal_code : '');
    }

    public function setAsDefault()
    {
        // Remove default from other addresses
        static::where('user_id', $this->user_id)
              ->where('id', '!=', $this->id)
              ->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }
}
