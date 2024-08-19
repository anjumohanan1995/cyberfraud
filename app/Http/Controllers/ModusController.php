<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Modus;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

class ModusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd("hi");
        return view('modus.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('modus.list');
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

        Modus::create([
            'name' => @$request->name? $request->name:'',
            'status' => $request->input('status'),

        ]);

        return redirect()->back()->with('success', 'Modus Added successfully!');
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
        $modus = Modus::find($id);
        return view('modus.edit',compact('modus'));
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
        $modus = Modus::find($id);
        $modus->name = $request->name;
        $modus->status = $request->status;
        $modus->update();
        return redirect()->route('modus.index')->with('success', 'Modus Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

            $modus = Modus::findOrFail($id);
            $modus->delete();
            return response()->json(['success' => 'Modus Deleted successfully!'], 200);
    }

    public function getModus(Request $request){

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

        $query = Modus::where('deleted_at', null);

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%');
            });
        }

        $from_date="";$to_date="";
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        // $items = Modus::where('deleted_at',null)->orderBy('_id', 'desc')
        //                   ->orderBy($columnName, $columnSortOrder);

        $totalRecords = Modus::where('deleted_at', null)->orderBy('created_at','desc')->count();
        $totalRecordswithFilter = $query->count();

        // Fetch records with filter
        $records = $query->orderBy('created_at','desc')
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
            $edit = '<a  href="' . url('modus/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

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

    public function addModus(Request $request){


        $validate = Validator::make($request->all(),
        [
          'name' => 'required',
          'status' => 'required',
        ]);
        if ($validate->fails()) {

            return response()->json(['errors' => $validate->errors()], 422);
        }

        Modus::create([
            'name' => @$request->name? $request->name:'',
            'status' => $request->input('status'),

        ]);
        return response()->json(['success' => 'Modus Added successfully!'], 200);
    }
}
