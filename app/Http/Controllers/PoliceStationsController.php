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
        $data = PoliceStations::findOrFail($id);
        $districts = District::get();


        return view('dashboard.police_stations.edit', ['data' => $data,], compact('districts'));
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
            'station_name' => 'required|string|max:255',
            'district_id' => 'required',
            'place' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            // Add more validation rules as needed
        ]);

        // Find the police station by its ID
        $data  = PoliceStations::findOrFail($id);



        // Update the police station with the data from the request
        $data->name = $request->station_name;
        $data->district_id = $request->district_id;
        $data->place = $request->place;
        $data->address = $request->address;
        $data->phone = $request->phone;

        // Save the updated police station
        $data->save();

        // Redirect back with success message
        return redirect()->route('police_stations.index')->with('success', 'Police Station updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = PoliceStations::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Police station successfully deleted!']);
    }

    public function getpolice_stations(Request $request)
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

    // Total records
    $totalRecord = PoliceStations::with('district')->where('deleted_at', null);
    $totalRecords = $totalRecord->select('count(*) as allcount')->count();

    $totalRecordswithFilte = PoliceStations::with('district')->where('deleted_at', null);
    // Apply search
    if (!empty($searchValue)) {
        $totalRecordswithFilte->where(function($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('district', function($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  })
                  ->orWhere('place', 'like', '%' . $searchValue . '%')
                  ->orWhere('address', 'like', '%' . $searchValue . '%')
                  ->orWhere('phone', 'like', '%' . $searchValue . '%');
        });
    }

    $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

    // Fetch records
    $items = PoliceStations::with('district')->where('deleted_at', null);
    // Apply search
    if (!empty($searchValue)) {
        $items->where(function($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhereHas('district', function($q) use ($searchValue) {
                      $q->where('name', 'like', '%' . $searchValue . '%');
                  })
                  ->orWhere('place', 'like', '%' . $searchValue . '%')
                  ->orWhere('address', 'like', '%' . $searchValue . '%')
                  ->orWhere('phone', 'like', '%' . $searchValue . '%');
        });
    }

    $items->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder)->skip($start)->take($rowperpage);
    $records = $items->get();

    $data_arr = array();
    $i = $start;

    foreach ($records as $record) {
        $i++;
        $id = $record->id;
        $name = $record->name;
        $district = $record->district->name;
        $place = $record->place;
        $address = $record->address;
        $phone = $record->phone;

        $edit = '<a href="' . url('police_stations/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';

        $data_arr[] = array(
            "id" => $i,
            "name" => $name,
            "district" => $district,
            "place" => $place,
            "address" => $address,
            "phone" => $phone,
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




    // public function getpolice_stations(Request $request)
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

    //     // Total records
    //     $totalRecord = PoliceStations::with('district')->where('deleted_at',null)->orderBy('created_at','desc');
    //         $totalRecords = $totalRecord->select('count(*) as allcount')->count();


    //     $totalRecordswithFilte = PoliceStations::with('district')->where('deleted_at',null)->orderBy('created_at','desc');

    //     // Apply search
    // if (!empty($searchValue)) {
    //     $totalRecordswithFilte->where(function($query) use ($searchValue) {
    //         $query->where('name', 'like', '%' . $searchValue . '%')
    //               ->orWhereHas('district', function($q) use ($searchValue) {
    //                   $q->where('name', 'like', '%' . $searchValue . '%');
    //               })
    //               ->orWhere('place', 'like', '%' . $searchValue . '%')
    //               ->orWhere('address', 'like', '%' . $searchValue . '%')
    //               ->orWhere('phone', 'like', '%' . $searchValue . '%');
    //     });
    // }

    //         $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

    //         // Fetch records
    //         $items = PoliceStations::with('district')->where('deleted_at',null)->orderBy('created_at','desc')->orderBy($columnName,$columnSortOrder);

    //             // Apply search
    // if (!empty($searchValue)) {
    //     $items->where(function($query) use ($searchValue) {
    //         $query->where('name', 'like', '%' . $searchValue . '%')
    //               ->orWhereHas('district', function($q) use ($searchValue) {
    //                   $q->where('name', 'like', '%' . $searchValue . '%');
    //               })
    //               ->orWhere('place', 'like', '%' . $searchValue . '%')
    //               ->orWhere('address', 'like', '%' . $searchValue . '%')
    //               ->orWhere('phone', 'like', '%' . $searchValue . '%');
    //     });
    // }
    //         $records = $items->skip($start)->take($rowperpage)->get();

    //         $data_arr = array();
    //         $i=$start;

    //         foreach($records as $record) {
    //             $i++;
    //             $id = $record->id;
    //             $name = $record->name;
    //             $district = $record->district->name;
    //             $place = $record->place;
    //             $address = $record->address;
    //             $phone = $record->phone;

    //             $edit = '<a href="' . url('police_stations/'.$id.'/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="'.$id.'">Delete</button>';

    //             $data_arr[] = array(
    //                 "id" => $i,
    //                 "name" => $name,
    //                 "district" => $district,
    //                 "place" => $place,
    //                 "address" => $address,
    //                 "phone" => $phone,
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
