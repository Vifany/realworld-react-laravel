<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Get index for tags
     */
    public function index()
    {
        return response()->json([
            'tags' => Tag::orderBy('tag')->pluck('tag'),
        ]);
    }
}
