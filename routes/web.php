<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankCasedataController;
use App\Http\Controllers\CaseData;
use App\Http\Controllers\CaseDataController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardPagesController;
use App\Http\Controllers\DropCollectionController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ModusController;
use App\Http\Controllers\PoliceStationsController;
use App\Http\Controllers\SourceTypeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\EvidenceTypeController;
use App\Http\Controllers\ComplaintGraphController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\MuleAccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\BankReportController;
use App\Http\Controllers\ComplaintStatController;
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
Route::middleware(['auth'])->group(function () {


    //dashboard pages starts here.
    Route::get('/dashboard', [DashboardPagesController::class, 'dashboard'])->name("dashboard");
    // Route::get('filter-case-data', [DashboardPagesController::class, 'filterCaseData'])->name("filter-case-data");


    //users route starts here.
    Route::resource('users', UsersController::class)->middleware('check.permission:User Management');
    //profile
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::get('users-management/users-list/get', [UsersController::class, 'getUsersList'])->name("get.users-list");

    Route::resource('roles', RoleController::class)->middleware('check.permission:Role Management');
    Route::get('users-management/roles-list/get', [RoleController::class, 'getRoles'])->name("get.roles");

    Route::get('/roles/{id}/editPermission', [RoleController::class, 'editPermission'])->name('edit-rolePermission')->middleware('check.permission:Role Management');
    Route::post('/roles/addPermission/{id}', [RoleController::class, 'addPermission'])->name('roles.permission.store')->middleware('check.permission:Role Management');




    Route::resource('modus', ModusController::class);
    Route::get('modus-list/get', [ModusController::class, 'getModus'])->name("get.modus");

    //permmission route starts here.
    Route::resource('permissions', PermissionController::class)->middleware('check.permission:Permission Management');
    Route::get('users-management/permissions/get', [PermissionController::class, 'getPermissions'])->name("get.permissions");

    Route::resource('police_stations', PoliceStationsController::class);
    Route::get('police_stations-list/get', [PoliceStationsController::class, 'getpolice_stations'])->name("get.police_stations");


    //bank case status route starts here.
    Route::get('bank-case-data', [BankCasedataController::class, 'index'])->name("bank-case-data.index")->middleware('check.permission:Upload NCRP Case Data Management,Upload Bank Action');
    Route::post('bank-case-data/store', [BankCasedataController::class, 'store'])->name("bank-case-data.store");

    Route::get('/subpermissions/{id}', [PermissionController::class, 'addSubpermission']);
    Route::post('users-management/subpermissions', [PermissionController::class, 'storeSubPermissions'])->name("subpermissions.store");
    Route::post('users-management/subpermissions/{id}', [PermissionController::class, 'deleteSubPermissions'])->name("subpermissions.destroy");


    Route::resource('police_stations', PoliceStationsController::class);
    Route::get('police_stations-list/get', [PoliceStationsController::class, 'getpolice_stations'])->name("get.police_stations");

    Route::get('import-complaints', [ComplaintController::class, 'importComplaints'])->name("import.complaints")->middleware('check.permission:Upload NCRP Case Data Management,Upload Primary Data');
    Route::post('complaintStore', [ComplaintController::class, 'complaintStore'])->name("complaints.store");

    Route::get('/no-permission', function () {
        return view('no-permission');
    });

    //case data controller starts here.
    Route::get('case-data', [CaseDataController::class, 'index'])->name("case-data.index")->middleware('check.permission:NCRP Case Data Management');
    Route::get('case-data/get-datalist', [CaseDataController::class, 'getDatalist'])->name("get.datalist");
    // Route::post('case-data/post-datalist/filter', [CaseDataController::class, 'getDatalist'])->name("post.datalist-filter");
    Route::get('case-data/get-bank-datalist', [CaseDataController::class, 'getBankDatalist'])->name("get.bank.datalist");
    Route::get('case-data/bank-case-data', [CaseDataController::class, 'bankCaseData'])->name("case.data.bank.case.data")->middleware('check.permission:Upload NCRP Case Data Management,Upload Bank Action');
    Route::get('case-data/details-view', [CaseDataController::class, 'detailsView'])->name("case-data/details-view");
    Route::get('case-data/{id}/view', [CaseDataController::class, 'caseDataView'])->name("case-data.view")->middleware('check.permission:NCRP Case Data Management,Show Detail Page');;
    Route::post('/update-transaction-amount', [CaseDataController::class, 'updateTransactionAmount'])->name('update.transaction.amount');

    //for listing casedata of cyberdomain souurcetype
    Route::get('case-data-others', [CaseDataController::class, 'caseDataOthers'])->name("case-data-others")->middleware('check.permission:Other Case Data Management');
    Route::get('case-data/get-datalist-others', [CaseDataController::class, 'getDatalistOthers'])->name("get.datalist.others");

    Route::post('case-data/edit', [CaseDataController::class, 'editdataList'])->name("edit.datalist");
    Route::get('activateLink', [CaseDataController::class, 'activateLink']);
    Route::GET('assignedTo', [CaseDataController::class, 'AssignedTo']);
    Route::post('/update-complaint-status', [CaseDataController::class, 'updateStatus']);
    Route::GET('assignedToOthers', [CaseDataController::class, 'AssignedToOthers']);
    Route::post('/update-complaint-status-others', [CaseDataController::class, 'updateStatusOthers']);
    Route::get('activateLinkIndividual', [CaseDataController::class, 'activateLinkIndividual']);

    Route::get('activateLinkIndividualOthers', [CaseDataController::class, 'activateLinkIndividualOthers'])->name('activateLinkIndividualOthers');

    //for uploading others case data
    Route::get('upload-others', [CaseDataController::class, 'uploadOthersCaseData'])->name("upload-others-caseData")->middleware('check.permission:Upload Other Case Data Management');

    //for creating and download excel tmplate of other case data upload

    Route::get('template', [CaseDataController::class, 'createDownloadTemplate'])->name("create-download-template");

    // for autogenerating case number in upload others case data
    Route::post('get-casenumber', [CaseDataController::class, 'getCaseNumber'])->name("get.casenumber");

    //for other case data details innerpage
    Route::get('other-case-details/{id}', [CaseDataController::class, 'otherCaseDetails'])->name("other-case-details")->middleware('no-cache');
    Route::get('edit-others-caseData/{id}', [CaseDataController::class, 'editotherCaseDetails'])->name("edit-others-caseData");
    Route::put('others-caseData-update/{id}', [CaseDataController::class, 'updateotherCaseDetails'])->name("case-data-others.update");

    //for casenumber auto generate
    Route::put('others-caseData-update/{id}', [CaseDataController::class, 'updateotherCaseDetails'])->name("case-data-others.update");

    //collection drop controller
    Route::get('drop-collection', [DropCollectionController::class, 'dropCollection']);


    Route::resource('sourcetype', SourceTypeController::class)->middleware('check.permission:Source Type Management');
    Route::get('users-management/sourcetype-list/get', [SourceTypeController::class, 'getsourcetype'])->name("get.sourcetype");
    Route::get('upload-registrar', [SourceTypeController::class, 'uploadRegistrar'])->name("upload-registrar")->middleware('check.permission:Source Type Management,Upload registrar');
    Route::post('registrarStore', [SourceTypeController::class, 'registrarStore'])->name("registrar.store");

//dashboard graph
Route::get('/complaints/chart', [ComplaintGraphController::class,'chartData'])->name('complaints.chart');



    Route::get('reports', [ReportsController::class, 'index'])->name("reports.index")->middleware('check.permission:Reports Management');
    // Route::get('get-datalist-ncrp', [ReportsController::class, 'getDatalistNcrp'])->name("get.datalist.ncrp");
    Route::get('get-datalist-ncrp', [ReportsController::class, 'getDatalistNcrp'])->name("get.datalist.ncrp");
    Route::get('get-datalist-othersourcetype', [ReportsController::class, 'getDatalistOthersourcetype'])->name("get.datalist.othersourcetype");

    Route::resource('evidencetype', EvidenceTypeController::class)->middleware('check.permission:Evidence Type Management');
    Route::get('evidencetype-list/get', [EvidenceTypeController::class, 'getevidencetype'])->name("get.evidencetype");

    Route::resource('profession', ProfessionController::class)->middleware('check.permission:Source Type Management,Add Profession');
    Route::get('profession-list/get', [ProfessionController::class, 'getprofession'])->name("get.profession");


//evidence
    Route::resource('evidence', EvidenceController::class);
    Route::get('bank-case-data/evidence/create/{acknowledgement_no}', [EvidenceController::class, 'create'])->name('evidence.create');
    Route::post('/evidence', [EvidenceController::class, 'store'])->name('evidence.store');
    Route::delete('/evidence/{id}', [EvidenceController::class, 'destroy'])->name('evidence.destroy');
    Route::get('evidence/index/{acknowledgement_no}', [EvidenceController::class, 'index'])->name('evidence.index');

    Route::post('fir-upload', [CaseDataController::class, 'firUpload'])->name('fir_file.upload');
    Route::get('download-fir/{ak_no}', [CaseDataController::class, 'downloadFIR'])->name('download.fir');
    Route::post('profile-update', [CaseDataController::class, 'profileUpdate'])->name('profile.update');

    //evidence management

    Route::get('evidence.management', [EvidenceController::class, 'evidenceManagement'])->name('evidence.management')->middleware('check.permission:Evidence Management');
    Route::get('evidence.ncrp', [EvidenceController::class, 'evidenceNcrp'])->name('get.evidence.ncrp');
    Route::get('evidence.others', [EvidenceController::class, 'evidenceOthers'])->name('get.evidence.others');


    //notice module

    Route::get('notice', [NoticeController::class,'againstEvidence'])->name('notice.evidence')->middleware('check.permission:Notice Management,Against Evidence Permission');

    Route::get('evidence-list-notice', [NoticeController::class,'evidenceListNotice'])->name('get_evidence_list_notice');

    //mule account
    Route::get('muleaccount', [MuleAccountController::class,'Muleaccount'])->name('muleaccount')->middleware('check.permission:Mule Account Management');
    Route::get('muleaccount-list', [MuleAccountController::class,'muleaccountList'])->name('get_muleaccount_list');

    // category module
    Route::resource('category', CategoryController::class)->middleware('check.permission:Source Type Management');
    Route::get('get-categories', [CategoryController::class,'getCategories'])->name('get.categories');
    Route::post('add-category', [CategoryController::class,'addCategory'])->name('add.category');

    //modus module
    Route::resource('modus', ModusController::class);
    Route::get('get-modus', [ModusController::class,'getModus'])->name('get.modus');
    Route::post('add-modus', [ModusController::class,'addModus'])->name('add.modus');

    //Sub CAtegory module

    Route::resource('subcategory', SubCategoryController::class)->middleware('check.permission:Source Type Management,Add Subcategory');
    Route::get('get-subcategories', [SubCategoryController::class,'getSubCategories'])->name('get.subcategories');
    Route::post('add-subcategory', [SubCategoryController::class,'addSubCategory'])->name('add.subcategory')->middleware('check.permission:Source Type Management,Add Subcategory');

    // Mail Merge
    Route::get('/get-mailmerge-list/{ack_no}', [MailController::class, 'mailMergeList'])->name('get-mailmerge-list')->middleware('check.permission:Evidence Management,Show NCRP mail Merge');
    Route::get('get-mailmergelist-ncrp', [MailController::class, 'getMailmergeListNcrp'])->name("get.mailmergelist.ncrp");
    // Route::get('/get-mailmerge-preview', [MailController::class, 'mailMergePreview'])->name('get-mailmerge-preview');

    Route::get('/get-mailmerge-listother/{case_number}', [MailController::class, 'mailMergeListOther'])->name('get-mailmerge-listother')->middleware('check.permission:Evidence Management,Show Other mail Merge');
    Route::get('get-mailmergelist-other', [MailController::class, 'getMailmergeListOther'])->name("get.mailmergelist.other");
    // Route::get('/get-mailmerge-previewOther/{evidence_type}/{option}/{case_no}', [MailController::class, 'mailMergePreviewOther'])->name('get-mailmerge-previewOther');


    Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send-email');

    // url status recheck in evidence management

    Route::get('/status-recheck',[EvidenceController::class, 'statusRecheck'])->name('url_status_recheck');
    Route::get('/url-status',[EvidenceController::class, 'urlStatus'])->name('get_url_status');


    Route::post('/update-reported-status/{id}', [EvidenceController::class, 'updateReportedStatus']);
    Route::post('/update-reported-statusother/{id}', [EvidenceController::class, 'updateReportedStatusOther']);

    //evidence bulk upload ncrp
    Route::get('evidence/bulkUpload/{acknowledgement_no}', [EvidenceController::class, 'evidenceBulkUpload'])->name('evidence.bulkUpload');
    Route::post('evidence/bulk-upload',[EvidenceController::class, 'evidenceBulkUploadFile'])->name('evidence.bulk-upload');

    Route::get('/get-portal-link/{registrar}', [MailController::class, 'getPortalLink'])->name('get-portal-link');
    Route::post('/update-portal-count', [MailController::class, 'updatePortalCount'])->name('update.portal.count');

    Route::post('/evidence/store', [EvidenceController::class, 'storeEvidence'])->name('evidenceStore');
    Route::post('/generate/notice', [NoticeController::class, 'generateNotice'])->name('generate.notice');
    Route::get('/notices', [NoticeController::class, 'Notices'])->name('notices.index');
    Route::get('/notices/{id}', [NoticeController::class, 'showNotice'])->name('notices.show');
    Route::get('/notices/{id}/edit', [NoticeController::class, 'editNoticeView'])->name('notices.edit');
    Route::put('/notices/{id}', [NoticeController::class, 'updateNotice'])->name('notices.update');
    Route::post('/notices/{id}/follow', [NoticeController::class, 'follow'])->name('notices.follow');







//Route for bank Reports
Route::get('/bank-daily-reports', [BankReportController::class, 'index'])->name('bank-daily-reports.index');
Route::get('/bank-reports', [BankReportController::class, 'getBankDetailsByDate'])->name('bank-daily-reports.index');

Route::get('/above-one-lakh', [BankReportController::class, 'aboveIndex'])->name('above-one-lakh');
Route::get('/above-report-data', [BankReportController::class, 'getAboveData'])->name('aboveReport');


});
Route::get('/complaint-stats', [ComplaintStatController::class, 'getComplaintStats'])->name('complaint.stats');
Route::get('/complaint-filters', [ComplaintStatController::class, 'getAvailableFilters'])->name('complaint.filters');
// Route::get('/complaint-stats', [ComplaintStatController::class, 'getComplaintStats']);
//Route::post('/validate-otp',[AuthController::class,'validateOtp'])->name('validate.otp')->middleware('auth');
//Route::get('/verfiy-otp',[AuthController::class, 'verifyOtp'])->name('verify-otp')->middleware('auth');

// mule account notice
Route::get('mule-notice', [NoticeController::class,'againstMuleAccount'])->name('notice.mule.account');
Route::post('/generate/mule/notice', [NoticeController::class, 'generateMuleNotice'])->name('generate.mule.notice');






