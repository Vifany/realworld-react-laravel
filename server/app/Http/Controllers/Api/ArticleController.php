<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Resources\json\ArticleResource;

use App\Models\{
    Article,
    Tag
    };
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
                $tags = $request->input('article.tagList');


                $article = new Article();
                $article->fill(
                    ($request->all())['article']
                );
                $article->author = $user->id;
                $article->save();

                foreach ($tags as $tag) {
                    $tag = Tag::firstOrCreate(['tag'=>$tag]);
                    $article->tags()->attach($tag);
                }

                return $article;
            }
        );

        return [
            'article' => new ArticleResource($article)
            ];
    }

    public function read(Request $request, $slug)
    {
        $article = Article::where('date_slug', $slug)->first();
        if ($article) {
            return [
                'article' => new ArticleResource($article)
                ];
        } else {
            return response()->json(
                [
                        'error' => 'Article not Found'
                    ],
                404
            );
        }
    }

    public function index(Request $request = null )
    {
        if ($request == null) {
            $request = new Request();
        }
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $query = Article::query();

        if ($tag = $request->input('tag',)) {
            $query->whereHas(
                'tags', function ($query) use ($tag) {
                    $query->where('tag', $tag);
                }
            );
        }

        if ($author = $request->input('author')) {
            $query->where('author', $author);
        }

        if ($user = $request->input('favorited')) {
            $query->whereHas(
                'favorited', function ($query) use ($user) {
                    $query->where('user_id', $user);
                }
            );
        }

        $query->orderBy('created_at', 'desc');
        $query->skip($offset)->take($limit);

        $articles = ArticleResource::collection($query->get());
        return [
            'articlesCount'=>$articles->count(),
            'articles'=>$articles
            ];
    }

    public function feed(Request $request)
    {
        if ($request == null) {
            $request = new Request();
        }
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);
        $user = Auth::user();
        $articles = ArticleResource::collection(
            $user->favorites()
                ->skip($offset)
                ->paginate($limit)
        );

        return [
            'articlesCount'=>$articles->count(),
            'articles'=>$articles
            ];
    }

    public function update(Request $request, $slug)
    {
        $user = Auth::user();
        $article = Article::where('date_slug', $slug)->first();
        if (!($article->isAuthor($user))) {
            return
            response()->json(
                [
                        'error' => 'Unauthorized'
                    ],
                403
            );
        }

        $article = DB::transaction(
            function () use ($request, $article) {
                $article->fill(
                    ($request->all())['article']
                );
                $article->save();
                return $article;
            }
        );

        return [
            'article' => new ArticleResource($article)
            ];

    }

    public function delete(Request $request, $slug)
    {
        $user = Auth::user();
        $article = Article::where('date_slug', $slug)->first();
        if (!($article->isAuthor($user))) {
            return
            response()->json(
                [
                        'error' => 'Unauthorized'
                    ],
                403
            );
        }

        $article ->delete();

        return
        response()->json(
            [
                    'message' => 'Article successfully Deleted'
                ],
            204
        );
    }
}
