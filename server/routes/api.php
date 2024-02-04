<?php

use App\Http\Controllers\api\{
    AuthController,
    UserController,
    ProfileController,
    ArticleController,
    CommentController,
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
        Route::POST('/', [UserController::class, 'registerUser']);
        Route::POST('/login', [AuthController::class, 'login']);
    }
);



Route::group(
    ['middleware' => 'auth:api', 'prefix' => '/user'],
    function () {
        Route::GET('/', [ProfileController::class, 'getCurrenUser']);
        Route::PUT('/', [ProfileController::class, 'updateCurrenUser']);
    }
);

Route::group(
    ['middleware' => 'auth:api','prefix' => '/articles'],
    function () {
        Route::POST('/', [ArticleController::class, 'create']);
        Route::GET('/', [ArticleController::class, 'index'])
            ->withoutMiddleware('auth:api');
        Route::GET('/feed', [ArticleController::class, 'feed']);

        Route::group(
            ['prefix' => '/{slug}'],
            function () {
                Route::GET('/', [ArticleController::class, 'read'])
                    ->withoutMiddleware('auth:api');
                Route::PUT('/', [ArticleController::class, 'update']);
                Route::DELETE('/', [ArticleController::class, 'delete']);

                Route::group(
                    ['prefix' => '/favorite'],
                    function () {
                        Route::POST('/', [UserController::class, 'favorite']);
                        Route::DELETE('/', [UserController::class, 'unfavorite']);
                    }
                );

                Route::group(
                    ['prefix' => '/comments'],
                    function () {
                        Route::GET('/', [CommentController::class, 'read']);
                        Route::POST('/', [CommentController::class, 'create']);
                        Route::DELETE(
                            '/{id}',
                            [CommentController::class, 'delete']
                        );
                    }
                );
            }
        );
    }
);

/*
The great apistroitelny plan
- [X]  Articles
    - [X]  **GET/articles**Get recent articles globally
    - [X]  **GET/articles/{slug}**Get an article
    - [X]  **POST/articles** Create an article AUTH
    - [X]  **GET/articles/feed** Get recent articles from users you follow AUTH
    - [X]  **PUT/articles/{slug}** Update an article AUTH
    - [X]  **DELETE/articles/{slug}** Delete an article AUTH
- [ ]  Comments
    - [X]  **GET/articles/{slug}/comments** Get comments for an article
    - [X]  **POST/articles/{slug}/comments** Create a comment for an article AUTH
    - [ ]  **DELETE/articles/{slug}/comments/{id}** Delete a comment for an article AUTH
- [X]  Favorites
    - [X]  **POST/articles/{slug}/favorite** Favorite an article AUTH
    - [X]  **DELETE/articles/{slug}/favorite** Unfavorite an article AUTH
- [ ]  Profile
    - [ ]  **GET/profiles/{username}** Get a profile
    - [ ]  **POST/profiles/{username}/follow** Follow a user AUTH
    - [ ]  **DELETE/profiles/{username}/follow** Unfollow a user AUTH
- [ ]  Tags
    - [ ]  **GET/tags** Get tags
- [X]  User and Auth
    - [X]  **POST/users/login** Existing user login
    - [X]  **POST/users Register**
    - [X]  **GET/user** Get current user AUTH
    - [x]  **PUT/user**Update current user AUTH
*/
