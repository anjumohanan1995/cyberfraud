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
        $user = Auth::user();
        $role = $user->role;
        $roles =Role::orderBy('id','desc')->where('deleted_at',null)->get();
        return view("dashboard.user-management.users.create",compact('roles','role'));
    }

    public function store(Request $request)
    {
        // Validation rules
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,deleted_at,NULL',
            'password' => ['required', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],
            'role' => 'required',
            'sign' => 'image|nullable', // Nullable if signature is not uploaded
            'sign_name' => 'required_with:sign,sign_designation|nullable',
            'sign_designation' => 'required_with:sign,sign_name|nullable'
        ]);

        // Check if validation fails
        if ($validate->fails()) {
            return Redirect::back()->withInput()->withErrors($validate);
        }

        // Initialize the image path variable
        $imagePath = '';

        // Handle the signature upload if it exists
        if ($request->hasFile('sign')) {
            $file = $request->file('sign');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('signatures'), $filename);

            // Save the relative path to the database
            $imagePath = 'signatures/' . $filename;
        }

        // Create the user
        User::create([
            'name' => $request->name ?: '',
            'last_name' => $request->lname ?: '',
            'email' => $request->email ?: '',
            'password' => Hash::make($request->password),
            'role' => $request->role ?: '',
            'sign' => $imagePath,
            'sign_name' => $request->sign_name ?: '',
            'sign_designation' => $request->sign_designation ?: '',
        ]);

        // Redirect with success message
        return redirect()->route('users.index')->with('success', 'User added successfully.');
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
        $user = Auth::user();
        $role = $user->role;

        $roles =Role::orderBy('id','desc')->where('deleted_at',null)->get();
        return view('dashboard.user-management.users.edit', ['data' => $data,'roles'=>$roles, 'role'=>$role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     // Validate the incoming request data
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'password' => ['nullable', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],
    //         'sign' => 'nullable|image' // Validate 'sign' if it's provided
    //     ]);

    //     // Find the user by its ID
    //     $data = User::findOrFail($id);

    //     // Update the user with the data from the request
    //     $data->name = $request->name;
    //     $data->last_name = $request->last_name;
    //     $data->email = $request->email;
    //     $data->role = $request->role;

    //     // Only update the password if a new password is provided
    //     if ($request->filled('password')) {
    //         $data->password = Hash::make($request->password);
    //     }

    //     // Handle file upload for 'sign'
    //     if ($request->hasFile('sign')) {
    //         // Delete the old signature if it exists
    //         if ($data->sign && file_exists(public_path($data->sign))) {
    //             unlink(public_path($data->sign));
    //         }

    //         // Store the new signature directly in the public/signatures directory
    //         $file = $request->file('sign');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path('signatures'), $filename);
    //         $data->sign = 'signatures/' . $filename;
    //     }

    //     // Save the updated user data
    //     $data->save();

    //     // Redirect back with success message
    //     return redirect()->route('users.index')->with('success', 'User updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        // Custom validation rules
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => ['nullable', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],
            'sign' => 'nullable|image',
        ]);

        // Find the user by ID
        $data = User::findOrFail($id);

        // Update name, email, and role if provided
        $data->name = $request->input('name', $data->name);
        $data->last_name = $request->input('last_name', $data->last_name);
        $data->email = $request->input('email', $data->email);
        $data->role = $request->input('role', $data->role);

        // Only update the password if it's provided and not null
        if ($request->filled('password')) {
            $data->password = Hash::make($request->password);
        }

        // Handle the signature file upload if a new file is provided
        if ($request->hasFile('sign')) {
            // Delete the old signature if it exists
            // if ($data->sign && file_exists(public_path($data->sign))) {
            //     unlink(public_path($data->sign));
            // }

            // Store the new signature
            $file = $request->file('sign');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('signatures'), $filename);
            $data->sign = 'signatures/' . $filename;
        }

        // Update the sign-related fields if provided
        $data->sign_name = $request->input('sign_name', $data->sign_name);
        $data->sign_designation = $request->input('sign_designation', $data->sign_designation);

        // Save the updated user data
        $data->save();

        // Redirect back with success message
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }


//      public function update(Request $request, $id)
// {
//     // Define custom validation messages
//     // $messages = [
//     //     'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character. It must also be at least 8 characters long.',
//     // ];

//     // Validate the incoming request data
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'password' => ['nullable', 'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],
//         'sign' => 'nullable|image' // Validate 'sign' if it's provided
//     ]);

//     // Find the user by its ID
//     $data = User::findOrFail($id);

//     // Update the user with the data from the request
//     $data->name = $request->name;
//     $data->last_name = $request->last_name;
//     $data->email = $request->email;
//     $data->role = $request->role;

//     // Only update the password if a new password is provided
//     if ($request->filled('password')) {
//         $data->password = Hash::make($request->password);
//     }

//      // Handle file upload for 'sign'
//      if ($request->hasFile('sign')) {
//         // Delete the old signature if it exists
//         if ($data->sign && file_exists(public_path($data->sign))) {
//             unlink(public_path($data->sign));
//         }
//         // Store the new signature
//         $file = $request->file('sign');
//             $filename = time() . '_' . $file->getClientOriginalName();
//             $file->move(public_path('signatures'), $filename);
//             $data->sign = 'signatures/' . $filename;
//     }

//     // Save the updated user data
//     $data->save();

//     // Redirect back with success message
//     return redirect()->route('users.index')->with('success', 'User updated successfully!');
// }

    // public function update(Request $request, $id)
    // {
    //      // Validate the incoming request data
    //      $request->validate([
    //         'name' => 'required|string|max:255',
    //         'password' => ['regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'],

    //         // Add more validation rules as needed
    //     ]);

    //     // Find the permission by its ID.
    //     $data = User::findOrFail($id);

    //     // Update the permission with the data from the request
    //     $data->name = $request->name;
    //     $data->last_name = $request->last_name;
    //     $data->email = $request->email;
    //     $data->role = $request->role;
    //     // 'password' => Hash::make($request->password),

    //     $data->password = Hash::make($request->password);

    //     // Update other attributes as needed

    //     // Save the updated permission
    //     $data->save();

    //     // Redirect back with success message
    //     return redirect()->route('users.index')->with('success', 'User updated successfully!');
    // }

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

        $query = User::where('deleted_at', null);

        // Search
        if(!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('email', 'like', '%' . $searchValue . '%')
                  ->orWhere('role', 'like', '%' . $searchValue . '%');
            });
        }

        // Total records
        $totalRecords = $query->count();

        // Sort
        $query->orderBy($columnName, $columnSortOrder);

        // Pagination
        $records = $query->skip($start)->take($rowperpage)->get();

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
                }
                if($hasDeleteUserPermission || $user->role == 'Super Admin'){
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
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
            );

            return response()->json($response);
    }

    public function profile()
    {
        $user = Auth::user();
            $role = $user->role;
            $permission = RolePermission::where('role', $role)->first();
            $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
            $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
            if ($sub_permissions || $user->role == 'Super Admin') {
                $hasEditUserPermission = in_array('Edit User', $sub_permissions) || $user->role == 'Super Admin';
                } else{
                    $hasEditUserPermission = false;
                }
        $user = User::where('_id', auth()->user()->id)->where('deleted_at', null)->first();
        return view('profile.view_profile', compact('user', 'role', 'hasEditUserPermission'));
    }


}

