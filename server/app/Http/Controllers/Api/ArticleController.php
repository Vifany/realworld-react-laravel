<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Resources\json\ArticleResource;

use App\Models\Article;
use Illuminate\Support\Facades\{
    Auth,
    DB
};

class ArticleController extends Controller
{
    public function create(CreateArticleRequest $request)
    {
        $user = Auth::user();
        $article = DB::transaction(
            function () use ($request, $user) {
                $article = new Article();
                $article->fill(
                    ($request->all())['article']
                );
                $article->author = $user->id;
                $article->save();
                return $article;
            }
        );

        return new ArticleResource(
            (object)[
                'user' => $user,
                'article' => $article
            ]
        );
    }

    public function read(Request $request, $slug)
    {
        $article = Article::find($slug);
        if ($article) {
            return new ArticleResource(
                (object)[
                    'user' => Auth::user(),
                    'article' => $article
                ]
            );
        } else {
            return response()->json(
                    [
                        'error' => 'Article not Found'
                    ],
                    404
                );
        }
    }

    public function index($request)
    {
        //
    }

    public function feed($request)
    {
        //
    }

    public function update($request)
    {
        //
    }

    public function delete($request)
    {
        //
    }
}
