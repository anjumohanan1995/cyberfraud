<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    protected $maxAttempts = 3;
    protected $lockoutTime = 60; // in minutes

    public function login(Request $request)
    {
        // Validate the form data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],


        ]);

        $email = $request->input('email');
        $cacheKey = $this->getFailedAttemptsCacheKey($email);
        // dd($cacheKey);

        Log::info('Cache Key:', ['key' => $cacheKey]);
        Log::info('Failed Attempts:', ['attempts' => Cache::get($cacheKey)]);

        if ($this->hasTooManyLoginAttempts($cacheKey)) {
            $minutes = $this->getLockoutTimeRemaining($cacheKey);
            return redirect()->back()->withInput()->withErrors(['email' => "Too many login attempts. Please try again in $minutes minutes."]);
        }


        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication passed
            // Reset failed login attempts
            $this->clearLoginAttempts($cacheKey);
           // $otp = Str::random(6);
            // $user = Auth::user();
            //session(['otp' => $otp]);
           //$user_email = Auth::user()->email;
            // if(Mail::to($user_email)->send(new SendOtpMail($otp))){
            //     return redirect()->route('verify-otp');
            // }
            // else{
            //     return redirect()->back()->withInput()->withErrors(['email' => 'Invalid ']);
            // }


            return redirect()->intended('/dashboard'); // Redirect to dashboard or any desired page
        } else {
            // Authentication failed
            $this->incrementLoginAttempts($cacheKey);
            return redirect()->back()->withInput()->withErrors(['email' => 'Invalid Email']);
        }
    }

    protected function getFailedAttemptsCacheKey($email)
    {
        return 'login_attempts_' . md5($email);
    }

    protected function hasTooManyLoginAttempts($cacheKey)
    {
        return Cache::has($cacheKey) && Cache::get($cacheKey)['attempts'] >= $this->maxAttempts;
    }

    protected function incrementLoginAttempts($cacheKey)
    {
        $failedAttempts = Cache::get($cacheKey, ['attempts' => 0, 'locked_at' => null]);
        $failedAttempts['attempts'] += 1;

        if ($failedAttempts['attempts'] >= $this->maxAttempts) {
            $failedAttempts['locked_at'] = Carbon::now()->addMinutes($this->lockoutTime);
        }

        Cache::put($cacheKey, $failedAttempts, $this->lockoutTime);
    }

    protected function clearLoginAttempts($cacheKey)
    {
        Cache::forget($cacheKey);
    }

    protected function getLockoutTimeRemaining($cacheKey)
    {
        $failedAttempts = Cache::get($cacheKey);
        if ($failedAttempts && $failedAttempts['locked_at']) {
            $lockedAt = Carbon::parse($failedAttempts['locked_at']);
            $remainingMinutes = max(0, $lockedAt->diffInMinutes(Carbon::now()));
            return $remainingMinutes;
        }

        return 0;
    }

    // public function verifyOtp(){
    //     return view('login.otp-modal');
    // }

    // public function validateOtp(Request $request){


    //     $otp = $request->otp;
    //     if ($otp && $otp == session('otp')) {
    //         session(['otp_verified' => true]);

    //         return redirect()->route('dashboard');
    //     } else {
    //         return back()->with('error', 'Invalid OTP. Please try again.');
    //     }
    // }


}
