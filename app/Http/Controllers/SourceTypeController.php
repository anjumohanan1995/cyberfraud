<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\Bank;
use App\Models\Insurance;
use App\Models\Merchant;
use App\Models\Wallet;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\RegistrarImport;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;


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


        return redirect()->route('sourcetype.create')->with('success','Source Type Added successfully.');


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

                $edit = '<a  href="' . url('sourcetype/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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
            $edit = '<a  href="' . url('bank/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existingBank = Bank::where('bank', $request->name)->first();
        if ($existingBank) {
            return response()->json([
                'errors' => ['name' => ['The bank already exists.']]
            ], 422);
        }

        Bank::create([
            'bank' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => 'Bank Added Successfully.'
        ]);
    }

    public function bankedit($id)
    {
        $data = Bank::findOrFail($id);
        return view('dashboard.user-management.bank.edit', ['data' => $data,]);
    }

    public function bankupdate(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banks', 'bank')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $data = Bank::findOrFail($id);

        $data->bank = $request->name;
        $data->status = $request->status;

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
            $edit = '<a  href="' . url('insurance/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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
            'name' => 'required|unique:insurances,insurance',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existinginsurance = Insurance::where('insurance', $request->name)->first();
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
                Rule::unique('insurances', 'insurance')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

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
        return view("dashboard.user-management.Merchant.create");
    }

    public function getmerchant(Request $request)
    {
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
            $edit = '<a  href="' . url('merchant/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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
            'name' => 'required|unique:merchants,merchant',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existingMerchant = Merchant::where('Merchant', $request->name)->first();
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
        return view('dashboard.user-management.Merchant.edit', ['data' => $data,]);
    }

    public function merchantupdate(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('merchants', 'merchant')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

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
            $id = $record->id;
            $name = $record->wallet;
            $edit = '<a  href="' . url('wallet/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:wallets,wallet',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $existingwallet = Wallet::where('wallet', $request->name)->first();
        if ($existingwallet) {
            return response()->json([
                'errors' => ['name' => ['The wallet already exists.']]
            ], 422);
        }

        wallet::create([
            'wallet' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => 'Wallet Added Successfully.'
        ]);
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
                Rule::unique('wallets', 'wallet')->ignore($id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

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
