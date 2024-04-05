<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        return view("dashboard.user-management.permissions.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('dashboard.user-management.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Example validation rules for the name field
        ]);

        // Create a new Permission instance
        $permission = new Permission();
        $permission->name = $request->name;

        // Save the model instance
        $permission->save();


        // For demonstration purposes, let's just return a success message
        return redirect()->route('permissions.index')->with('success', 'Form submitted successfully!');
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
        $permission = Permission::findOrFail($id);


        return view('dashboard.user-management.permissions.edit', ['permission' => $permission]);
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
            // Add more validation rules as needed
        ]);

        // Find the permission by its ID.
        $permission = Permission::findOrFail($id);

        // Update the permission with the data from the request
        $permission->name = $request->name;
        // Update other attributes as needed

        // Save the updated permission
        $permission->save();

        // Redirect back with success message
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return back()->with('success', 'Permission successfully deleted!');
    }
    public function getPermissions(Request $request)
    {
    //    // Fetch permissions from the database with pagination
    //    $permissions = Permission::paginate(2); // Assuming you want 10 permissions per page

    //    // Return permissions data along with pagination links in JSON format
    //    return response()->json([
    //        'data' => $permissions->items(), // Permissions data for the current page
    //        'links' => $permissions->links()->toHtml(), // Pagination links HTML
    //    ]);


       # Read value
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

           // Total records
           $totalRecord = Permission::where('deleted_at',null)->orderBy('created_at','desc');
           $totalRecords = $totalRecord->select('count(*) as allcount')->count();


           $totalRecordswithFilte = Permission::where('deleted_at',null)->orderBy('created_at','desc');
           $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

           // Fetch records
           $items = Permission::where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);
           $records = $items->skip($start)->take($rowperpage)->get();
   
           $data_arr = array();
           $i=$start;

           foreach($records as $record){
               $i++;
               $id = $record->id;
               $name = $record->name;
               
               $edit = '<a  href="' . url('permissions/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>&nbsp;&nbsp;<a  href="' . url('subpermissions/'.$id) . '" class="btn btn-primary edit-btn">Sub Permission</a>';

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

    public function addSubpermission(Request $request,$id)
    {

        $subpermission= Permission::where('_id',$id)->first();   
        //dd($subpermission);
        $jsonString= $subpermission->sub_permission;
       // dd($jsonString);
      $subpermissions =json_decode($jsonString, true); 
        //$subpermissions=$subpermission->sub_permission;
        return view('dashboard.user-management.permissions.subpermission',compact('subpermissions','subpermission'));
    }
    public function storeSubPermissions(Request $request)
    {

        $id            =  $request->permission_id;
        $subpermission =  Permission::where('_id',$id)->first(); 

        $existingSubpermissionsJson = $subpermission->sub_permission;

        // Decode the JSON string to an array
        $existingSubpermissions = json_decode($existingSubpermissionsJson, true);

        // Add the new subpermission to the array (e.g., from the request)
        $newSubpermission = $request->input('sub_permission');
        $existingSubpermissions[] = $newSubpermission;

        // Encode the updated array back to a JSON string
        $updatedSubpermissionsJson = json_encode($existingSubpermissions);

        // Update the 'sub_permission' field in the database with the new JSON string
        $subpermission->update(['sub_permission' => $updatedSubpermissionsJson]);

        return back()->with('success', 'Sub Permission successfully Added!');




    }

    public function deleteSubPermissions(Request $request,$id)
    {
        $pid            =  $request->permission_id;
        $subpermission =  Permission::where('_id',$pid)->first(); 

        $permission = $id;


        $existingSubpermissionsJson = $subpermission->sub_permission;

        // Decode the JSON string to an array
        $existingSubpermissions = json_decode($existingSubpermissionsJson, true);
    
        // Remove the subpermission from the array
        $subpermissionToRemove = $permission;
        $updatedSubpermissions = array_diff($existingSubpermissions, [$subpermissionToRemove]);
    
        // Encode the updated array back to a JSON string
        $updatedSubpermissionsJson = json_encode($updatedSubpermissions);
    
        // Update the 'sub_permission' field in the database with the new JSON string
        $subpermission->update(['sub_permission' => $updatedSubpermissionsJson]);
        return back()->with('success', 'Sub Permission successfully deleted!');

    }

    

    


    
}
