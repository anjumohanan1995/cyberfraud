<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;

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
            'status'=>'active',
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

    public function update(Request $request, $id)
{
    // Custom validation rules
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => [
            'nullable',
            'regex:/^(?=.*[!@#$%^&*()_+\-=\[\]{};:\'\"\\|,.<>\/?])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/'
        ],
        'old_password' => 'nullable|min:6', // Ensure old password is provided if changing password
        'sign' => 'nullable|image',
    ]);

    // Find the user by ID
    $data = User::findOrFail($id);

    // Update name, email, and role if provided
    $data->name = $request->input('name', $data->name);
    $data->last_name = $request->input('last_name', $data->last_name);
    $data->email = $request->input('email', $data->email);
    $data->role = $request->input('role', $data->role);
    $data->status = $request->input('status', $data->status);

    // Handle password update
    if ($request->filled('password')) {
        // Check if the old password is correct
        if (!Hash::check($request->old_password, $data->password)) {
            return redirect()->back()->withErrors(['old_password' => 'The current password is incorrect.']);
        }

        // Update to the new password
        $data->password = Hash::make($request->password);
    }

    // Handle the signature file upload if a new file is provided
    if ($request->hasFile('sign')) {
        // Delete the old signature if it exists
        if ($data->sign && file_exists(public_path($data->sign))) {
            unlink(public_path($data->sign));
        }

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


    public function destroy($id)
    {
        $data = User::findOrFail($id);

        $data->delete();

        return back()->with('success', 'User successfully deleted!');
    }
    // public function getUsersList(Request $request)
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

    //     // Retrieve the status filter (active or inactive)
    //     $statusFilter = $request->get('status_filter', 'active'); // Default to 'active'

    //     // Build the base query
    //     $query = User::query();

    //     // Apply filter based on status
    //     if ($statusFilter === 'active') {
    //         $query->whereNull('deleted_at');
    //     } else if ($statusFilter === 'inactive') {
    //         $query->whereNotNull('deleted_at');
    //     }

    //     // Apply search filter
    //     if (!empty($searchValue)) {
    //         $query->where(function ($q) use ($searchValue) {
    //             $q->where('name', 'like', '%' . $searchValue . '%')
    //               ->orWhere('email', 'like', '%' . $searchValue . '%')
    //               ->orWhere('role', 'like', '%' . $searchValue . '%');
    //         });
    //     }

    //     // Total records
    //     $totalRecords = $query->count();

    //     // Sort
    //     $query->orderBy($columnName, $columnSortOrder);

    //     // Pagination
    //     $records = $query->skip($start)->take($rowperpage)->get();

    //     // Process user permissions
    //     $user = Auth::user();
    //     $role = $user->role;
    //     $permission = RolePermission::where('role', $role)->first();
    //     $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
    //     $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

    //     $hasEditUserPermission = in_array('Edit User', $sub_permissions) || $user->role == 'Super Admin';
    //     $hasDeleteUserPermission = in_array('Delete User', $sub_permissions) || $user->role == 'Super Admin';

    //     $data_arr = [];
    //     $i = $start;

    //     foreach ($records as $record) {
    //         $i++;
    //         $id = $record->id;
    //         $name = $record->name;
    //         $email = $record->email;
    //         $role = $record->role;
    //         $edit = '';

    //         if ($hasEditUserPermission || $user->role == 'Super Admin') {
    //             $edit .= '<a href="' . url('users/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
    //         }
    //         if ($hasDeleteUserPermission || $user->role == 'Super Admin') {
    //             $edit .= '<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';
    //         }

    //         $data_arr[] = [
    //             "id" => $i,
    //             "name" => $name,
    //             "email" => $email,
    //             "role" => $role,
    //             "edit" => $edit
    //         ];
    //     }

    //     $response = [
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecords,
    //         "aaData" => $data_arr
    //     ];

    //     return response()->json($response);
    // }

    public function getUsersList(Request $request)
    {
        // Read values
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

        // Retrieve the status filter (active or inactive)
        $statusFilter = $request->get('status_filter', 'active'); // Default to 'active'

        // Build the base query
        $query = User::query();

        // Apply filter based on status field in MongoDB
        if ($statusFilter === 'active') {
            $query->where('status', 'active'); // Ensure to filter based on 'status' field in MongoDB
        } else if ($statusFilter === 'inactive') {
            $query->where('status', 'inactive'); // Ensure to filter based on 'status' field in MongoDB
        }

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
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

        // Process user permissions
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
        $data_arr = [];
        $i = $start;

        foreach ($records as $record) {
            $i++;
            $id = $record->id;
            $name = $record->name;
            $email = $record->email;
            $role = $record->role;
            $status = $record->status; // Get the status field
            $edit = '';

            // Create toggle button for active/inactive status
            $isChecked = $status === 'active' ? 'checked' : '';
            $statusToggleButton = '
                <label class="switch">
                    <input type="checkbox" class="status-toggle" data-id="' . $id . '" ' . $isChecked . '>
                    <span class="slider round"></span>
                </label>
            ';

            if ($hasEditUserPermission || $user->role == 'Super Admin') {
                $edit .= '<a href="' . url('users/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
            }
            // if ($hasDeleteUserPermission || $user->role == 'Super Admin') {
            //     $edit .= '<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';
            // }

            $data_arr[] = [
                "id" => $i,
                "name" => $name,
                "email" => $email,
                "role" => $role,
                "status" => $statusToggleButton,
                "edit" => $edit
            ];
        }


        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }

    public function updateStatus(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        $status = $request->input('status');

    if ($status === 'inactive') {
        $remover = Auth::user();

        // Log the activity
        ActivityLog::create([
            'remover_id' => $remover->id,
            'remover_role' => $remover->role,
            'remover_name' => $remover->name,
            'removed_id' => $user->id,
            'removed_name' => $user->name,
        ]);
    }

    // Update the user's status
    $user->status = $status;
    $user->save();

        // Return success response
        return response()->json(['success' => 'Status updated successfully.']);
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

