<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\{
    CreateArticleRequest,
    IndexArticleRequest,
    UpdateArticleRequest
};
use App\Http\Resources\json\ArticleResource;
use App\Models\{
    Article,
    Tag,
    Profile,
    User,
};
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Gate
};

class ArticleController extends Controller
{

    public function store(CreateArticleRequest $request)
    {
        $article = DB::transaction(
            function () use ($request) {

                $article = $request->user()->articles()->create(
                    ($request->validated())['article']
                );

                foreach ($request->validated()['article']['tagList'] as $tag) {
                    $tag = Tag::firstOrCreate(['tag' => $tag]);
                    $article->tags()->attach($tag);
                }

                return $article;
            }
        );

        return response(
            [
            'article' => new ArticleResource($article),
            ],
            201
        );
    }

    public function show(Request $request, $slug)
    {
        $article = Article::Slugged($slug)->first();
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


    public function index(IndexArticleRequest $request)
    {

        $limit = $request->validated()['limit'];
        $offset = $request->validated()['offset'];

        $query = Article::Tagged($request->validated()['tag'])
            ->WrittenBy(
                Profile::idByName($request->validated()['author'])
            )
            ->FavoritedBy(
                Profile::idByName($request->validated()['favorited'])
            );


        $query->orderBy('created_at', 'desc');
        $query->skip($offset)->take($limit);

        $articles = $query->get();

        $articles = ArticleResource::collection($articles);
        return [
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ];
    }

    public function feed(Request $request)
    {
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $articles = ArticleResource::collection(
            $request->user()->getFeed()
        )
            ->sortByDesc('created_at')
            ->slice($offset, $limit);


        return [
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ];
    }

    public function update(UpdateArticleRequest $request, $slug)
    {
        $article = Article::Slugged($slug)->first();

        if (Gate::denies('ud-article', $article)) {
            abort(403, 'Unauthorized action.');
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
        $article = Article::Slugged($slug)->first();
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        if (Gate::denies('ud-article', $article)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }
        $article->delete();

        return response()->json(['message' => 'Article successfully deleted'], 204);
    }
}
