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
            session(['otp' => $otp]);
            $user_email = Auth::user()->email;
            if(Mail::to($user_email)->send(new SendOtpMail($otp))){
                return redirect()->route('verify-otp');
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

    public function verifyOtp(){
        return view('login.otp-modal');
    }

    public function validateOtp(Request $request){

        
        $otp = $request->otp;
        if ($otp && $otp == session('otp')) {
            session(['otp_verified' => true]);
           
            return redirect()->route('dashboard');
        } else {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }
    }


}
