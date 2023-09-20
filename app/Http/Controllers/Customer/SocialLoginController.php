<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
public function redirectToProvider($provider)
{
    return Socialite::driver($provider)->redirect();
}

public function handleProviderCallback($provider)
{
    $socialUser = Socialite::driver($provider)->user();
    $user = User::firstOrCreate([
        'email' => $socialUser->getEmail(),
    ], [
        'name' => $socialUser->getName(),
        'password' => bcrypt(Str::random(16)),
        'user_type' => 'customer',
        'is_active' => 1,
    ]);

    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
    $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$user];
    return response($response, 200);
}
}
