<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// login route with token.
// starts
Route::post('/test-login', function (Request $request) {
    
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        $token = $user->createToken('apiToken'); // Remove the parentheses here

        return response()->json(['token' => $token->plainTextToken]);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
});
// stops


// auth check route with middlewire
// starts
Route::middleware('auth:sanctum')->group(function(){
    
    Route::get('/test-get', function () {
        dd(Auth::user()->email);
    });
});
// ends 
