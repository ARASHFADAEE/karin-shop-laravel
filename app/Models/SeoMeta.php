<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'metaable_id',
        'metaable_type',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'robots',
    ];

    // Relationships
    public function metaable()
    {
        return $this->morphTo();
    }
}
