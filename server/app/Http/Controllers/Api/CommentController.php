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
    /**
     * Store comment for an article by article slug
     *
     * @param  CommentRequest $request
     * @param  string $slug
     */
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
                $request->validated()['comment'],
                [
                    'author_id' => $request->user()->id,
                ]
            )
        );

        return response()->json([
            'comment' => new CommentResource($comment),
        ]);
    }

    /**
     * Read comments of an article by slug
     *
     * @param  Request $request
     * @param  string $slug
     */
    public function read($slug)
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
        return response()->json([
            'comments' => $comments,
        ]);
    }

    /**
     * Destroy comment for an article if user is author of comment
     *
     * @param string $slug - specification requires
     * @param  int $id - comment id
     */
    public function destroy($slug, $id)
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
