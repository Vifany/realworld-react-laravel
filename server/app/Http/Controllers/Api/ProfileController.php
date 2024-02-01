<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\json\CurrentUserResource;
use App\Http\Requests\{
    UpdateUserRequest,
};


class ProfileController extends Controller
{
    public function getCurrenUser(Request $request){
        /**
         * @var  App\Models\User $user
         */
        $user = Auth::user();
        $token = Auth::refresh();


        return new CurrentUserResource( (object)[
            'user' =>$user,
            'token' =>$token,
        ]);

    }

    public function updateCurrenUser(UpdateUserRequest $request){
        $user = Auth::user();

        foreach ($request['user'] as $key => $value) {
            // Only update the field if the value is not null
            if ($value !== null) {
                $user->profile->$key = $value;
            }
        }

        $token = Auth::refresh();

        return new CurrentUserResource( (object)[
            'user' =>$user,
            'token' =>$token,
        ]);
    }
}
