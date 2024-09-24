<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\RolePermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
           $categories = Category::whereNull('deleted_at')->get();
           return view('subcategories.list',compact('categories'));

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
        $categories = Category::whereNull('deleted_at')->get();
        $subcategory = SubCategory::find($id);
        return view('subcategories.edit',compact('subcategory','categories'));
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
            'category' => 'required',
            'subcategory' => 'required',
            'status' => 'required',
        ]);
        //   if ($validate->fails()) {

        //       return response()->json(['errors' => $validate->errors()], 422);
        //   }
        // $subcategory = SubCategory::find($id);
        // $subcategory->category_id = $request->category;
        // $subcategory->subcategory = $request->subcategory;
        // $subcategory->status = $request->status;
        // $subcategory->update();


        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        // Find the role by its ID.
        $data = SubCategory::findOrFail($id);

        $newsubcategory = strtoupper($request->subcategory);

        // Check if the new name already exists for a different record
        if (SubCategory::where('deleted_at', null)->where('name', $newsubcategory)->where('category_id', $request->category)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->withErrors(['name' => 'This SubCategory type name already exists.']);
        }

        // Update the evidence type with the data from the request
        $data->category_id = $request->category;
        $data->subcategory= $newsubcategory;
        $data->status = $request->status;

        // Update other attributes as needed
        // Save the updated evidence type
        $data->save();

        return redirect()->route('subcategory.create')->with('success', 'SubCategory Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $subcategory = SubCategory::findOrFail($id);

        if($subcategory->delete()){
            return response()->json(['success' => 'SubCategory Deleted successfully!'],200);
        }
        else{
            return response()->json(['error' => 'SubCategory Deleted failed!'], 400);
        }

    }
    public function getSubCategories(Request $request){
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);

        $hasDeleteSubCategoryPermission = $sub_permissions && in_array('Delete Subcategory', $sub_permissions) || $user->role == 'Super Admin';

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

        $query = SubCategory::where('deleted_at', null);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('subcategory', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('category', function ($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  });
            });
        }

        $from_date="";$to_date="";
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // Total records without filter
        $totalRecords = SubCategory::where('deleted_at', null)->count();

        // Total records with filter
        $totalRecordswithFilter = $query->count();


            // Fetch records with filter and sorting
        $records = $query->orderBy($columnName, $columnSortOrder) // Apply sorting here
            ->orderBy('created_at', 'desc') // Sort by created_at as secondary order
            ->skip($start)
            ->take($rowperpage)
            ->get();

// dd($records);
        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->id;
            $category = $record->category->name;
            $subcategory = $record->subcategory;
            $status = $record->status == 1 ? 'Active' : 'Inactive';
            $edit = '<a  href="' . url('subcategory/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;';
            if ($hasDeleteSubCategoryPermission) {
                $edit .= '<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';
            }
            $data_arr[] = array(
                "id" => $i,
                "category" => $category,
                "subcategory"=>$subcategory,
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



    public function addSubCategory(Request $request){

          $validate = Validator::make($request->all(),
        [
            'category' => 'required',
            'subcategory' => 'required',
            'status' => 'required',
        ]);
          if ($validate->fails()) {

              return response()->json(['errors' => $validate->errors()], 422);
          }

          $subcategory = strtoupper($request->input('subcategory'));

          // Check if the name already exists in the database
          if (SubCategory::where('deleted_at', null)->where('subcategory', $subcategory)->where('category_id', $request->category)->exists()) {
              return response()->json(['errors' => ['name' => 'This subcategory already exists.']], 422);
          }

          SubCategory::create([
              'category_id' => $request->input('category'),
              'subcategory' => $subcategory,
              'status' => $request->input('status'),


          ]);
          return response()->json(['success' => 'Sub Category Added successfully!'], 200);
    }
}
