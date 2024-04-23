<?php

namespace App\Http\Controllers;

use App\Models\Modus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;


class ModusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("dashboard.modus.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view("dashboard.modus.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required',



            ]
        );
        if ($validate->fails()) {
            //dd($validate);
            return Redirect::back()->withInput()->withErrors($validate);
        }

        Modus::create([
            'name' => @$request->name ? $request->name : '',

        ]);

        return redirect()->route('modus.index')->with('success', 'Modus Added successfully.');
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
        $data = Modus::findOrFail($id);


        return view('dashboard.modus.edit', ['data' => $data,]);
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

        // Find the role by its ID.
        $data = Modus::findOrFail($id);

        // Update the role with the data from the request
        $data->name = $request->name;

        // Update other attributes as needed
        // Save the updated role
        $data->save();

        // Redirect back with success message
        return redirect()->route('modus.index')->with('success', 'Modus updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Modus::findOrFail($id);

        $data->delete();

        return response()->json(['success' => 'Modus successfully deleted!']);
    }



    public function getModus(Request $request)
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
        $totalRecord = Modus::where('deleted_at', null)->orderBy('created_at', 'desc');
        $totalRecords = $totalRecord->select('count(*) as allcount')->count();


        $totalRecordswithFilte = Modus::where('deleted_at', null)->orderBy('created_at', 'desc');
        $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

        // Fetch records
        $items = Modus::where('deleted_at', null)->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder);
        $records = $items->skip($start)->take($rowperpage)->get();

        $data_arr = array();
        $i = $start;

        foreach ($records as $record) {
            $i++;
            $id = $record->id;
            $source_type = $record->source_type;
            $acknowledgement_no = $record->acknowledgement_no;
            $district = $record->district;
            $police_station = $record->police_station;
            $complainant_name = $record->complainant_name;
            $complainant_mobile = $record->complainant_mobile;
            $transaction_id = $record->transaction_id;
            $bank_name = $record->bank_name;
            $account_id = $record->account_id;
            $amount = $record->amount;
            $entry_date = $record->entry_date;
            $current_status = $record->current_status;
            $date_of_action = $record->date_of_action;
            $action_taken_by_name = $record->action_taken_by_name;
            $action_taken_by_designation = $record->action_taken_by_designation;
            $action_taken_by_mobile = $record->action_taken_by_mobile;
            $action_taken_by_email = $record->action_taken_by_email;
            $action_taken_by_bank = $record->action_taken_by_bank;


            $edit = '<a  href="' . url('modus/' . $id . '/edit') . '" class="btn btn-primary edit-btn">Edit</a>&nbsp;&nbsp;<button class="btn btn-danger delete-btn" data-id="' . $id . '">Delete</button>';

            $data_arr[] = array(
                "source_type" => $source_type,
                "acknowledgement_no" => $acknowledgement_no,
                "district" => $district,
                "police_station" => $police_station,
                "complainant_name" => $complainant_name,
                "complainant_mobile" => $complainant_mobile,
                "transaction_id" => $transaction_id,
                "bank_name" => $bank_name,
                "account_id" => $account_id,
                "amount" => $amount,
                "entry_date" => $entry_date,
                "current_status" => $current_status,
                "date_of_action" => $date_of_action,
                "action_taken_by_name" => $action_taken_by_name,
                "action_taken_by_designation" => $action_taken_by_designation,
                "action_taken_by_mobile" => $action_taken_by_mobile,
                "action_taken_by_email" => $action_taken_by_email,
                "action_taken_by_bank" => $action_taken_by_bank,
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
}
