<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
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
          if ($validate->fails()) {
           
              return response()->json(['errors' => $validate->errors()], 422);
          }
        $subcategory = SubCategory::find($id);
        $subcategory->category_id = $request->category;
        $subcategory->subcategory = $request->subcategory;
        $subcategory->status = $request->status;
        $subcategory->update();

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

        $from_date="";$to_date="";
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $items = SubCategory::where('deleted_at',null)->orderBy('_id', 'desc')
                          ->orderBy($columnName, $columnSortOrder);

        $records = $items->skip($start)->take($rowperpage)->get();
        $totalRecord = SubCategory::where('deleted_at',null)->orderBy('_id', 'desc');
        $totalRecords = $totalRecord->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $totalRecords;

        $data_arr = array();
        $i=$start;
      
        foreach($records as $record){
            $i++;
            $id = $record->id;
            $category = $record->category->name;
            $subcategory = $record->subcategory;
            $status = $record->status == 1 ? 'Active' : 'Inactive';
            $edit = '<a  href="' . url('subcategory/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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
  
          SubCategory::create([
              'category_id' => $request->input('category'),
              'subcategory' => $request->input('subcategory'),
              'status' => $request->input('status'),
              
  
          ]);
          return response()->json(['success' => 'Sub Category Added successfully!'], 200);
    }
}
