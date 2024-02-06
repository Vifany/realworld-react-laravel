<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\Json\CommentResource;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $slug)
    {
        $article = Article::where('date_slug', $slug)->first();
        if ($article == null) {
            return response()->json(
                [
                    'error' => 'Article not Found',
                ],
                404
            );
        }

        $comment = $article->comments()->create(
            array_merge(
                $request->comment,
                [
                    'author_id' => $request->user()->id,
                ]
            )

        );

        return [
            'comment' => new CommentResource($comment),
        ];
    }

    public function read(Request $request, $slug)
    {
        $article = Article::where('date_slug', $slug)->first();
        if (!$article) {
            return response()->json(
                [
                    'error' => 'Article not Found',
                ],
                404
            );
        }

        $comments = CommentResource::collection($article->comments);
        return [
            'comments' => $comments,
        ];
    }

    public function destroy(Request $request, $slug, $id)
    {
        $comment = Comment::where('id', $id)->first();
        if (!($comment->isAuthor($request->user()))) {
            return response()->json(
                [
                        'error' => 'Not author of comment',
                    ],
                403
            );
        }

        $comment->delete();

        return response()->json(
            [
                    'message' => 'Comment successfully Deleted',
                ],
            204
        );
    }
}
