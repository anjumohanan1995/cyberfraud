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
    public function getPermissions()
    {
       // Fetch permissions from the database with pagination
       $permissions = Permission::paginate(2); // Assuming you want 10 permissions per page

       // Return permissions data along with pagination links in JSON format
       return response()->json([
           'data' => $permissions->items(), // Permissions data for the current page
           'links' => $permissions->links()->toHtml(), // Pagination links HTML
       ]);
    }
}
