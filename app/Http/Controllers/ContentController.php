<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function show($id)
    {
        $content = Content::with('subCategory.category')
            ->where('is_published', true)
            ->findOrFail($id);

        // Get related contents from the same subcategory
        $relatedContents = Content::where('sub_category_id', $content->sub_category_id)
            ->where('id', '!=', $content->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        return view('content.show', compact('content', 'relatedContents'));
    }
}