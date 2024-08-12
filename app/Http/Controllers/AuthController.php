<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        // dd($request);
        // Validate the form data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],


        ]);
        // dd("hi");

        $systemIp = $this->getSystemIp($request);
        $cacheKey = 'password_attempts_' . $systemIp;
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return redirect()->back()->withInput()->withErrors(['password' => 'Too many failed attempts. Your IP has been blocked for 1 hour.']);
        }


        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            Cache::forget($cacheKey);
            // Authentication passed
           // $otp = Str::random(6);
            $user = Auth::user();
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
            if (Auth::attempt(['email' => $request->email])) {
                // Email exists but password is wrong
                Cache::put($cacheKey, $attempts + 1, now()->addHour());
                $attemptsLeft = 3 - ($attempts + 1);
                $errorMessage = $attemptsLeft > 0
                    ? "Invalid password. Attempts left: $attemptsLeft"
                    : 'Too many failed attempts. Your IP has been blocked for 1 hour.';
                return redirect()->back()->withInput()->withErrors(['password' => $errorMessage]);
            } else {
                // Email doesn't exist
                return redirect()->back()->withInput()->withErrors(['email' => 'Invalid email address']);
            }
        }
    }

    private function getSystemIp(Request $request)
    {
        // dd($request);
        $ip = null;

        // Check for IP address in the X-Forwarded-For header
        if ($request->header('X-Forwarded-For')) {
            $ipList = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim(end($ipList));
        }

        // If not found in X-Forwarded-For, check other common proxy headers
        if (empty($ip)) {
            $headers = [
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            ];

            foreach ($headers as $header) {
                if ($request->server($header)) {
                    $ip = trim($request->server($header));
                    break;
                }
            }
        }

        // If still not found, use the default method
        if (empty($ip)) {
            $ip = $request->ip();
        }

        // Validate the IP address
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // If the IP is not valid or is a private/reserved IP, fall back to the server's remote address
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
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
