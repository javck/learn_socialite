<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $provider_user = Socialite::driver($provider)->user();
        //dd($provider_user);

        //先檢查用戶是否存在
        $user = User::where('provider',$provider)->where('provider_id',$provider_user->getId())->first();
        //當用戶不存在時，先把用戶存入資料庫
        if(!$user){
            $user = User::create([
                'name' => $provider_user->getName(),
                'nickname' => $provider_user->getNickName(),
                'email'=> $provider_user->getEmail(),
                'provider' => $provider,
                'provider_id' => $provider_user->getId(),
                'avatar' => $provider_user->getAvatar()
            ]);
        }
        

        //協助完成登入
        auth()->login($user,true);

        //轉址到Dashboard
        return redirect('dashboard');
    }
}