<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use App\Http\Resources\json\CurrentUserResource;
use App\Http\Requests\{
    UpdateUserRequest,
};


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
            (object)[
            'user' =>$user,
            'token' =>$token,
             ]
        );

    }

    public function updateCurrenUser(UpdateUserRequest $request)
    {
        $user = Auth::user();

            DB::transaction(
                function () use ($user,$request) {
                    $user->fill($request->all()['user']);
                    $user->profile->fill($request->all()['user']);
                    $user->save();
                }
            );

        $token = Auth::refresh();

        return new CurrentUserResource(
            (object)[
            'user' =>$user,
            'token' =>$token,
             ]
        );
    }
}
