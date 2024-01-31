<?php



use App\Http\Controllers\api\{
    AuthController,
    UserController,
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



Route::prefix('/users')->group(function() {
    Route::post('/', [UserController::class, 'registerUser']);
    Route::post('/login', [AuthController::class, 'login']);


});

/*
The great apistroitelny plan
- [ ]  Articles
    - [ ]  **GET/articles**Get recent articles globally
    - [ ]  **GET/articles/{slug}**Get an article
    - [ ]  **POST/articles** Create an article LOG
    - [ ]  **GET/articles/feed** Get recent articles from users you follow LOG
    - [ ]  **PUT/articles/{slug}** Update an article LOG
    - [ ]  **DELETE/articles/{slug}** Delete an article LOG
- [ ]  Comments
    - [ ]  **GET/articles/{slug}/comments** Get comments for an article
    - [ ]  **POST/articles/{slug}/comments** Create a comment for an article LOG
    - [ ]  **DELETE/articles/{slug}/comments/{id}** Delete a comment for an article LOG
- [ ]  Favorites
    - [ ]  **POST/articles/{slug}/favorite** Favorite an article LOG
    - [ ]  **DELETE/articles/{slug}/favorite** Unfavorite an article LOG
- [ ]  Profile
    - [ ]  **GET/profiles/{username}** Get a profile
    - [ ]  **POST/profiles/{username}/follow** Follow a user LOG
    - [ ]  **DELETE/profiles/{username}/follow** Unfollow a user LOG
- [ ]  Tags
    - [ ]  **GET/tags** Get tags
- [ ]  User and Auth
    - [ ]  **POST/users/login** Existing user login
    - [ ]  **POST/users Register**
    - [ ]  **GET/user** Get current user LOG
    - [ ]  **PUT/user**Update current user LOG
*/
