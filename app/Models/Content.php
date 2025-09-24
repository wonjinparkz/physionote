<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'sub_category_id',
        'title',
        'body',
        'thumbnail',
        'sort_order',
        'is_published',
        'badge',
        'custom_data',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'custom_data' => 'array',
    ];

    protected $attributes = [
        'category' => 'card_news',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
