<?php

namespace App\Http\Controllers;
use App\Models\Profession;
use App\Models\RolePermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
            return response()->json(['errors' => $validate->errors()], 422);
        }


        $name = strtoupper($request->name);

        // Check if the name already exists in the database
        if (Profession::where('deleted_at', null)->where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => 'This profession name already exists.']], 422);
        }

        Profession::create([
            'name' => $name,
            'status' => $request->input('status'),
        ]);


        // return redirect()->route('profession.index')->with('success','Profession Added successfully.');
        return response()->json(['success' => 'Profession Added successfully.']);


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

        // if ($validate->fails()) {
        //     //dd($validate);
        //     return Redirect::back()->withInput()->withErrors($validate);
        // }


        // Find the role by its ID.
        $data = Profession::findOrFail($id);

        $newName = strtoupper($request->name);

        // Check if the new name already exists for a different record
        if (Profession::where('deleted_at', null)->where('name', $newName)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['name' => 'This profession name already exists.']);
        }

        // Update the evidence type with the data from the request
        $data->name = $newName;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated evidence type
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

    $user = Auth::user();
    $role = $user->role;
    $permission = RolePermission::where('role', $role)->first();
    $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
    $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

    $hasDeleteProfessionPermission = $sub_permissions && in_array('Delete Profession', $sub_permissions) || $user->role == 'Super Admin';

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


        // Fetch records with filter and sorting
    $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
        ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
        ->skip($start)
        ->take($rowperpage)
        ->get();


    $data_arr = array();
    $i = $start;

    foreach ($records as $record) {
        $i++;
        $id = $record->id;
        $name = $record->name;

        $edit = '<a href="' . url('profession/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
        if ($hasDeleteProfessionPermission) {
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



}

