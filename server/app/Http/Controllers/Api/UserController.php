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
    public function registerUser(RegisterUserRequest $request)
    {
        try {
                $newUser = DB::transaction(
                    function () use ($request) {
                        $newUser = User::create(
                            [
                            'email' => $request->input('user.email'),
                            'password' => Hash::make(
                                $request->input('user.password')
                            ),
                            ]
                        );
                        $newUser->profile()->create(
                            [
                            'username' => $request->input('user.username'),
                            ]
                        );
                        return $newUser;
                    }
                );

            $token = Auth::guard('api')->login($newUser);

            return (new CurrentUserResource(
                (object) [
                'user' => $newUser,
                'token' => $token,
                 ]
            ))
            ->response()
            ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(
                [
                'error' => 'Failed to register user. ' . $e->getMessage(),
                ],
                422
            );
        }
    }

    public function favorite(Request $request, $slug)
    {
        if ($article = Article::Slugged($slug)->first()) {
            $request->user()->favorite($article);
            return response()->json(
                [
                'message' => 'Article added to Favorites',
                ],
                200
            );
        }
        return response()->json(
            [
            'error' => 'Article not Found',
            ],
            404
        );
    }

    public function unfavorite(Request $request, $slug)
    {
        if ($article = Article::Slugged($slug)->first()) {
            $request->user()->unfavorite($article);
            return response()->json(
                ['message' => 'Article removed from Favorites'],
                203
            );
        }
        return response()->json(
            [
            'error' => 'Article not Found',
            ],
            404
        );
    }

    public function getCurrenUser(Request $request)
    {
        return new CurrentUserResource(
            (object) [
            'user' => Auth::user(),
            'token' => Auth::refresh(),
             ]
        );
    }

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

        $token = Auth::refresh();

        return (new CurrentUserResource(
            (object) [
            'user' => $user,
            'token' => $token,
             ]
        )
        );
    }

    public function show(Request $request, $username)
    {
        $profile = Profile::where('username', $username)->first();
        $following = false;
        if ($user = $request->user()) {
            $following = $user->isFollowing($profile->user);
        }
        return [
            'profile' => new ProfileResource(
                ['profile' => $profile, 'following' => $following]
            ),
        ];
    }

    public function follow(Request $request, $username)
    {
        $user = $request->user();
        if (($userId = Profile::idByName($username)) != null) {
            $user->follow($userId);
        }
    }

    public function unfollow(Request $request, $username)
    {
        $user = $request->user();
        if (($userId = Profile::idByName($username)) != null) {
            $user->unfollow($userId);
        }
    }
}
