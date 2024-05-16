<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankCasedataController;
use App\Http\Controllers\CaseData;
use App\Http\Controllers\CaseDataController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardPagesController;
use App\Http\Controllers\DropCollectionController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModusController;
use App\Http\Controllers\PoliceStationsController;
use App\Http\Controllers\SourceTypeController;
use App\Models\BankCasedata;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use MongoDB\Operation\DropCollection;

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

//logout route starts here.
Route::get('logout', [LogoutController::class, 'logout']);

//password reset
Route::get('forgot-password', [LogoutController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [LogoutController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset--password/{token}', [LogoutController::class, 'resetPassword'])->name('reset.password');
Route::get('reset-password-view', [LogoutController::class, 'resetPasswordView'])->name('reset.password.view');
Route::post('password-update', [LogoutController::class, 'passwordUpdate'])->name('password.update');



// used default middlewire for authentication.
Route::middleware('auth')->group(function () {



    //dashboard pages starts here.
    Route::get('/dashboard', [DashboardPagesController::class, 'dashboard'])->name("dashboard");


    //users route starts here.
    Route::resource('users', UsersController::class);
    //profile
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::get('users-management/users-list/get', [UsersController::class, 'getUsersList'])->name("get.users-list");

    Route::resource('roles', RoleController::class);
    Route::get('users-management/roles-list/get', [RoleController::class, 'getRoles'])->name("get.roles");

    Route::get('/roles/{id}/editPermission', [RoleController::class, 'editPermission'])->name('edit-rolePermission');
    Route::post('/roles/addPermission/{id}', [RoleController::class, 'addPermission'])->name('roles.permission.store');




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

    Route::get('/subpermissions/{id}', [PermissionController::class, 'addSubpermission']);
    Route::post('users-management/subpermissions', [PermissionController::class, 'storeSubPermissions'])->name("subpermissions.store");
    Route::post('users-management/subpermissions/{id}', [PermissionController::class, 'deleteSubPermissions'])->name("subpermissions.destroy");


    Route::resource('police_stations', PoliceStationsController::class);
    Route::get('police_stations-list/get', [PoliceStationsController::class, 'getpolice_stations'])->name("get.police_stations");

    Route::get('import-complaints', [ComplaintController::class, 'importComplaints'])->name("import.complaints");
    Route::post('complaintStore', [ComplaintController::class, 'complaintStore'])->name("complaints.store");


    //case data controller starts here.
    Route::get('case-data', [CaseDataController::class, 'index'])->name("case-data.index");
    Route::get('case-data/get-datalist', [CaseDataController::class, 'getDatalist'])->name("get.datalist");
    // Route::post('case-data/post-datalist/filter', [CaseDataController::class, 'getDatalist'])->name("post.datalist-filter");
    Route::get('case-data/get-bank-datalist', [CaseDataController::class, 'getBankDatalist'])->name("get.bank.datalist");
    Route::get('case-data/bank-case-data', [CaseDataController::class, 'bankCaseData'])->name("case.data.bank.case.data");


    //collection drop controller
    Route::get('drop-collection', [DropCollectionController::class, 'dropCollection']);
<<<<<<< HEAD


    Route::resource('sourcetype', SourceTypeController::class);
    Route::get('users-management/sourcetype-list/get', [SourceTypeController::class, 'getsourcetype'])->name("get.sourcetype");






=======
>>>>>>> e670293bfb6af1de83721f663e8f8f2594e4dca1
});
