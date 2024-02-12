<?php


use App\Http\Controllers\api\{
    AuthController,
    UserController,
    ProfileController,
    ArticleController,
    CommentController,
    TagController,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::prefix('/users')->group(
    function () {
        Route::POST('/', [UserController::class, 'registerUser'])
            ->name('api.users.register');
        Route::POST('/login', [AuthController::class, 'login'])
            ->name('api.users.login');
    }
);



Route::group(
    ['middleware' => 'auth:api', 'prefix' => '/user'],
    function () {
        Route::GET('/', [UserController::class, 'getCurrenUser'])
            ->name('api.user.get');
            Route::PUT('/', [UserController::class, 'updateCurrenUser'])
            ->name('api.user.update');
    }
);

Route::group(
    ['middleware' => 'auth:api', 'prefix' => '/profiles/{username}'],
    function () {
        Route::GET('/', [UserController::class, 'show'])
            ->name('api.profile.show');
        Route::POST('/follow', [UserController::class, 'follow'])
            ->name('api.profile.follow');
        Route::DELETE('/follow', [UserController::class, 'unfollow'])
            ->name('api.profile.unfollow');
    }
);

Route::group(
    ['middleware' => 'auth:api','prefix' => '/articles'],
    function () {
        Route::POST('/', [ArticleController::class, 'store'])
            ->name('api.articles.store');
        Route::GET('/', [ArticleController::class, 'index'])->withoutMiddleware('auth:api')
            ->name('api.articles.index');
        Route::GET('/feed', [ArticleController::class, 'feed'])
            ->name('api.articles.feed');

        Route::group(
            ['prefix' => '/{slug}'],
            function () {
                Route::GET('/', [ArticleController::class, 'show'])->withoutMiddleware('auth:api')
                    ->name('api.articles.read');
                Route::PUT('/', [ArticleController::class, 'update'])
                    ->name('api.articles.update');
                Route::DELETE('/', [ArticleController::class, 'destroy'])
                    ->name('api.articles.destroy');

                Route::group(
                    ['prefix' => '/favorite'],
                    function () {
                        Route::POST('/', [UserController::class, 'favorite'])
                            ->name('api.articles.favorite');
                        Route::DELETE('/', [UserController::class, 'unfavorite'])
                            ->name('api.articles.unfavorite');
                    }
                );

                Route::group(
                    ['prefix' => '/comments'],
                    function () {
                        Route::GET('/', [CommentController::class, 'read']);
                        Route::POST('/', [CommentController::class, 'store']);
                        Route::DELETE('/{id}', [CommentController::class, 'destroy']);
                    }
                );
            }
        );
    }
);



Route::GET('/tags', [TagController::class, 'index'])->name('api.tags.get');

/*
The great apistroitelny plan
- [X]  Articles
    - [X]  **GET/articles**Get recent articles globally
    - [X]  **GET/articles/{slug}**Get an article
    - [X]  **POST/articles** Create an article AUTH
    - [X]  **GET/articles/feed** Get recent articles from users you follow AUTH
    - [X]  **PUT/articles/{slug}** Update an article AUTH
    - [X]  **DELETE/articles/{slug}** Delete an article AUTH
- [X]  Comments
    - [X]  **GET/articles/{slug}/comments** Get comments for an article
    - [X]  **POST/articles/{slug}/comments** Create a comment for an article AUTH
    - [X]  **DELETE/articles/{slug}/comments/{id}** Delete a comment for an article AUTH
- [X]  Favorites
    - [X]  **POST/articles/{slug}/favorite** Favorite an article AUTH
    - [X]  **DELETE/articles/{slug}/favorite** Unfavorite an article AUTH
- [X]  Profile
    - [X]  **GET/profiles/{username}** Get a profile
    - [X]  **POST/profiles/{username}/follow** Follow a user AUTH
    - [X]  **DELETE/profiles/{username}/follow** Unfollow a user AUTH
- [X]  Tags
    - [X]  **GET/tags** Get tags
- [X]  User and Auth
    - [X]  **POST/users/login** Existing user login
    - [X]  **POST/users Register**
    - [X]  **GET/user** Get current user AUTH
    - [x]  **PUT/user**Update current user AUTH
*/
