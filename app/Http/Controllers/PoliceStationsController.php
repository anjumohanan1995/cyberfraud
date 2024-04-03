<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\PoliceStations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PoliceStationsController extends Controller
{
    //


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("dashboard.police_stations.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch all districts from the District model
        $districts = District::get();
        dd($districts);

        // Pass the districts data to the view
        return view("dashboard.police_stations.create", compact('districts'));
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
            'station_name' => 'required',
            'district_id' => 'required',
            'place' => 'required',
            'address' => 'required',
            'phone' => 'required',

        ]);
        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        PoliceStations::create([
            'name' => $request->station_name,
            'district_id' => $request->district_id,
            'place' => $request->place,
            'address' => $request->address,
            'phone' => $request->phone,

        ]);

        return redirect()->route('police_stations.index')->with('success','Police Station Added successfully.');


    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     $data = Modus::findOrFail($id);


    //     return view('dashboard.modus.edit', ['data' => $data,]);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //      // Validate the incoming request data
    //      $request->validate([
    //         'name' => 'required|string|max:255',
    //         // Add more validation rules as needed
    //     ]);

    //     // Find the role by its ID.
    //     $data = Modus::findOrFail($id);

    //     // Update the role with the data from the request
    //     $data->name = $request->name;

    //     // Update other attributes as needed
    //     // Save the updated role
    //     $data->save();

    //     // Redirect back with success message
    //     return redirect()->route('modus.index')->with('success', 'Modus updated successfully!');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $data = Modus::findOrFail($id);

    //     $data->delete();

    //     return response()->json(['success' => 'Modus successfully deleted!']);
    // }



    // public function getModus(Request $request)
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

    //         // Total records
    //         $totalRecord = Modus::where('deleted_at',null)->orderBy('created_at','desc');
    //         $totalRecords = $totalRecord->select('count(*) as allcount')->count();


    //         $totalRecordswithFilte = Modus::where('deleted_at',null)->orderBy('created_at','desc');
    //         $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

    //         // Fetch records
    //         $items = Modus::where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);
    //         $records = $items->skip($start)->take($rowperpage)->get();

    //         $data_arr = array();
    //         $i=$start;

    //         foreach($records as $record){
    //             $i++;
    //             $id = $record->id;
    //             $name = $record->name;

    //             $edit = '<a  href="' . url('modus/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

    //             $data_arr[] = array(
    //                 "id" => $i,
    //                 "name" => $name,

    //                 "edit" => $edit
    //             );
    //         }

    //         $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr
    //         );

    //         return response()->json($response);
    // }


}
