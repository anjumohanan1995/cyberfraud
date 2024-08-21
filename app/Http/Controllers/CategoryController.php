<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

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
            return Redirect::back()->withInput()->withErrors($validate);
        }

        Category::create([
            'name' => @$request->name? $request->name:'',
            'status' => $request->input('status'),

        ]);

        return redirect()->back()->with('success', 'Category Added successfully!');
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
        $category = category::find($id);
        $category->name = $request->name;
        $category->status = $request->status;
        $category->update();
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
            $edit = '<a  href="' . url('category/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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

            return response()->json(['errors' => $validate->errors()], 422);
        }

        Category::create([
            'name' => @$request->name? $request->name:'',
            'status' => $request->input('status'),

        ]);
        return response()->json(['success' => 'Category Added successfully!'], 200);
    }
}
