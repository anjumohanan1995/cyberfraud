<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use App\Models\Complaint;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use MongoDB\Client;


=======
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\DB;
>>>>>>> 0b77ab2928cb24296a1f6ec510ec94e3b72d6706

class CaseDataController extends Controller
{
    public function index()
    {
        // Assuming $banks is an array of bank data
        $banks = [
            ['id' => 1, 'name' => 'Ratnakar Bank Limited (RBL)'],
            ['id' => 2, 'name' => 'State Bank of India'],
            ['id' => 3, 'name' => 'Dhanlaxmi Bank'],
            ['id' => 4, 'name' => 'Federal Bank']
        ];

        // // Assuming $banks is an array of bank data
        // $wallet = [
        //     ['id' => 1, 'name' => 'Ratnakar Bank Limited (RBL)'],
        //     ['id' => 2, 'name' => 'State Bank of India'],
        //     ['id' => 3, 'name' => 'Dhanlaxmi Bank']
        // ];

        // Pass the $banks data to the view
        return view('dashboard.case-data-list.index')->with('banks', $banks);
    }

     public function bankCaseData(Request $request)
    {

        $acknowledgement_no = $request->acknowledgement_no;
        $account_id = $request->account_id;

        return view('dashboard.case-data-list.bank-casedata', compact('acknowledgement_no', 'account_id'));
    }


    public function getBankDatalist(Request $request)
    {
        $acknowledgement_no = intval($request->acknowledgement_no);
        $account_id = intval($request->account_id);


        // dd($acknowledgement_no +'-'+ $account_id);
        // dd($acknowledgement_no);
        // dd($account_id);


        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page.

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index.
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name.
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc.
        $searchValue = $search_arr['value']; // Search value.



        // Total records.
        $totalRecordsQuery = BankCasedata::where('acknowledgement_no', $acknowledgement_no)
            ->where('account_no_1', $account_id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');


        // dd($totalRecordsQuery);

        $totalRecords = $totalRecordsQuery->count();

        // Total records with filter.
        $totalRecordswithFilterQuery = clone $totalRecordsQuery;
        $totalRecordswithFilterQuery->where(function ($query) use ($searchValue) {
            // Add your search conditions here.
        });

        $totalRecordswithFilter = $totalRecordswithFilterQuery->count();

        // Fetch records
        $itemsQuery = clone $totalRecordsQuery;
        $itemsQuery->orderBy($columnName, $columnSortOrder);

        // dd($totalRecordsQuery->count());


        // dd($acknowledgement_no . '-' . $account_id);
        // dd(BankCasedata::all());

        // dd(BankCasedata::where('acknowledgement_no', $acknowledgement_no)->where('account_no_1', $account_id)->get()->count());




        $records = $itemsQuery->skip($start)->take($rowperpage)->get();

        // dd($records->count());

        $data_arr = [];
        $i = $start;

        foreach ($records as $record) {
            $i++;
            // Extracting data from $record object
            $data_arr[] = [
                "id" => $i,
                'acknowledgement_no' => $record->acknowledgement_no,
                'transaction_id_or_utr_no' => $record->transaction_id_or_utr_no,
                'Layer' => $record->Layer,
                'account_no_1' => $record->account_no_1,
                'action_taken_by_bank' => $record->action_taken_by_bank,
                'bank' => $record->bank,
                'account_no_2' => $record->account_no_2,
                'ifsc_code' => $record->ifsc_code,
                'cheque_no' => $record->cheque_no,
                'mid' => $record->mid,
                'tid' => $record->tid,
                'approval_code' => $record->approval_code,
                'merchant_name' => $record->merchant_name,
                'transaction_date' => $record->transaction_date,
                'transaction_amount' => $record->transaction_amount,
                'reference_no' => $record->reference_no,
                'remarks' => $record->remarks,
                'date_of_action' => $record->date_of_action,
                'action_taken_name' => $record->action_taken_name,
                'action_taken_email' => $record->action_taken_email,
                'branch_location' => $record->branch_location,
                'branch_manager_details' => $record->branch_manager_details,
                "edit" => '<div><form action="' . url("case-data/bank-case-data") . '" method="GET"><input type="hidden" name="acknowledgement_no" value="' . $record->acknowledgement_no . '"><input type="hidden" name="account_id" value="' . $record->account_no_2 . '"><button type="submit" class="btn btn-danger">Show Case</button></form></div>'
            ];
        }

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }



    public function getDatalist(Request $request)
    {      
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page.

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        
        $columnIndex = $columnIndex_arr[0]['column']; // Column index.
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name.
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc.
        $searchValue = $search_arr['value']; // Search value.

<<<<<<< HEAD
        // Total records.
        $totalRecord = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder);
        //$totalRecords = $totalRecord->select('count(*) as allcount')->count();
        $totalRecords = Complaint::groupBy('acknowledgement_no')->get()->count();
     
        $totalRecordswithFilte = Complaint::where('deleted_at', null)->orderBy('created_at', 'desc');
        //$totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();
        $totalRecordswithFilter = Complaint::groupBy('acknowledgement_no')->get()->count();

        //Fetch records.
            $items = Complaint::groupBy('acknowledgement_no')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'desc')
            ->orderBy($columnName, $columnSortOrder);
            echo $items->count();
        if($searchValue){
            $items = Complaint::groupBy('acknowledgement_no')
            ->where('acknowledgement_no', 'like', '%' . $searchValue . '%')
            ->orWhere('district', 'like', '%' . $searchValue . '%')
            ->orWhere('complainant_name', 'like', '%' . $searchValue . '%')
            ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
            ->orWhere('police_station', 'like', '%' . $searchValue . '%')
            ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'desc')
            ->orderBy($columnName, $columnSortOrder); 

      

            $totalRecords = Complaint::groupBy('acknowledgement_no')->where('acknowledgement_no', 'like', '%' . $searchValue . '%')->orWhere('district', 'like', '%' . $searchValue . '%')->orWhere('complainant_name', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->orWhere('police_station', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->where('deleted_at', null)->get()->count();

            $totalRecordswithFilter = $totalRecords;
        }

        $records = $items->skip($start)->take($rowperpage)->get();
=======
        // Retrieve parameters from the request
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $mobile = $request->get('mobile');
        $acknowledgement_no= $request->get('acknowledgement_no');
        $filled_by = $request->get('filled_by');
        $search_by = $request->get('search_by');
        $options = $request->get('options');
        // dd($acknowledgement_no);
        // dd($filled_by );


        // Base query for total records
        $totalRecordQuery = Complaint::where('deleted_at', null);

        // Base query for filtered records
        $items = Complaint::where('deleted_at', null);

        // Apply filter conditions
        if ($fromDate && $toDate) {
        // Parse and format dates using Carbon
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate . ' 00:00:00')->startOfDay();
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $toDate . ' 23:59:59')->endOfDay();

        // Convert Carbon objects to UTCDateTime
        $fromUTC = new UTCDateTime($from->getTimestamp() * 1000);
        $toUTC = new UTCDateTime($to->getTimestamp() * 1000);

        // Filter records based on the formatted dates
        $totalRecordQuery->whereBetween('entry_date', [$fromUTC, $toUTC]);
        $items->whereBetween('entry_date', [$fromUTC, $toUTC]);
        }

        if ($mobile) {
            $mobile = (int)$mobile;
            $totalRecordQuery->where('complainant_mobile', $mobile);
            $items->where('complainant_mobile', $mobile);
        }
        if ($options && $options!='null') {
            $totalRecordQuery->where('bank_name', $options);
            $items->where('bank_name', $options);
        }
        if ($acknowledgement_no) {
            $acknowledgement_no = (int)$acknowledgement_no;
            $totalRecordQuery->where('acknowledgement_no', $acknowledgement_no);
            $items->where('acknowledgement_no', $acknowledgement_no);
        }


// Apply "Filled by" filter
if ($filled_by) {
    switch ($filled_by) {
        case 'citizen':
            // Filter citizen filled entries within 24 hours
            $startOfDay = Carbon::now()->subDay()->startOfDay();
            $endOfDay = Carbon::now()->endOfDay();
            $items->where('entry_date', '>=', new UTCDateTime($startOfDay->timestamp * 1000))
                ->where('entry_date', '<=', new UTCDateTime($endOfDay->timestamp * 1000));
            // Filter citizen filled entries
            $items->where('acknowledgement_no', '>=', 21500000000000)->where('acknowledgement_no', '<=', 21599999999999);
            break;
        case 'cyber':
            // Filter cyber filled entries within 24 hours
            $startOfDay = Carbon::now()->subDay()->startOfDay();
            $endOfDay = Carbon::now()->endOfDay();
            $items->where('entry_date', '>=', new UTCDateTime($startOfDay->timestamp * 1000))
                ->where('entry_date', '<=', new UTCDateTime($endOfDay->timestamp * 1000));
            // Filter cyber filled entries
            $items->where('acknowledgement_no', '>=', 31500000000000)->where('acknowledgement_no', '<=', 31599999999999);
            break;
        default:
            // Do nothing for 'All' option
            break;
    }
}




// Apply filter based on selected option
if ($search_by) {
    switch ($search_by) {
        case 'account_id':
            // Fetch records from the complaints collection where account_id exists
            $complaints = Complaint::where('account_id', 'exists', true)->get()->pluck('account_id');

            // Fetch records from the bank_casedata collection where account_no_1 exists
            $bankData = BankCasedata::where('account_no_1', 'exists', true)->get()->pluck('account_no_1');

            // Compare the account_id fields from both collections
            $matchingIds = $complaints->intersect($bankData)->toArray();

            // Filter items where the account_id exists in both collections
            $items->whereIn('complaints.account_id', $matchingIds);
            $items->whereIn('bank_casedata.account_no_1', $matchingIds);

            break;
        case 'transaction_id':
            // Fetch records from the complaints collection where transaction_id exists
            $complaints = Complaint::where('transaction_id', 'exists', true)->get()->pluck('transaction_id');

            // Fetch records from the bank_casedata collection where transaction_id_or_utr_no exists
            $bankData = BankCasedata::where('transaction_id_or_utr_no', 'exists', true)->get()->pluck('transaction_id_or_utr_no');

            // Compare the transaction_id fields from both collections
            $matchingIds = $complaints->intersect($bankData);

            // Filter items where the transaction_id exists in both collections
            $items->whereIn('complaints.transaction_id', $matchingIds);
            $items->whereIn('bank_casedata.transaction_id_or_utr_no', $matchingIds);
           // dd($items);
            break;
        default:
            // Do nothing for other options
            break;
    }
}


        // Total records count
        $totalRecords = $totalRecordQuery->count();

        // Total records count after filtering
        $totalRecordswithFilter = $items->count();
        // DB::connection()->enableQueryLog();
        // DB::enableQueryLog();
        // Fetch filtered records with pagination
        $records = $items->orderBy('created_at', 'desc')
                    ->orderBy($columnName, $columnSortOrder)
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();
                    // dd($records->toSql());
                    // dd(DB::getQueryLog());

//First You will need to enable the query log by calling:


// $queries = DB::getQueryLog();

// // Print or log the queries
// foreach ($queries as $query) {
//     Log::info($query['query']);
// }

        // Prepare data for response
>>>>>>> 0b77ab2928cb24296a1f6ec510ec94e3b72d6706
        $data_arr = array();
        $i = $start + 1; // Adjust pagination number

        foreach ($records as $record){
            $com = Complaint::where('acknowledgement_no',$record->acknowledgement_no)->take(10)->get();
            $i++;
            $id = $record->id;
            $source_type = $record->source_type;
            $acknowledgement_no = $record->acknowledgement_no;
<<<<<<< HEAD
           
            $transaction_id="";$amount="";
            foreach($com as $com){
                $transaction_id .= $com->transaction_id."<br>"; 
                $amount .= $com->amount."<br>";
                $complainant_name = $com->complainant_name;
                $complainant_mobile = $com->complainant_mobile;
                $bank_name = $com->bank_name;
                $district = $com->district;
                $police_station = $com->police_station;
                $account_id = $com->account_id;
                $entry_date = $com->entry_date;
                $current_status = $com->current_status;
                $date_of_action = $com->date_of_action;
                $action_taken_by_name = $com->action_taken_by_name;
                $action_taken_by_designation = $com->action_taken_by_designation;
                $action_taken_by_mobile = $com->action_taken_by_mobile;
                $action_taken_by_email = $com->action_taken_by_email;
                $action_taken_by_bank = $com->action_taken_by_bank;
            }
       
=======
            $district = $record->district;
            $police_station = $record->police_station;
            $complainant_name = $record->complainant_name;
            $complainant_mobile = $record->complainant_mobile;
            $transaction_id = $record->transaction_id;
            $bank_name = $record->bank_name;
            $account_id = $record->account_id;
            $amount = $record->amount;
            $entry_date = Carbon::parse($record->entry_date)->format('Y-m-d H:i:s');
            $current_status = $record->current_status;
            $date_of_action = $record->date_of_action;
            $action_taken_by_name = $record->action_taken_by_name;
            $action_taken_by_designation = $record->action_taken_by_designation;
            $action_taken_by_mobile = $record->action_taken_by_mobile;
            $action_taken_by_email = $record->action_taken_by_email;
            $action_taken_by_bank = $record->action_taken_by_bank;

>>>>>>> 0b77ab2928cb24296a1f6ec510ec94e3b72d6706
            $edit = '<div><form action="' . url("case-data/bank-case-data") . '" method="GET"><input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '"><input type="hidden" name="account_id" value="' . $account_id . '"><button type="submit" class="btn btn-danger">Show Case</button></form></div>';
           
            $data_arr[] = array(
                "id" => $i,
                "acknowledgement_no" => '<a href="'.url("case-data/details-view").'">'.$acknowledgement_no.'</a>',
                "district" => $district."<br>".$police_station,
                "complainant_name" => $complainant_name."<br>".$complainant_mobile,
                "transaction_id" => $transaction_id,
                "bank_name" => $bank_name,
                "account_id" => $account_id,
                "amount" => $amount,
                "entry_date" => $entry_date,
                "current_status" => $current_status,
                "date_of_action" => $date_of_action,
                "action_taken_by_name" => $action_taken_by_name,
                "edit" => $edit
            );
        }

// dd($data_arr);

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );


        return response()->json($response);
    }

<<<<<<< HEAD
    public function detailsView(){
        return view('dashboard.case-data-list.index');
    }
=======

>>>>>>> 0b77ab2928cb24296a1f6ec510ec94e3b72d6706
}
