<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;



class LogoutController extends Controller
{
    public function logout()
    {
        Auth::logout();
        Session::forget('otp_verified');
        return redirect('/');
    }



    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(50);
            $user->reset_password_token = $token;
            $user->save();

            //  dd($user->email, $token);

           $mail = Mail::to($user->email)->send(new PasswordResetMail($user, $token));
         
            if($mail){
                return redirect(url('/'))->with('success', 'Password reset link sent successfully!!');
            }
            else{
                return redirect(url('/'))->with('error', 'User not found!!');
            }
            // return "Password reset link sent successfully!";
        } else {
            return redirect(url('/'))->with('error', 'User not found!!');
        }
    }

    public function resetPassword($token)
    {
        //dd($token);
        $user = User::where('reset_password_token', $token)->first();

        if ($user) {

            $id = $user->id;
            return view('auth.passwords.reset_password', compact(['id']));
        } else {


            return view('auth.passwords.email')->with('error', 'Invalid token!');
        }
    }

    public function resetPasswordView()
    {

        return view('auth.passwords.reset_password');
    }

    public function passwordUpdate(Request $request)

    {


        $request->validate([
            'id' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::find($request->id);

        if (!$user) {
            return view('auth.passwords.email')->with('error', 'User not found!');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect(url('/'))->with('success', 'Password updated successfully!');
    }
}
