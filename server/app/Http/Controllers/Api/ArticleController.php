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
                $article->author=$user->id;
                $article->save();
                return $article;
            }
        );

        return new ArticleResource(
            (object)[
            'user'=>$user,
            'article'=>$article
            ]
        );

    }

    public function read($request)
    {
        //
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
