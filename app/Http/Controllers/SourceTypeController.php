<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\Bank;
use App\Models\Insurance;
use App\Models\Merchant;
use App\Models\Wallet;
use App\Models\RolePermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\RegistrarImport;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use MongoDB\BSON\Regex;
use Illuminate\Support\Facades\Auth;

class SourceTypeController extends Controller
{
    public function index()
    {
        return view("dashboard.user-management.sourcetype.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view("dashboard.user-management.sourcetype.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $validate = Validator::make($request->all(),
        [
          'name' => 'required',
          'status' => 'required|in:active,inactive',



        ]);

        if ($validate->fails()) {
            //dd($validate);
            return response()->json(['errors' => $validate->errors()], 422);
        }


        $name = strtoupper($request->name);

        // Check if the name already exists in the database
        if (SourceType::where('deleted_at', null)->where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => 'This source type name already exists.']], 422);
        }

        SourceType::create([
            'name' => $name,
            'status' => $request->input('status'),
        ]);

        // Redirect back with a success message
        return response()->json(['success' => 'Source Type Added successfully.']);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = SourceType::findOrFail($id);


        return view('dashboard.user-management.sourcetype.edit', ['data' => $data,]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);


        // Find the role by its ID.
        $data = SourceType::findOrFail($id);

        $newName = strtoupper($request->name);

        // Check if the new name already exists for a different record
        if (SourceType::where('deleted_at', null)->where('name', $newName)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['name' => 'This source type name already exists.']);
        }

        // Update the evidence type with the data from the request
        $data->name = $newName;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated evidence type
        $data->save();

        return redirect()->route('sourcetype.create')->with('success', 'Source Type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = SourceType::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Source Type successfully deleted!']);
    }



    public function getsourcetype(Request $request)
    {

        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteSTPermission = $sub_permissions && in_array('Delete Source Type', $sub_permissions) || $user->role == 'Super Admin';

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value


        $query = SourceType::where('deleted_at', null);

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%');
            });
        }

            // // Total records
            // $totalRecord = SourceType::where('deleted_at',null)->orderBy('created_at','desc');
            // $totalRecords = $totalRecord->select('count(*) as allcount')->count();


            // $totalRecordswithFilte = SourceType::where('deleted_at',null)->orderBy('created_at','desc');
            // $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

            // // Fetch records
            // $items = SourceType::where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);
            // $records = $items->skip($start)->take($rowperpage)->get();

            $totalRecords = SourceType::where('deleted_at', null)->count();

        // Total records with filter
        $totalRecordswithFilter = $query->count();

    // Fetch records with filter and sorting
    $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
        ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
        ->skip($start)
        ->take($rowperpage)
        ->get();

            $data_arr = array();
            $i=$start;

            foreach($records as $record){
                $i++;
                $id = $record->id;
                $name = $record->name;

                // $edit = '<a  href="' . url('sourcetype/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
                $edit = '<a href="' . url('sourcetype/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';

                if ($hasDeleteSTPermission) {
                    $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
                }

                $data_arr[] = array(
                    "id" => $i,
                    "name" => $name,

                    "edit" => $edit
                );
            }

            $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
            );

            return response()->json($response);
    }

    public function uploadRegistrar(){
        return view("import_registrar");
    }

    public function registrarStore(Request $request)
    {
        // dd($request->registrar_number);
        $file = $request->file('registrar_file');
        // dd($file);
        $request->validate([
            // 'registrar_number' => 'required|unique:registrar_data',
            'registrar_file' => 'required|mimes:xls,xlsx,csv'
        ]);



        if ($file) {
            try {
                // dd('hi');
                // Import data from the file
                Excel::import(new RegistrarImport(), $file);

                // Provide feedback to the user
                return redirect()->back()->with('success', 'Form submitted successfully!');
            } catch (ValidationException $e) {
                // dd("1");
                return redirect()->back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {

                // return redirect()->back()->withErrors($e->errors())->withInput();
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    // dd($e);
                    // Retrieve the validation errors
                    $errors = $e->validator->getMessageBag()->all();
                    // dd($errors);

                    // Redirect back with validation errors and input data
                    return redirect()->back()->withErrors($errors)->withInput();
                } else {
                    // dd("3");
                    // Handle other exceptions
                    return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                }

                // return response()->json([
                //     'error' => 'An error occurred during import',
                //     'message' => $e->getMessage()
                // ], 500);
            }
        } else {
            // No file uploaded
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }

    public function bankCreate()
    {
        return view("dashboard.user-management.bank.create");
    }


    public function getbank(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteBankPermission = $sub_permissions && in_array('Delete Bank', $sub_permissions) || $user->role == 'Super Admin';

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];


        $query = Bank::where('deleted_at', null);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('bank', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = Bank::where('deleted_at', null)->count();

        $totalRecordswithFilter = $query->count();

        $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
            ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->id;
            $name = $record->bank;
            $edit = '<a  href="' . url('bank/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
            if ($hasDeleteBankPermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }


            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "edit" => $edit
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    public function bankstore(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:banks,bank',
            'status' => 'required|in:active,inactive',
        ]);

        // if ($validate->fails()) {
        //     return response()->json([
        //         'errors' => $validate->errors()
        //     ], 422);
        // }

        // Convert the bank name to lowercase for case-insensitive comparison
        $existingBank = Bank::where('deleted_at', null)->where('bank', 'regex', new \MongoDB\BSON\Regex('^' . preg_quote($request->name) . '$', 'i'))->first();
        // dd(existingBank);
        if ($existingBank) {
            return response()->json([
                'errors' => ['name' => ['The bank already exists.']]
            ], 422);
        }

        Bank::create([
            'bank' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        // return response()->json([
        //     'success' => 'Bank Added Successfully.'
        // ]);
        return response()->json(['success' => 'Bank Added successfully!']);
    }

    public function bankedit($id)
    {
        $data = Bank::findOrFail($id);
        return view('dashboard.user-management.bank.edit', ['data' => $data,]);
    }

    public function bankupdate(Request $request, $id)
    {
        // Validate basic input fields
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Case-insensitive uniqueness check, ignoring the current record
        $existingBank = Bank::where('deleted_at', null)->where('_id', '!=', $id)
            ->where('bank', 'regex', new Regex('^' . preg_quote($request->name) . '$', 'i'))
            ->first();

        if ($existingBank) {
            return redirect()->back()->withErrors(['name' => 'The bank already exists.'])->withInput();
        }

        // Find the bank by ID
        $data = Bank::findOrFail($id);

        // Update bank fields
        $data->bank = $request->name;
        $data->status = $request->status;

        // Save updated data
        $data->save();

        return redirect()->route('bank.create')->with('success', 'Bank updated successfully!');
    }

    public function destroybank($id)
    {
        $data = Bank::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Bank successfully deleted!']);
    }


    public function insuranceCreate()
    {
        return view("dashboard.user-management.insurance.create");
    }

    public function getinsurance(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteInsurancePermission = $sub_permissions && in_array('Delete Insurance', $sub_permissions) || $user->role == 'Super Admin';

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $query = Insurance::where('deleted_at', null);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('insurance', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = Insurance::where('deleted_at', null)->count();

        $totalRecordswithFilter = $query->count();

        $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
            ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->id;
            $name = $record->insurance;
            $edit = '<a  href="' . url('insurance/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';

            if ($hasDeleteInsurancePermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }
            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "edit" => $edit
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    public function insurancestore(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existinginsurance = Insurance::where('deleted_at', null)->where('insurance', new \MongoDB\BSON\Regex('^' . preg_quote($request->name) . '$', 'i'))->first();
        if ($existinginsurance) {
            return response()->json([
                'errors' => ['name' => ['The Insurance already exists.']]
            ], 422);
        }

        Insurance::create([
            'insurance' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => 'Insurance Added Successfully.'
        ]);
    }

    public function insuranceedit($id)
    {
        $data = Insurance::findOrFail($id);
        return view('dashboard.user-management.insurance.edit', ['data' => $data,]);
    }

    public function insuranceupdate(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Rule::unique('insurances', 'insurance')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $existingInsurance = Insurance::where('deleted_at', null)->where('_id', '!=', $id)
            ->where('insurance', 'regex', new Regex('^' . preg_quote($request->name) . '$', 'i'))
            ->first();

        if ($existingInsurance) {
            return redirect()->back()->withErrors(['name' => 'The insurance already exists.'])->withInput();
        }


        $data = Insurance::findOrFail($id);

        $data->insurance = $request->name;
        $data->status = $request->status;

        $data->save();

        return redirect()->route('insurance.create')->with('success', 'Insurance updated successfully!');
    }

    public function destroyinsurance($id)
    {
        $data = Insurance::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Insurance successfully deleted!']);
    }

    public function merchantCreate()
    {
        return view("dashboard.user-management.merchant.create");
    }

    public function getmerchant(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteMerchantPermission = $sub_permissions && in_array('Delete Merchant', $sub_permissions) || $user->role == 'Super Admin';

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $query = Merchant::where('deleted_at', null);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('merchant', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = Merchant::where('deleted_at', null)->count();

        $totalRecordswithFilter = $query->count();

        $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
            ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->id;
            $name = $record->merchant;
            $edit = '<a  href="' . url('merchant/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';

            if ($hasDeleteMerchantPermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }

            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "edit" => $edit
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    public function merchantstore(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existingMerchant = Merchant::where('deleted_at', null)->where('merchant', 'regex', new \MongoDB\BSON\Regex('^' . preg_quote($request->name) . '$', 'i'))->first();
        // $existingBank = Bank::where('bank', 'regex', new \MongoDB\BSON\Regex('^' . preg_quote($request->name) . '$', 'i'))->first();

        if ($existingMerchant) {
            return response()->json([
                'errors' => ['name' => ['The Merchant already exists.']]
            ], 422);
        }

        Merchant::create([
            'merchant' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => 'Merchant Added Successfully.'
        ]);
    }

    public function merchantedit($id)
    {
        $data = Merchant::findOrFail($id);
        return view('dashboard.user-management.merchant.edit', ['data' => $data,]);
    }

    public function merchantupdate(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Rule::unique('merchants', 'merchant')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $existingMerchant = Merchant::where('deleted_at', null)->where('_id', '!=', $id)
            ->where('merchant', 'regex', new Regex('^' . preg_quote($request->name) . '$', 'i'))
            ->first();

        if ($existingMerchant) {
            return redirect()->back()->withErrors(['name' => 'The Merchant already exists.'])->withInput();
        }

        $data = Merchant::findOrFail($id);

        $data->merchant = $request->name;
        $data->status = $request->status;

        $data->save();

        return redirect()->route('merchant.create')->with('success', 'Merchant updated successfully!');
    }

    public function destroymerchant($id)
    {
        $data = Merchant::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Merchant successfully deleted!']);
    }

    public function WalletCreate()
    {
        return view("dashboard.user-management.wallet.create");
    }

    public function getwallet(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteWalletPermission = $sub_permissions && in_array('Delete Wallet', $sub_permissions) || $user->role == 'Super Admin';

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $query = Wallet::where('deleted_at', null);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('wallet', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = Wallet::where('deleted_at', null)->count();

        $totalRecordswithFilter = $query->count();

        $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
            ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->_id;
            $name = $record->wallet;
            $edit = '<a  href="' . url('wallet/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';

            if ($hasDeleteWalletPermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }

            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "edit" => $edit
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    public function walletstore(Request $request)
{
    // Validate the input
    $validate = Validator::make($request->all(), [
        'name' => 'required',
        'status' => 'required|in:active,inactive',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'errors' => $validate->errors()
        ], 422);
    }

    // Check for existing wallet that is not soft-deleted
    $existingWallet = Wallet::whereNull('deleted_at')
        ->where('wallet', 'regex', new \MongoDB\BSON\Regex('^' . preg_quote($request->name) . '$', 'i'))
        ->first();

    if ($existingWallet) {
        return response()->json([
            'errors' => ['name' => ['The wallet name has already been taken.']]
        ], 422);
    }

    // Create new wallet entry
    Wallet::create([
        'wallet' => $request->input('name'),
        'status' => $request->input('status'),
    ]);

    return response()->json([
        'success' => 'Wallet added successfully.'
    ]);
    // return redirect()->route('wallet.create')->with('success', 'Wallet added successfully!');



}



    public function walletedit($id)
    {
        $data = Wallet::findOrFail($id);
        return view('dashboard.user-management.wallet.edit', ['data' => $data,]);
    }

    public function walletupdate(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Rule::unique('wallets', 'wallet')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $existingWallet = Wallet::where('deleted_at', null)->where('_id', '!=', $id)
            ->where('wallet', 'regex', new Regex('^' . preg_quote($request->name) . '$', 'i'))
            ->first();

        if ($existingWallet) {
            return redirect()->back()->withErrors(['name' => 'The Wallet already exists.'])->withInput();
        }

        $data = Wallet::findOrFail($id);

        $data->wallet = $request->name;
        $data->status = $request->status;

        $data->save();

        return redirect()->route('wallet.create')->with('success', 'Wallet updated successfully!');
    }

    public function destroywallet($id)
    {
        $data = Wallet::findOrFail($id);
        $data->delete();
        return response()->json(['success' => 'Wallet successfully deleted!']);
    }


}
