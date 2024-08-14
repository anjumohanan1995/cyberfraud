<?php

namespace App\Http\Controllers;
use App\Models\Profession;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfessionController extends Controller
{
    public function index()
    {
        return view("dashboard.profession.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view("dashboard.profession.create");
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

        Profession::create([
            'name' => @$request->name? $request->name:'',
            'status' => $request->input('status'),

        ]);

        return redirect()->route('profession.index')->with('success','Profession Added successfully.');


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
        $data = Profession::findOrFail($id);


        return view('dashboard.profession.edit', ['data' => $data,]);
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
        $data = Profession::findOrFail($id);

        // Update the role with the data from the request
        $data->name = $request->name;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated role
        $data->save();

        // Redirect back with success message
        return redirect()->route('profession.index')->with('success', 'Profession updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Profession::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Profession successfully deleted!']);
    }



    public function getprofession(Request $request)
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

    // Initialize query
    $query = Profession::where('deleted_at', null);

    // Apply search filter
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('name', 'like', '%' . $searchValue . '%');
        });
    }

    // Total records without filter
    $totalRecords = Profession::where('deleted_at', null)->count();

    // Total records with filter
    $totalRecordswithFilter = $query->count();

    // Fetch records with filter
    $records = $query->orderBy('created_at','desc')
        ->skip($start)
        ->take($rowperpage)
        ->get();

    $data_arr = array();
    $i = $start;

    foreach ($records as $record) {
        $i++;
        $id = $record->id;
        $name = $record->name;

        $edit = '<a href="' . url('profession/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';

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



}

