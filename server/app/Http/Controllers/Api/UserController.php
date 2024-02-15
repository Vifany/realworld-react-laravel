<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\Article;
use App\Http\Resources\json\{
    CurrentUserResource,
    ProfileResource,
};
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Store and login new user
     *
     * @param  RegisterUserRequest $request
     */
    public function registerUser(RegisterUserRequest $request)
    {
        try {
                $newUser = DB::transaction(
                    function () use ($request) {
                        $newUser = User::create(
                            [
                            'email' => $request->validated()['user']['email'],
                            'password' => Hash::make(
                                $request->validated()['user']['password']
                            ),
                            ]
                        );
                        $newUser->profile()->create(
                            [
                            'username' => $request->validated()['user']['username'],
                            ]
                        );
                        return $newUser;
                    }
                );

            $token = Auth::guard('api')->login($newUser);

            return response()->json(
                new CurrentUserResource(
                    (object) [
                    'user' => $newUser,
                    'token' => $token,
                    ]
                ),
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                'error' => 'Failed to register user. ' . $e->getMessage(),
                ],
                422
            );
        }
    }

    /**
     * Add article to favorites by slug
     *
     * @param  Request $request
     * @param  string $slug
     */
    public function favorite(Request $request, $slug)
    {
        $article = Article::Slugged($slug)->firstOrFail();
        $request->user()->favorite($article);
        return response()->json(
            ['message' => 'Article added to Favorites'],
            200
        );
    }

    /**
    * Remove article from favorites by slug
    *
    * @param  Request $request
    * @param  string $slug
    */
    public function unfavorite(Request $request, $slug)
    {
        $article = Article::Slugged($slug)->firstOrFail();
        $request->user()->unfavorite($article);
        return response()->json(
            ['message' => 'Article removed from Favorites'],
            200
        );
    }

    /**
     * Refresh current user info
     *
     * @param  Request $request
     */
    public function getCurrenUser()
    {
        return response()->json(new CurrentUserResource(
            (object) [
            'user' => Auth::user(),
            'token' => Auth::refresh(),
             ]
        ));
    }

    /**
     * Update current user database entry
     *
     * @param  UpdateUserRequest $request
     */
    public function updateCurrenUser(UpdateUserRequest $request)
    {
        $user = $request->user();

            DB::transaction(
                function () use ($user, $request) {
                    $user->fill($request->validated()['user']);
                    $user->profile->fill($request->validated()['user']);
                    $user->save();
                }
            );

        return response()->json(new CurrentUserResource(
            (object) [
            'user' => $user,
            'token' => Auth::refresh(),
            ]
        ));
    }

    /**
     * Show user profile by username
     *
     * @param  Request $request
     * @param  string $username
     */
    public function show(Request $request, $username)
    {
        $profile = Profile::where('username', $username)->first();
        $following = false; //in case of not logged in user
        if ($user = $request->user()) {
            $following = $user->isFollowing($profile->user);
        }
        return response()->json([
            'profile' => new ProfileResource(
                (object) [
                    'profile' => $profile,
                    'following' => $following,
                ]
            ),
        ]);
    }

    /**
     * Follow user by username
     *
     * @param  Request $request
     * @param  string $username
     */
    public function follow(Request $request, $username)
    {
        $user = $request->user();
        if (($userId = Profile::idByName($username)) != null) {
            $user->follow($userId);
            $profile = Profile::where('username', $username)->first();
            $following = $user->isFollowing($profile->user);
            return [
                'profile' => new ProfileResource(
                    (object) [
                        'profile' => $profile,
                        'following' => $following,
                    ]
                ),
            ];
        }
    }

    /**
     * Unfollow user by username
     *
     * @param  Request $request
     * @param  string $username
     */
    public function unfollow(Request $request, $username)
    {
        $user = $request->user();
        if (($userId = Profile::idByName($username)) != null) {
            $user->unfollow($userId);
            $profile = Profile::where('username', $username)->first();
            $following = $user->isFollowing($profile->user);
            return [
                'profile' => new ProfileResource(
                    (object) [
                        'profile' => $profile,
                        'following' => $following,
                    ]
                ),
            ];
        }
    }
}
