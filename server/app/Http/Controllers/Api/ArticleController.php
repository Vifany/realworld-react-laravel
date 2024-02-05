<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Resources\json\ArticleResource;
use App\Models\{
    Article,
    Tag,
    Profile,
    User
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{

    public function store(CreateArticleRequest $request)
    {
        $article = DB::transaction(
            function () use ($request) {

                $article = new Article();
                $article->fill(
                    ($request->validated())['article']
                );
                $article->author()->associate($request->user());
                $article->save();

                foreach ($request->validated()['article']['tagList'] as $tag) {
                    $tag = Tag::firstOrCreate(['tag' => $tag]);
                    $article->tags()->attach($tag);
                }

                return $article;
            }
        );

        return [
            'article' => new ArticleResource($article),
            ];
    }

    public function show(Request $request, $slug)
    {
        $article = Article::where('date_slug', $slug)->first();
        if ($article) {
            return [
                'article' => new ArticleResource($article),
                ];
        } else {
            return response()->json(
                [
                        'error' => 'Article not Found',
                    ],
                404
            );
        }
    }


    public function index(Request $request)
    {

        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $query = Article::query();

        if ($tag = $request->input('tag',)) {
            $query->whereHas(
                'tags',
                function ($query) use ($tag) {
                    $query->where('tag', $tag);
                }
            );
        }

        if ($author = $request->input('author')) {
            $query->where('author_id', Profile::idByName($author));
        }

        if ($user = $request->input('favorited')) {
            $query->whereHas(
                'favorited',
                function ($query) {
                    $query->where(
                        'user_id',
                        Profile::idByName($user)
                    );
                }
            );
        }

        $query->orderBy('created_at', 'desc');
        $query->skip($offset)->take($limit);

        $articles = ArticleResource::collection($query->get());
        return [
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ];
    }

    public function feed(Request $request)
    {
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);
        $user = Auth::user();

        $articles = ArticleResource::collection($user->getFeed())
            ->sortByDesc('created_at')
            ->slice($offset, $limit);


        return [
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ];
    }

    public function update(Request $request, $slug)
    {
        $article = Article::where('date_slug', $slug)->first();
        if (!($article->isAuthor($request->user()))) {
            return response()->json(
                [
                        'error' => 'Unauthorized',
                    ],
                403
            );
        }

        $article = DB::transaction(
            function () use ($request, $article) {
                $article->fill(
                    ($request->validated())['article']
                );
                $article->save();
                return $article;
            }
        );

        return [
            'article' => new ArticleResource($article),
            ];
    }

    public function destroy(Request $request, $slug)
    {
        $user = Auth::user();
        $article = Article::where('date_slug', $slug)->first();
        if (!($article->isAuthor($user))) {
            return response()->json(
                [
                        'error' => 'Unauthorized',
                    ],
                403
            );
        }

        $article->delete();

        return response()->json(
            [
                    'message' => 'Article successfully Deleted',
                ],
            204
        );
    }
}
