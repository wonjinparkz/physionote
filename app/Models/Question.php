<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'question',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'option_5',
        'answer',
        'explanation',
        'category_id',
        'sub_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    protected static function booted()
    {
        static::creating(function ($question) {
            // sub_category_id가 있고 category_id가 없으면 자동 설정
            if ($question->sub_category_id && !$question->category_id) {
                $subCategory = SubCategory::find($question->sub_category_id);
                if ($subCategory) {
                    $question->category_id = $subCategory->category_id;
                }
            }
        });

        static::updating(function ($question) {
            // sub_category_id가 변경되면 category_id도 업데이트
            if ($question->isDirty('sub_category_id') && $question->sub_category_id) {
                $subCategory = SubCategory::find($question->sub_category_id);
                if ($subCategory) {
                    $question->category_id = $subCategory->category_id;
                }
            }
        });
    }
}
