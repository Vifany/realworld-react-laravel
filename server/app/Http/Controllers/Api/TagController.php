<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        return [
            'tags' => Tag::orderBy('tag')->pluck('tag'),
        ];
    }
}
