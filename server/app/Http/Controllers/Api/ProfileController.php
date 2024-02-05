<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\json\{
    CurrentUserResource,
    ProfileResource,
};
use App\Http\Requests\UpdateUserRequest;

use App\Models\Profile;

class ProfileController extends Controller
{
    public function getCurrenUser(Request $request)
    {
        /**
         * @var App\Models\User $user
         */
        $user = Auth::user();
        $token = Auth::refresh();


        return new CurrentUserResource(
            (object) [
            'user' => $user,
            'token' => $token,
             ]
        );
    }

    public function updateCurrenUser(UpdateUserRequest $request)
    {
        $user = Auth::user();

            DB::transaction(
                function () use ($user, $request) {
                    $user->fill($request->all()['user']);
                    $user->profile->fill($request->all()['user']);
                    $user->save();
                }
            );

        $token = Auth::refresh();

        return new CurrentUserResource(
            (object) [
            'user' => $user,
            'token' => $token,
             ]
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
