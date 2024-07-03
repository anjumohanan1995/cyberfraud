<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("dashboard.user-management.users.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role =Role::orderBy('id','desc')->where('deleted_at',null)->get();
        return view("dashboard.user-management.users.create",compact('role'));
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
          'email' => 'required|email|unique:users,deleted_at,NULL',
          'password' => 'required' ,
          'role' => 'required' ,


        ]);
        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        User::create([
            'name' => @$request->name? $request->name:'',
            'last_name' => @$request->lname?$request->lname:'',
            'email' => @$request->email?$request->email:'',
            'password' => Hash::make($request->password),
            'role' => @$request->role?$request->role:''
        ]);

        return redirect()->route('users.index')->with('success','User Added successfully.');


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
        $data = User::findOrFail($id);

        $role =Role::orderBy('id','desc')->where('deleted_at',null)->get();
        return view('dashboard.user-management.users.edit', ['data' => $data,'role'=>$role]);
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
        $data = User::findOrFail($id);

        // Update the permission with the data from the request
        $data->name = $request->name;
        $data->last_name = $request->last_name;
        $data->email = $request->email;
        $data->role = $request->role;
        // Update other attributes as needed

        // Save the updated permission
        $data->save();

        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = User::findOrFail($id);

        $data->delete();

        return back()->with('success', 'User successfully deleted!');
    }



    public function getUsersList(Request $request)
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

            // Total records
            $totalRecord = User::where('deleted_at',null)->orderBy('created_at','desc');
            $totalRecords = $totalRecord->select('count(*) as allcount')->count();


            $totalRecordswithFilte = User::where('deleted_at',null)->orderBy('created_at','desc');
            $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

            // Fetch records
            $items = User::where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);
            $records = $items->skip($start)->take($rowperpage)->get();

            $user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasEditUserPermission = in_array('Edit User', $sub_permissions) || $user->role == 'Super Admin';
                $hasDeleteUserPermission = in_array('Delete User', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasEditUserPermission = false;
                    $hasDeleteUserPermission = false;
                }
            $data_arr = array();
            $i=$start;

            foreach($records as $record){
                $i++;
                $id = $record->id;
                $name = $record->name;
                $email =  $record->email;
                $role  =  $record->role;
                $edit = '';
                // only show links to edit and delete if they have permission. if they ave both permission it should show both edit and delete
                if($hasEditUserPermission || $user->role == 'Super Admin'){
                    $edit .= '<a  href="' . url('users/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
                }elseif($hasDeleteUserPermission || $user->role == 'Super Admin'){
                    $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
                }
                //$edit = '<a  href="' . url('users/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

                $data_arr[] = array(
                    "id" => $i,
                    "name" => $name,
                    "email" => $email,
                    "role" => $role,
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

    public function profile()
    {
        $user = User::where('_id', auth()->user()->id)->where('deleted_at', null)->first();
        return view('profile.view_profile', compact('user'));
    }


}
