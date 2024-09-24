<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\RolePermission;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('category.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('category.list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validate = Validator::make($request->all(),
        [
          'name' => 'required',
          'status' => 'required',
        ]);


        if ($validate->fails()) {
            //dd($validate);
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $name = strtoupper($request->name);

        // Check if the name already exists in the database
        if (Category::where('deleted_at', null)->where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => 'This category type name already exists.']], 422);
        }

        Category::create([
            'name' => $name,
            'status' => $request->input('status'),
        ]);


        // return redirect()->back()->with('success', 'Category Added successfully!');
        return response()->json(['success' => 'Category Added successfully.']);
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
        //
        $category = category::find($id);
        return view('category.edit',compact('category'));
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
        //
        $validate = Validator::make($request->all(),
        [
          'name' => 'required',
          'status' => 'required',
        ]);

        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        // Find the role by its ID.
        $data = Category::findOrFail($id);

        $newName = strtoupper($request->name);

        // Check if the new name already exists for a different record
        if (Category::where('deleted_at', null)->where('name', $newName)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['name' => 'This category type name already exists.']);
        }

        // Update the evidence type with the data from the request
        $data->name = $newName;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated evidence type
        $data->save();

        return redirect()->route('category.index')->with('success', 'Category Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(['success' => 'Category Deleted successfully!'], 200);
    }

    public function getCategories(Request $request){

        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteCategoryPermission = $sub_permissions && in_array('Delete Category', $sub_permissions) || $user->role == 'Super Admin';

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $query = Category::where('deleted_at', null);

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $from_date="";$to_date="";
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // $items = Category::where('deleted_at',null)->orderBy('_id', 'desc')
        //                   ->orderBy($columnName, $columnSortOrder);

        // $records = $items->skip($start)->take($rowperpage)->get();
        // $totalRecord = Category::where('deleted_at',null)->orderBy('_id', 'desc');
        // $totalRecords = $totalRecord->select('count(*) as allcount')->count();
        // $totalRecordswithFilter = $totalRecords;

        $totalRecords = Category::where('deleted_at', null)->count();

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
            $status = $record->status == 1 ? 'Active' : 'Inactive';
            $edit = '<a  href="' . url('category/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
            if ($hasDeleteCategoryPermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }
            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "status" => $status,
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

    public function addCategory(Request $request){


        $validate = Validator::make($request->all(),
        [
          'name' => 'required',
          'status' => 'required',
        ]);
        if ($validate->fails()) {
            //dd($validate);
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $name = strtoupper($request->name);

        // Check if the name already exists in the database
        if (Category::where('deleted_at', null)->where('name', $name)->exists()) {
            return response()->json(['errors' => ['name' => 'This category type name already exists.']], 422);
        }

        Category::create([
            'name' => $name,
            'status' => $request->input('status'),
        ]);

        // return response()->json(['success' => 'Category Added successfully!'], 200);
        return response()->json(['success' => 'Category Added successfully.']);
    }
}
