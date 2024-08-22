<?php

namespace App\Http\Controllers;
use App\Models\SourceType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\RegistrarImport;

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
            return Redirect::back()->withInput()->withErrors($validate);
        }

        SourceType::create([
            'name' => @$request->name? $request->name:'',
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
         // Validate the incoming request data
         $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            // Add more validation rules as needed
        ]);

        // Find the role by its ID.
        $data = SourceType::findOrFail($id);

        // Update the role with the data from the request
        $data->name = $request->name;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated role
        $data->save();

        // Redirect back with success message
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



}
