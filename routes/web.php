<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankCasedataController;
use App\Http\Controllers\DashboardPagesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModusController;
use App\Http\Controllers\PoliceStationsController;
use App\Models\BankCasedata;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

    // User::create([
    //     'name' => 'Super Admin',
    //     'email' => 'superadmin@email.com',
    //     'password' => Hash::make('12345678'), // You should hash the password for security
    //     'role' => 'super admin', // Assuming 1 is the role ID for super admin
    // ]);
    return view('login.login');
});



//login route starts here
Route::post('/login', [AuthController::class, 'login'])->name("login");

//dashboard pages starts here.
Route::get('/dashboard', [DashboardPagesController::class, 'dashboard'])->name("dashboard");

//users route starts here.
Route::resource('users', UsersController::class);
Route::get('users-management/users-list/get', [UsersController::class, 'getUsersList'])->name("get.users-list");

Route::resource('roles', RoleController::class);
Route::get('users-management/roles-list/get', [RoleController::class, 'getRoles'])->name("get.roles");

Route::resource('modus', ModusController::class);
Route::get('modus-list/get', [ModusController::class, 'getModus'])->name("get.modus");

//permmission route starts here.
Route::resource('permissions', PermissionController::class);
Route::get('users-management/permissions/get', [PermissionController::class, 'getPermissions'])->name("get.permissions");

Route::resource('police_stations', PoliceStationsController::class);
Route::get('police_stations-list/get', [PoliceStationsController::class, 'getpolice_stations'])->name("get.police_stations");


//bank case status route starts here.
Route::get('bank-case-data', [BankCasedataController::class, 'index'])->name("bank-case-data.index");
Route::post('bank-case-data/store', [BankCasedataController::class, 'store'])->name("bank-case-data.store");

