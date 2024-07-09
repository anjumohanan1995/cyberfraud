<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        // Validate the form data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],


        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication passed
            $otp = Str::random(6);
            $user = Auth::user();
            $user->otp = $otp;
        
            if($user->save()){
                if($mail = Mail::to($user->email)->send(new SendOtpMail($user, ['otp'=>$user->otp]))){
                    return view('login.otp-modal');
                }
                else{
                    return redirect()->back()->withInput()->withErrors(['email' => 'Invalid Mail Id']);
                }
                
            }
            else{
                return redirect()->back()->withInput()->withErrors(['email' => 'Invalid ']);
            }

            
            //return redirect()->intended('/dashboard'); // Redirect to dashboard or any desired page
        } else {
            // Authentication failed
            return redirect()->back()->withInput()->withErrors(['email' => 'Invalid credentials']);
        }
    }

    public function validateOtp(Request $request){

        
        $user = Auth::user();
       
        if($request->otp == $user['otp']){
          
        return redirect()->intended('/dashboard')->with('success','Login Success'); // Redirect to dashboard or any desired page
        }
        else{
           //dd("fgiui");
           return redirect('/')->with('error','Invalid OTP');
        }
    }
}
