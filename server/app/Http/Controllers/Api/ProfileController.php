<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\json\CurrentUserResource;


class ProfileController extends Controller
{
    public function getCurrenUser(Request $request){
        /**
         * @var  App\Models\User $user
         */
        $user = Auth::user();
        $token = auth()->getToken()->get();


        return new CurrentUserResource( (object)[
            'user' =>$user,
            'token' =>$token,
        ]);

    }
}
