<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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





    public function getUsers()
    {
        // Fetch users from the database
       // Fetch users from the database with pagination
       $users = User::paginate(100); // Assuming you want 10 users per page

       // Return users data along with pagination links in JSON format
       return response()->json([
           'data' => $users->items(), // users data for the current page
           'links' => $users->links()->toHtml(), // Pagination links HTML
       ]);
    }
}
