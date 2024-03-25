<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardPagesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/', function () {
    return view('login.login');
});

//login route starts here
Route::post('/login', [AuthController::class, 'login'])->name("login");


//dashboard pages starts here.
Route::get('/dashboard', [DashboardPagesController::class, 'dashboard'])->name("dashboard");


//users route starts here.
Route::resource('users', UsersController::class);
Route::get('users-management/users/get', [UsersController::class, 'getUsers'])->name("get.users");


//permmission route starts here.
Route::resource('permissions', PermissionController::class);
Route::get('users-management/permissions/get', [PermissionController::class, 'getPermissions'])->name("get.permissions");
