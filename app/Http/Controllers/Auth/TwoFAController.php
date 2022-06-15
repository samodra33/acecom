<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class TwoFAController extends Controller
{
    public function show(Request $request)
    {
        $google2fa = app('pragmarx.google2fa');
        $user_data = $request->all();

        $user = User::find($request->userId);

        $user_data["google2fa_secret"] = $google2fa->generateSecretKey();
        $user_data['email'] = $user->email;

        $request->session()->flash('user_data', $user_data);

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user_data['email'],
            $user_data['google2fa_secret']
        );

        $secret = $user_data['google2fa_secret'];
        return view('auth.2fa', compact('user', 'QR_Image', 'secret'));
    }

    public function complete(Request $request)
    {
        $user_data = $request->all();

        $user = User::find(auth()->id());
        $user["google2fa_secret"] = $request->google2fa_secret;
        $user->save();

        return redirect('/?firstToken=true');
    }
}
