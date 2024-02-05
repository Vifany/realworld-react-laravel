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
    public function create(CommentRequest $request, $slug)
    {
        $author = Auth::user();
        $article = Article::where('date_slug', $slug)->first();
        if (!$article) {
            return response()->json(
                [
                    'error' => 'Article not Found',
                ],
                404
            );
        }

        $comment = DB::transaction(
            function () use ($request, $author, $article) {
                $comment = new Comment();
                $comment->fill(
                    $request->all()['comment']
                );
                $comment->author = $author->id;
                $comment->article = $article->id;
                $comment->save();
                return $comment;
            }
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

    public function delete(Request $request, $slug, $id)
    {
        $user = Auth::user();
        $comment = Comment::where('id', $id)->first();
        if (!($comment->isAuthor($user))) {
            return response()->json(
                [
                        'error' => 'Unauthorized',
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
