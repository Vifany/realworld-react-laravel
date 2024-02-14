<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    Gate
};
use App\Http\Requests\CommentRequest;
use App\Http\Resources\Json\CommentResource;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $slug)
    {
        $article = Article::Slugged($slug)->first();
        if (!$article) {
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
        $article = Article::Slugged($slug)->first();
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
        if (Gate::denies('d-comment', $comment)) {
            return response()->json(['message' => 'Not authorized'], 401);
        } else {
            $comment->delete();

            return response()->json(
                [
                    'message' => 'Comment successfully Deleted',
                ],
                200
            );
        }
    }
}
