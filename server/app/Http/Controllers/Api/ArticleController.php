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
};
use Illuminate\Support\Facades\{
    DB,
    Gate
};

class ArticleController extends Controller
{
    /**
     * Store new article
     *
     * @param CreateArticleRequest $request
     */
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

        return response()->json(
            [
            'article' => new ArticleResource($article),
            ],
            200
        );
    }

    /**
     * Show article by slug
     *
     * @param String $slug
     */
    public function show($slug)
    {
        $article = Article::Slugged($slug)->first();
        if ($article) {
            return response()->json([
                'article' => new ArticleResource($article),
                ]);
        } else {
            return response()->json(
                [
                    'error' => 'Article not Found',
                ],
                404
            );
        }
    }

    /**
     * Show index of articles with set filter
     *
     * @param IndexArticleRequest $request request zero or more
     * of following parameters:
     * limit - amount of articles in return
     * offset - offset from beginning of return of query
     * tag - tag
     * author - author of article
     * favorited - user who favorited article
     */
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
        return response()->json([
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ]);
    }

    /**
     * Get feed for currently logged in user
     *
     * @param  Request $request
     */
    public function feed(Request $request)
    {
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $articles = ArticleResource::collection(
            $request->user()->getFeed()
        )
            ->sortByDesc('created_at')
            ->slice($offset, $limit);


        return response()->json([
            'articlesCount' => $articles->count(),
            'articles' => $articles
            ]);
    }

    /**
     * Update article by slug, if user is author
     *
     * @param  UpdateArticleRequest $request
     * @param  string $slug
     */
    public function update(UpdateArticleRequest $request, $slug)
    {
        $article = Article::Slugged($slug)->first();
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        if (Gate::denies('ud-article', $article)) {
            return response()->json(['message' => 'Not authorized'], 401);
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

        return response()->json(
            [
            'article' => new ArticleResource($article),
            ],
            200
        );
    }

    /**
     * Destroy article by slug, if user is author
     *
     * @param  Request $request
     * @param  string $slug
     */
    public function destroy(Request $request, $slug)
    {
        $article = Article::Slugged($slug)->first();
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        if (Gate::denies('ud-article', $article)) {
            return response()->json(['message' => 'Not authorized'], 401);
        }
        $article->delete();

        return response()->json(['message' => 'Article successfully deleted'], 204);
    }
}
