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
use App\Http\Resources\json\CurrentUserResource;

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

            return new CurrentUserResource(
                (object) [
                'user' => $newUser,
                'token' => $token,
                 ]
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

    public function favorite(Request $request, $slug)
    {
        if ($article = Article::where('date_slug', $slug)->first()) {
            Auth::user()->favorite($article);
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
        if ($article = Article::where('date_slug', $slug)->first()) {
            Auth::user()->unfavorite($article);
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
}
