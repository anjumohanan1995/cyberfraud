<?php

namespace App\Http\Controllers;
use App\Models\EvidenceType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class EvidenceTypeController extends Controller
{
    public function index()
    {
        return view("dashboard.evidencetype.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view("dashboard.evidencetype.create");
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

        $name = strtolower($request->name);

        // Check if the name already exists in the database
        if (EvidenceType::where('deleted_at', null)->where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => 'This evidence type name already exists.']], 422);
        }

        EvidenceType::create([
            'name' => $name,
            'status' => $request->input('status'),
        ]);

        return redirect()->route('evidencetype.index')->with('success','Evidence Type Added successfully.');


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
        $data = EvidenceType::findOrFail($id);


        return view('dashboard.evidencetype.edit', ['data' => $data,]);
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

        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        // Find the role by its ID.
        $data = EvidenceType::findOrFail($id);

        $newName = strtolower($request->name);

        // Check if the new name already exists for a different record
        if (EvidenceType::where('deleted_at', null)->where('name', $newName)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['name' => 'This evidence type name already exists.']);
        }

        // Update the evidence type with the data from the request
        $data->name = $newName;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated evidence type
        $data->save();

        // Redirect back with success message
        return redirect()->route('evidencetype.index')->with('success', 'Evidence Type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = EvidenceType::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Evidence Type successfully deleted!']);
    }

    public function getevidencetype(Request $request)
{
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

    // Build query
    $query = EvidenceType::where('deleted_at', null);

    // Apply search filter
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('name', 'like', '%' . $searchValue . '%');
        });
    }

    // Total records
    $totalRecords = EvidenceType::where('deleted_at', null)->count();

    // Total records with filter
    $totalRecordswithFilter = $query->count();

    // Fetch records with sorting, filtering, and pagination
    $records = $query
        ->orderBy($columnName, $columnSortOrder)
                        ->orderBy('created_at', 'desc') // Sort by created_at as secondary order

                     ->skip($start)
                     ->take($rowperpage)
                     ->get();

    // Prepare data for response
    $data_arr = [];
    $i = $start;

    foreach ($records as $record) {
        $i++;
        $id = $record->id;
        $name = $record->name;

        $edit = '<a href="' . url('evidencetype/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';

        $data_arr[] = [
            "id" => $i,
            "name" => $name,
            "edit" => $edit
        ];
    }

    // Prepare the response
    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
    ];

    return response()->json($response);
}



    // public function getevidencetype(Request $request)
    // {

    //     ## Read value
    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length"); // Rows display per page

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     $searchValue = $search_arr['value']; // Search value

    //     $query = EvidenceType::where('deleted_at', null);

    //     // Apply search filter
    //     if (!empty($searchValue)) {
    //         $query->where(function ($q) use ($searchValue) {
    //             $q->where('name', 'like', '%' . $searchValue . '%');
    //         });
    //     }
    //         // Total records
    //     $totalRecord = EvidenceType::where('deleted_at',null)->orderBy('created_at','desc');
    //     $totalRecords = $totalRecord->select('count(*) as allcount')->count();

    //      $totalRecordswithFilte = EvidenceType::where('deleted_at',null)->orderBy('created_at','desc');
    //     $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

    //         // Fetch records
    //     $items = EvidenceType::where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);
    //     $records = $items->skip($start)->take($rowperpage)->get();

    //     $data_arr = array();
    //     $i=$start;

    //     foreach($records as $record){
    //         $i++;
    //         $id = $record->id;
    //         $name = $record->name;

    //         $edit = '<a  href="' . url('evidencetype/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';

    //         $data_arr[] = array(
    //             "id" => $i,
    //             "name" => $name,
    //             "edit" => $edit
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr
    //     );

    //     return response()->json($response);
    // }

}
