<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use App\Models\Complaint;
use App\Models\ComplaintOthers;
use App\Models\Bank;
use App\Models\ComplaintAdditionalData;
use App\Models\Fir;
use App\Models\Wallet;
use App\Models\Merchant;
use App\Models\Insurance;
use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\DB;
use MongoDB\Client;
use Illuminate\Support\Facades\Crypt;
use App\Models\SourceType;
use Excel;
use App\Models\EvidenceType;
use App\exports\SampleExport;





class CaseDataController extends Controller
{
    public function index()
    {
        // Retrieve the bank data from the Bank model
        $banks = Bank::all()->map(function($bank) {
            return [
                'id' => $bank->id,
                'name' => $bank->bank
            ];
        })->toArray();

        // dd($banks);

        $wallets = Wallet::all()->map(function($wallet) {
            return [
                'id' => $wallet->id,
                'name' => $wallet->wallet
            ];
        })->toArray();

        // dd($wallets);

        $merchants = Merchant::all()->map(function($merchant) {
            return [
                'id' => $merchant->id,
                'name' => $merchant->merchant
            ];
        })->toArray();

        // dd($merchants);

        $insurances = Insurance::all()->map(function($insurance) {
            return [
                'id' => $insurance->id,
                'name' => $insurance->insurance
            ];
        })->toArray();

        // dd($insurances);

        // Pass the $banks and $wallets data to the view
        return view('dashboard.case-data-list.index')->with([
            'banks' => $banks,
            'wallets' => $wallets,
            'merchants' => $merchants,
            'insurances' => $insurances
    ]);
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
        $totalRecordswithFilterQuery->where(function ($query) use ($searchValue){
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
        // Initialize variables
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
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $mobile = $request->get('mobile');
        $acknowledgement_no= $request->get('acknowledgement_no');
        $filled_by = $request->get('filled_by');
        $search_by = $request->get('search_by');
        $options = $request->get('options');
        $com_status = $request->get('com_status');
        // dd($com_status);
        $fir_lodge = $request->get('fir_lodge');
        $filled_by_who = $request->get('filled_by_who');
        $transaction_id = $request->get('transaction_id');
        $account_id = $request->get('account_id');

        // Filter conditions
        if ($com_status == "1"){
            $query = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->where('com_status', 1);
        }
        elseif ($com_status == "0"){
            $query = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->where('com_status', 0);

        }else{
            $query = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null);
        }

        // if (!empty($com_status)) {
        //     $query->where('com_status', (int)$com_status);
        // }

        if ($fromDate && $toDate) {
            $query->whereBetween('entry_date', [Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay(), Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay()]);
        }

        if (!empty($mobile)) {
            $query->where('complainant_mobile', (int)$mobile);
        }

        if (!empty($transaction_id)) {
            $query->where('transaction_id', (int)$transaction_id);
        }

        if (!empty($account_id)) {
            $query->where('account_id', (int)$account_id);
        }

        if (!empty($options) && $options != 'null') {
            $query->where('bank_name', $options);
        }

        if (!empty($acknowledgement_no)) {
            $query->where('acknowledgement_no', (int)$acknowledgement_no);
        }

        if (!empty($filled_by) && in_array($filled_by, ['citizen', 'cyber'])) {
            $query->where('entry_date', '>=', Carbon::now()->subDay()->startOfDay()->timestamp * 1000)
                  ->where('entry_date', '<=', Carbon::now()->endOfDay()->timestamp * 1000)
                  ->whereBetween('acknowledgement_no', [$filled_by === 'citizen' ? 21500000000000 : 31500000000000, $filled_by === 'citizen' ? 21599999999999 : 31599999999999]);
        }

        if (!empty($filled_by_who) && in_array($filled_by_who, ['citizen', 'cyber'])) {
            $query->whereBetween('acknowledgement_no', [$filled_by_who === 'citizen' ? 21500000000000 : 31500000000000, $filled_by_who === 'citizen' ? 21599999999999 : 31599999999999]);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('acknowledgement_no', 'like', '%' . $searchValue . '%')
                  ->orWhere('district', 'like', '%' . $searchValue . '%')
                  ->orWhere('complainant_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('police_station', 'like', '%' . $searchValue . '%');
            });
        }

// Check if FIR Lodge filter is enabled
if ($fir_lodge == "1") {
    // Retrieve acknowledgment numbers where fir_doc is not null
    $ackNumbers = ComplaintAdditionalData::whereNotNull('fir_doc')->pluck('ack_no');
    // Initialize an empty array to store all acknowledgment numbers
    $ackNumbersToFilter = [];

    // Loop through each acknowledgment number
    foreach ($ackNumbers as $ackNumber) {
        // Count the occurrences of the acknowledgment number in the Complaint table
        $acknumbers = Complaint::where('acknowledgement_no', (int)$ackNumber)->pluck('acknowledgement_no');

        // Merge acknowledgment numbers into the array
        $ackNumbersToFilter = array_merge($ackNumbersToFilter, $acknumbers->toArray());
    }
    // dd($ackNumbersToFilter);

    // Apply the FIR Lodge filter to the main query
    $query->whereIn('acknowledgement_no', $ackNumbersToFilter);
}

// Check if FIR Lodge filter is enabled
if ($fir_lodge == "0") {
    // Retrieve acknowledgment numbers where fir_doc is not null
    $ackNumbers = ComplaintAdditionalData::whereNotNull('fir_doc')->pluck('ack_no');

    // Initialize an empty array to store all acknowledgment numbers
    $ackNumbersToFilter = [];

    // Loop through each acknowledgment number
    foreach ($ackNumbers as $ackNumber) {
        // Add acknowledgment number to the array
        $ackNumbersToFilter[] = (int)$ackNumber;
    }

    // Apply the FIR Lodge filter to the main query
    $query->whereNotIn('acknowledgement_no', $ackNumbersToFilter);
}


        // Total records count
        $totalRecords = $query->get()->count();
        // dd($totalRecords);

        // Fetch records
        $records = $query->orderBy('created_at', 'desc')
                         ->orderBy($columnName, $columnSortOrder)
                         ->skip($start)
                         ->take($rowperpage)
                         ->get();


        $data_arr = array();
        $i = $start;

        // $totalRecordswithFilter =  $totalRecords;

        foreach ($records as $record){
            $com = Complaint::where('acknowledgement_no',$record->acknowledgement_no)->take(10)->get();
            $i++;
            $id = $record->id;
            $source_type = $record->source_type;
            $acknowledgement_no = $record->acknowledgement_no;

            $transaction_id="";$amount="";$bank_name="";
            foreach($com as $com){
                $transaction_id .= $com->transaction_id."<br>";
                $amount .= '<span class="editable" data-ackno="'.$record->acknowledgement_no.'" data-transaction="'.$com->transaction_id.'" >'.$com->amount."</span><br>";
                $bank_name .= $com->bank_name."<br>";
                $complainant_name = $com->complainant_name;
                $complainant_mobile = $com->complainant_mobile;

                $district = $com->district;
                $police_station = $com->police_station;
                $account_id = $com->account_id;
                $entry_date = Carbon::parse($com->entry_date)->format('Y-m-d H:i:s');
                $current_status = $com->current_status;
                $date_of_action = $com->date_of_action;
                $action_taken_by_name = $com->action_taken_by_name;
                $action_taken_by_designation = $com->action_taken_by_designation;
                $action_taken_by_mobile = $com->action_taken_by_mobile;
                $action_taken_by_email = $com->action_taken_by_email;
                $action_taken_by_bank = $com->action_taken_by_bank;
            }
            // $ack_no ='<form action="' . route('case-data.view') . '" method="POST">' .
            // '<input type="hidden" name="_token" value="' . csrf_token() . '">' . // Add CSRF token
            // '<input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '">' . // Hidden field for the acknowledgment number
            // '<button class="btn btn-outline-success" type="submit">' . $acknowledgement_no . '</button>' . // Submit button with the acknowledgment number as text
            // '</form>';
            $id = Crypt::encrypt($acknowledgement_no);
            $ack_no = '<a class="btn btn-outline-primary" href="' . route('case-data.view', ['id' => $id]) . '">' . $acknowledgement_no . '</a>';
           // $ack_no = '<a href="' . route('case-data.view', ['id' => $acknowledgement_no]) . '">' . $acknowledgement_no . '</a>';
            // $edit = '<div><form action="' . url("case-data/bank-case-data") . '" method="GET"><input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '"><input type="hidden" name="account_id" value="' . $account_id . '"><button type="submit" class="btn btn-danger">Show Case</button></form></div>';
            $edit = '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
            <input
                data-id="' . $acknowledgement_no . '"
                onchange="confirmActivation(this)"
                class="form-check-input"
                type="checkbox"
                id="SwitchCheckSizesm' . $com->id . '"
                ' . ($com->com_status == 1 ? 'checked   title="Deactivate"' : '  title="Activate"') . '>
         </div>';
            $data_arr[] = array(
                "id" => $i,
                "acknowledgement_no" => $ack_no,
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

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }


//     public function getDatalist(Request $request)
//     {
//       //  Complaint::query()->update(['com_status' => 1]);
//         $draw = $request->get('draw');
//         $start = $request->get("start");
//         $rowperpage = $request->get("length"); // Rows display per page.

//         $columnIndex_arr = $request->get('order');
//         $columnName_arr = $request->get('columns');
//         $order_arr = $request->get('order');
//         $search_arr = $request->get('search');

//         $columnIndex = $columnIndex_arr[0]['column']; // Column index.
//         $columnName = $columnName_arr[$columnIndex]['data']; // Column name.
//         $columnSortOrder = $order_arr[0]['dir']; // asc or desc.
//         $searchValue = $search_arr['value']; // Search value.
//         $fromDate = $request->get('from_date');
//         $toDate = $request->get('to_date');
//         $mobile = $request->get('mobile');
//         $acknowledgement_no= $request->get('acknowledgement_no');
//         $filled_by = $request->get('filled_by');
//         $search_by = $request->get('search_by');
//         $options = $request->get('options');
//         $com_status = $request->get('com_status');
//         $fir_lodge = $request->get('fir_lodge');
//         $filled_by_who = $request->get('filled_by_who');
//         $transaction_id = $request->get('transaction_id');
//         $account_id = $request->get('account_id');
//         // dd($transaction_id);
//         // dd($filled_by_who);
//         // dd($fir_lodge);


//         // Total records.
//         $totalRecordQuery = Complaint::where('deleted_at', null);
//         $totalRecord = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder);

//         //$totalRecords = $totalRecord->select('count(*) as allcount')->count();
//         $totalRecords = Complaint::groupBy('acknowledgement_no')->get()->count();

//         $totalRecordswithFilte = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->orderBy('created_at', 'desc');
//         //$totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();
//         $totalRecordswithFilter = Complaint::groupBy('acknowledgement_no')->get()->count();

//         //Fetch records.
//         if ($com_status === '' || $com_status === null) {
//             $com_status = "1";
//         }
//          //dd($com_status);
//             $items = Complaint::groupBy('acknowledgement_no')
//             ->where('deleted_at', null)
//             ->where('com_status', (int)$com_status)
//             ->orderBy('_id', 'desc')
//             ->orderBy($columnName, $columnSortOrder);

//             $totalRecords  = Complaint::groupBy('acknowledgement_no')
//             ->where('deleted_at', null)
//             ->where('com_status', (int)$com_status)
//             ->orderBy('created_at', 'desc')
//             ->orderBy($columnName, $columnSortOrder)->get()->count();
//             $totalRecordswithFilter = $totalRecords;
//         // Apply filter conditions
//         if ($fromDate && $toDate){
//             // Parse and format dates using Carbon
//             $totalRecordswithFilter = 0;
//             $from = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate . ' 00:00:00')->startOfDay();
//             $to = Carbon::createFromFormat('Y-m-d H:i:s', $toDate . ' 23:59:59')->endOfDay();

//             // Convert Carbon objects to UTCDateTime
//             $fromUTC = new UTCDateTime($from->getTimestamp() * 1000);
//             $toUTC = new UTCDateTime($to->getTimestamp() * 1000);

//             // Filter records based on the formatted dates
//             $totalRecordQuery->whereBetween('entry_date', [$fromUTC, $toUTC]);
//             $items->whereBetween('entry_date', [$fromUTC, $toUTC]);
//             $totalRecords = Complaint::groupBy('acknowledgement_no')
//                             ->whereBetween('entry_date', [$fromUTC, $toUTC])->get()->count();
//             $totalRecordswithFilter = $totalRecords;
//             }

//             if ($mobile){
//                 $totalRecordswithFilter = 0;
//                 $mobile = (int)$mobile;
//                 $totalRecordQuery->where('complainant_mobile', $mobile);

//                 $items->where('complainant_mobile', $mobile);
//                 $totalRecords = Complaint::groupBy('acknowledgement_no')
//                             ->where('complainant_mobile', $mobile)->where('com_status',(int)$com_status)->get()->count();
//                 $totalRecordswithFilter = $totalRecords;
//             }

//             if ($transaction_id){
//                 $totalRecordswithFilter = 0;
//                 $transaction_id = (int)$transaction_id;
//                 // dd($transaction_id);
//                 $totalRecordQuery->where('transaction_id', $transaction_id);
//                 $items->where('transaction_id', $transaction_id);

//                 $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('transaction_id', $transaction_id)->where('com_status',(int)$com_status)->get()->count();
//                 // dd($totalRecords);
//                 $totalRecordswithFilter = $totalRecords;
//             }
//             // dd($items);

//             if ($account_id){
//                 $totalRecordswithFilter = 0;
//                 $account_id = (int)$account_id;
//                 // dd($transaction_id);
//                 $totalRecordQuery->where('account_id ', $account_id);
//                 $items->where('account_id', $account_id);
//                 // dd($items);
//                 $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('account_id', $account_id)->where('com_status',(int)$com_status)->get()->count();
//                 // dd($totalRecords);
//                 $totalRecordswithFilter = $totalRecords;
//             }

//             if ($options && $options!='null') {
//                 $totalRecordswithFilter = 0;
//                 $totalRecordQuery->where('bank_name', $options);
//                 $items->where('bank_name', $options);
//                 $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('bank_name', $options)->where('com_status',(int)$com_status)->get()->count();
//                 //dd($totalRecords);
//                 $totalRecordswithFilter = $totalRecords;
//             }
//             if ($acknowledgement_no){
//                 $totalRecordswithFilter = 0;
//                 $acknowledgement_no = (int)$acknowledgement_no;
//                 $totalRecordQuery->where('acknowledgement_no', $acknowledgement_no);
//                 $items->where('acknowledgement_no', $acknowledgement_no);
//                 $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('acknowledgement_no', $acknowledgement_no)->where('com_status',(int)$com_status)->get()->count();
//                 $totalRecordswithFilter = $totalRecords;
//             }

//             if ($fir_lodge == "1") {
//                 // Retrieve acknowledgement numbers where fir_doc is not null
//                 $ackNumbers = ComplaintAdditionalData::whereNotNull('fir_doc')->pluck('ack_no');

//                 // Initialize total records count
//                 $totalRecordswithFilter = 0;

//                 // Loop through each acknowledgement number
//                 foreach ($ackNumbers as $ackNumber) {
//                     // Apply the filter to the total record query
//                     $totalRecordQuery->where('acknowledgement_no', (int)$ackNumber)
//                                      ->where('com_status', (int)$com_status);

//                     // Apply the same filter to the items query
//                     $items->orWhere(function ($query) use ($ackNumber, $com_status) {
//                         $query->where('acknowledgement_no', (int)$ackNumber)
//                               ->where('com_status', (int)$com_status);
//                     });

//                     // Calculate the total records count with the applied filters
//                     $totalRecords = Complaint::groupBy('acknowledgement_no')->where('acknowledgement_no', (int)$ackNumber)
//                                              ->where('com_status', (int)$com_status)
//                                              ->count();

//                     // Increment total records count
//                     $totalRecordswithFilter = $totalRecords;
//                 }
//             }

// if ($filled_by){
//     switch ($filled_by){
//         case 'citizen':
//             $totalRecordswithFilter = 0;
//             // Filter citizen filled entries within 24 hours
//             $startOfDay = Carbon::now()->subDay()->startOfDay();
//             $endOfDay = Carbon::now()->endOfDay();
//             $items->where('entry_date', '>=', new UTCDateTime($startOfDay->timestamp * 1000))
//                 ->where('entry_date', '<=', new UTCDateTime($endOfDay->timestamp * 1000));
//             // Filter citizen filled entries
//             $items->where('acknowledgement_no', '>=', 21500000000000)->where('acknowledgement_no', '<=', 21599999999999);
//             $totalRecords = Complaint::groupBy('acknowledgement_no')
//             ->where('acknowledgement_no', '>=', 21500000000000)->where('acknowledgement_no', '<=', 21599999999999)->where('com_status',(int)$com_status)->get()->count();
//                 $totalRecordswithFilter = $totalRecords;
//             break;
//         case 'cyber':
//             $totalRecordswithFilter = 0;
//             // Filter cyber filled entries within 24 hours
//             $startOfDay = Carbon::now()->subDay()->startOfDay();
//             $endOfDay = Carbon::now()->endOfDay();
//             $items->where('entry_date', '>=', new UTCDateTime($startOfDay->timestamp * 1000))
//                 ->where('entry_date', '<=', new UTCDateTime($endOfDay->timestamp * 1000));
//             // Filter cyber filled entries
//             $items->where('acknowledgement_no', '>=', 31500000000000)->where('acknowledgement_no', '<=', 31599999999999);
//             $totalRecords = Complaint::groupBy('acknowledgement_no')
//             ->where('acknowledgement_no', '>=', 31500000000000)->where('acknowledgement_no', '<=', 31599999999999)->where('com_status',(int)$com_status)->get()->count();
//                 $totalRecordswithFilter = $totalRecords;
//             break;
//         default:
//             // Do nothing for 'All' option
//             break;
//     }
// }

// if ($filled_by_who) {
//     switch ($filled_by_who) {
//         case 'citizen':
//             $totalRecordswithFilter = 0;
//             // Filter citizen filled entries
//             $items->where('acknowledgement_no', '>=', 21500000000000)->where('acknowledgement_no', '<=', 21599999999999);
//             $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('acknowledgement_no', '>=', 21500000000000)->where('acknowledgement_no', '<=', 21599999999999)->where('com_status', (int)$com_status)->get()->count();
//             $totalRecordswithFilter = $totalRecords;
//             break;
//         case 'cyber':
//             $totalRecordswithFilter = 0;
//             // Filter cyber filled entries
//             $items->where('acknowledgement_no', '>=', 31500000000000)->where('acknowledgement_no', '<=', 31599999999999);
//             $totalRecords = Complaint::groupBy('acknowledgement_no')
//                 ->where('acknowledgement_no', '>=', 31500000000000)->where('acknowledgement_no', '<=', 31599999999999)->where('com_status', (int)$com_status)->get()->count();
//             $totalRecordswithFilter = $totalRecords;
//             break;
//         default:
//             // Do nothing for 'All' option
//             break;
//     }
// }

//         if($searchValue){
//             $items = Complaint::groupBy('acknowledgement_no')
//             ->where('acknowledgement_no', 'like', '%' .$searchValue . '%')
//             ->orWhere('district', 'like', '%' . $searchValue . '%')
//             ->orWhere('complainant_name', 'like', '%' . $searchValue . '%')
//             ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
//             ->orWhere('police_station', 'like', '%' . $searchValue . '%')
//             ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
//             ->where('deleted_at', null)
//             ->orderBy('_id', 'desc')
//             ->orderBy($columnName, $columnSortOrder);

//             $totalRecords = Complaint::groupBy('acknowledgement_no')->where('acknowledgement_no', 'like', '%' . $searchValue . '%')->orWhere('district', 'like', '%' . $searchValue . '%')->orWhere('complainant_name', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->orWhere('police_station', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->where('deleted_at', null)->get()->count();

//             $totalRecordswithFilter = $totalRecords;
//         }
//         //$totalRecords = $totalRecordQuery->count();

//         // Total records count after filtering
//         //$totalRecordswithFilter = $items->count();
//         $totalRecords = $totalRecordQuery->count();
//         $records = $items->skip($start)->take($rowperpage)->get();
//         $totalRecordswithFilter = $totalRecords;

//         $data_arr = array();
//         $i = $start;

//         foreach ($records as $record){
//             $com = Complaint::where('acknowledgement_no',$record->acknowledgement_no)->take(10)->get();
//             $i++;
//             $id = $record->id;
//             $source_type = $record->source_type;
//             $acknowledgement_no = $record->acknowledgement_no;

//             $transaction_id="";$amount="";$bank_name="";
//             foreach($com as $com){
//                 $transaction_id .= $com->transaction_id."<br>";
//                 $amount .= '<span class="editable" data-ackno="'.$record->acknowledgement_no.'" data-transaction="'.$com->transaction_id.'" >'.$com->amount."</span><br>";
//                 $bank_name .= $com->bank_name."<br>";
//                 $complainant_name = $com->complainant_name;
//                 $complainant_mobile = $com->complainant_mobile;

//                 $district = $com->district;
//                 $police_station = $com->police_station;
//                 $account_id = $com->account_id;
//                 $entry_date = Carbon::parse($com->entry_date)->format('Y-m-d H:i:s');
//                 $current_status = $com->current_status;
//                 $date_of_action = $com->date_of_action;
//                 $action_taken_by_name = $com->action_taken_by_name;
//                 $action_taken_by_designation = $com->action_taken_by_designation;
//                 $action_taken_by_mobile = $com->action_taken_by_mobile;
//                 $action_taken_by_email = $com->action_taken_by_email;
//                 $action_taken_by_bank = $com->action_taken_by_bank;
//             }
//             // $ack_no ='<form action="' . route('case-data.view') . '" method="POST">' .
//             // '<input type="hidden" name="_token" value="' . csrf_token() . '">' . // Add CSRF token
//             // '<input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '">' . // Hidden field for the acknowledgment number
//             // '<button class="btn btn-outline-success" type="submit">' . $acknowledgement_no . '</button>' . // Submit button with the acknowledgment number as text
//             // '</form>';
//             $id = Crypt::encrypt($acknowledgement_no);
//             $ack_no = '<a class="btn btn-outline-primary" href="' . route('case-data.view', ['id' => $id]) . '">' . $acknowledgement_no . '</a>';
//            // $ack_no = '<a href="' . route('case-data.view', ['id' => $acknowledgement_no]) . '">' . $acknowledgement_no . '</a>';
//             // $edit = '<div><form action="' . url("case-data/bank-case-data") . '" method="GET"><input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '"><input type="hidden" name="account_id" value="' . $account_id . '"><button type="submit" class="btn btn-danger">Show Case</button></form></div>';
//             $edit = '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
//             <input
//                 data-id="' . $acknowledgement_no . '"
//                 onchange="confirmActivation(this)"
//                 class="form-check-input"
//                 type="checkbox"
//                 id="SwitchCheckSizesm' . $com->id . '"
//                 ' . ($com->com_status == 1 ? 'checked   title="Deactivate"' : '  title="Activate"') . '>
//          </div>';
//             $data_arr[] = array(
//                 "id" => $i,
//                 "acknowledgement_no" => $ack_no,
//                 "district" => $district."<br>".$police_station,
//                 "complainant_name" => $complainant_name."<br>".$complainant_mobile,
//                 "transaction_id" => $transaction_id,
//                 "bank_name" => $bank_name,
//                 "account_id" => $account_id,
//                 "amount" => $amount,
//                 "entry_date" => $entry_date,
//                 "current_status" => $current_status,
//                 "date_of_action" => $date_of_action,
//                 "action_taken_by_name" => $action_taken_by_name,
//                 "edit" => $edit
//             );
//         }

//         $response = array(
//             "draw" => intval($draw),
//             "iTotalRecords" => $totalRecords,
//             "iTotalDisplayRecords" => $totalRecordswithFilter,
//             "aaData" => $data_arr
//         );

//         return response()->json($response);
//     }

    public function detailsView(){
        return view('dashboard.case-data-list.index');
    }
   
    public function caseDataView(Request $request,$id){
        $id = Crypt::decrypt($id);

        $complaint = Complaint::where('acknowledgement_no',(int)$id)->first();
        $complaints = Complaint::where('acknowledgement_no',(int)$id)->get();
        $sum_amount = Complaint::where('acknowledgement_no', (int)$id)->where('com_status',1)->sum('amount');
        $bank_datas = BankCasedata::where('acknowledgement_no',(int)$id)->orderBy('Layer','asc')->get();
        $additional = ComplaintAdditionalData::where('ack_no',(string)$id)->first();

       // $transaction_numbers_layer1 = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',1)->get();
        $layers = BankCasedata::where('acknowledgement_no',(int)$id)->groupBy('Layer')->pluck('Layer');
        $pending_banks_array = [];
        for($i=1;$i<=count($layers);$i++){
           
            $transaction_numbers_left_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)->pluck('transaction_id_or_utr_no');

            $transaction_numbers_right_side="";$transaction_numbers_left_side="";$transaction_numbers_left_side_array="";
            $transaction_numbers_left_side_array_final="";

            $transaction_numbers_right_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)
                                                            ->where('action_taken_by_bank','Money Transfer to')->get();
            
            ++$i;

            $transaction_numbers_left_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)->pluck('transaction_id_or_utr_no');

            $transaction_numbers_left_side_array = explode(" ",$transaction_numbers_left_side);
            $mergedArray = [];

            foreach ($transaction_numbers_left_side_array as $item) {
         
                $values = explode(',', trim($item, '[]'));
                $values = array_map('trim', $values);
                $mergedArray = array_merge($mergedArray, $values);
            }

            $transaction_numbers_left_side_array_final = array_map(function($item) {
                return intval(trim($item, '"'));
            }, $mergedArray);

            foreach ($transaction_numbers_right_side as $tn){
                if($tn->transaction_id_sec){
                    if (!(in_array($tn->transaction_id_sec, $transaction_numbers_left_side_array_final))) {
                        $j=$i-1;
                        $pending_banks_array[] = array(
                            "layer"=>$j,
                            "pending_banks" => $tn->bank,
                            "transaction_id" => $tn->transaction_id_sec
                        );             
                     }
                     
                     
                }
                

             } 
             --$i;
        }

    $groupedData = [];
  
    foreach ($pending_banks_array as $item) {
    $layer = $item['layer'];
    $pendingBank = $item['pending_banks'];
    $transactionId = $item['transaction_id'];

    if (!isset($groupedData[$layer])) {
        $groupedData[$layer] = [];
    }

    $groupedData[$layer][] = ['pending_banks' => $pendingBank, 'transaction_id' => $transactionId];
    }

    $uniqueLayers = array_keys($groupedData);

    $finalData_pending_banks = [];
    foreach ($uniqueLayers as $layer) {
    $finalData_pending_banks[] = ['layer' => $layer, 'pending_banks' => $groupedData[$layer]];
    }
   // dd($finalData_pending_banks);
   
        $professions = Profession::where('status', 'active')
        ->whereNull('deleted_at')
        ->get();
        
        return view('dashboard.case-data-list.details',compact('complaint','complaints','bank_datas','sum_amount','additional','professions','finalData_pending_banks'));
    }

    public function editdataList(Request $request){
        $complaint = Complaint::where('acknowledgement_no',(int)$request->ackno)
                                ->where('transaction_id',(int)$request->transaction)
                                ->where('amount',(int)$request->amount)
                                ->first();
        if($complaint){
            $complaint->update([
                'amount' => (int)$request->new_amount
            ]);
            return redirect()->route('case-data.index')->with('message','success');
        }
        else{
            return response()->json(['error' => true, 'message' => 'error']);
        }

    }
    public function activateLink(Request $request)
    {

        $ackId = (int) $request->ack_id;
        $status = (int) $request->status;

        Complaint::where('acknowledgement_no', $ackId)
                 ->update(['com_status' => $status]);


        return response()->json(['status'=>'Status changed successfully.']);
    }
    public function activateLinkIndividual(Request $request)
    {

        //$id = Crypt::decrypt($request->com_id);
        $com_id =$request->com_id;
        $status = (int) $request->status;

        Complaint::where('_id', $com_id)
                 ->update(['com_status' => $status]);


        return response()->json(['status'=>'Status changed successfully.']);
    }

    public function caseDataOthers(){
       return view('dashboard.case-data-list.case-data-list-others');
    }

    public function getDatalistOthers(Request $request){
        // dd($request);
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

        $source_types = SourceType::all();
        $casenumber = $request->casenumber;
        $domain = $request->domain;
        $url = $request->url;
        $registrar = $request->registrar;
        $ip = $request->ip;
        //dd($casenumber);
        $complaints = ComplaintOthers::raw(function($collection) use ($start, $rowperpage, $casenumber, $url, $domain , $registrar , $ip) {

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$case_number',
                        'source_type' => ['$addToSet' => '$source_type'],
                        'url' => ['$addToSet' => '$url'],
                        'domain' => ['$addToSet' => '$domain'],
                        'registry_details' => ['$addToSet' => '$registry_details'],
                        'ip' => ['$addToSet' => '$ip'],
                        'registrar' => ['$addToSet' => '$registrar'],
                        'remarks' => ['$addToSet' => '$remarks'],
                    ]
                ],
                [
                    '$sort' => [
                        '_id' => 1,
                ]
                ],
                [
                    '$skip' => (int)$start
                ],
                [
                    '$limit' => (int)$rowperpage
                ]
            ];

            if (isset($casenumber)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'case_number' => $casenumber
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($registrar)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'registrar' => $registrar
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($ip)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ip' => $ip
                        ]
                    ]
                ], $pipeline);
            }

            return $collection->aggregate($pipeline);
        });

        $distinctCaseNumbers = ComplaintOthers::raw(function($collection) use ($casenumber, $url , $domain , $registrar) {

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$case_number'
                    ]
                ]
            ];

            if (isset($casenumber)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'case_number' => $casenumber
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($registrar)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'registrar' => $registrar
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($ip)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ip' => $ip
                        ]
                    ]
                ], $pipeline);
            }

            return $collection->aggregate($pipeline);
        });




        $totalRecords = count($distinctCaseNumbers);
        $data_arr = array();
        $i = $start;


        $totalRecordswithFilter =  $totalRecords;
        foreach($complaints as $record){

            $i++;
            $url = "";$domain="";$ip="";$registrar="";$remarks=""; $source_type="";

            $case_number = '<a href="' . route('other-case-details', ['id' => Crypt::encryptString($record->_id)]) . '">'.$record->_id.'</a>';

            foreach ($record->url as $item) {
                $url .= $item."<br>";
            }
            foreach ($record->source_type as $item) {
                foreach($source_types as $st){
                    if($st->_id == $item){
                        $source_type .= $st->name."<br>";
                    }
                }
            }
            foreach ($record->domain as $item) {
                $domain .= $item."<br>";
            }
            foreach ($record->ip as $item) {
                $ip .= $item."<br>";
            }
            foreach ($record->registrar as $item) {
                $registrar .= $item."<br>";
            }
            foreach ($record->remarks as $item) {
                $remarks .= $item."<br>";
            }

            $data_arr[] = array(
                    "id" => $i,
                    "source_type" => $source_type,
                    "case_number" => $case_number,
                    "url" => $url,
                    "domain" => $domain,
                    "ip" => $ip,
                    "registrar"=>$registrar,
                    "remarks" => $remarks,
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

    public function uploadOthersCaseData(){

        $sourceTypes = SourceType::where('status', 'active')->whereNull('deleted_at')->where('name', '!=', 'NCRP')->get();
        return view("import_complaints_others", compact('sourceTypes'));
    }

    public function otherCaseDetails($case_number){

        $case_details =  ComplaintOthers::where('case_number',Crypt::decryptString($case_number))->get();
        return view('other-case-details',compact('case_details'));
    }

    public function editotherCaseDetails($id){

       $complaint_others_by_id =  ComplaintOthers::find($id);
       return view('other-case-details-view',compact('complaint_others_by_id'));
    }

    public function updateotherCaseDetails(Request $request,$id){

        $com_oth = ComplaintOthers::find($id);

        $com_oth->url = $request->url;
        $com_oth->domain = $request->domain;
        $com_oth->registry_details = $request->registry_details;
        $com_oth->ip = $request->ip;
        $com_oth->registrar = $request->registrar;
        $com_oth->remarks = $request->remarks;

        if($com_oth->update()){
            return redirect()->route('other-case-details',['id' => Crypt::encryptString($com_oth->case_number)])->with('success', 'Updated successfully.');
        }
        else{
            return redirect()->back()->with('error', 'error when update!!');
        }
    }

    public function firUpload(Request $request)
    {

        if(!empty($request->fir_file)){

            $fileName = uniqid().'.'.$request->fir_file->extension();

            $request->fir_file->move(public_path('/fir_doc'), $fileName);

        }
        $complaint = ComplaintAdditionalData::where('ack_no',$request->acknowledgement_no)->first();

        if($complaint == ''){

            $complaint=   ComplaintAdditionalData::create([
            'ack_no' => @$request->acknowledgement_no? $request->acknowledgement_no:'']);
        }
        $complaint->ack_no=$request->acknowledgement_no;
        $complaint->fir_doc=$fileName;
        $complaint->save();

        return redirect()->back()->with('status', 'FIR uploaded successfully.');
    }
    public function downloadFIR(Request $request, $id)
    {
        $complaint = ComplaintAdditionalData::where('ack_no', $id)->first();

        if ($complaint && $complaint->fir_doc) {
            $filePath = public_path('fir_doc/' . $complaint->fir_doc);

            if (file_exists($filePath)) {
                return response()->download($filePath);
            } else {
                return redirect()->back()->with('error', 'FIR file not found.');
            }
        } else {
            return redirect()->back()->with('error', 'FIR file information not available.');
        }
    }

    public function profileUpdate(Request $request)
    {
        // dd($request);

        $complaint = ComplaintAdditionalData::where('ack_no',$request->acknowledgement_no)->first();
        if($complaint == ''){

            $complaint =   ComplaintAdditionalData::create([
            'ack_no' => @$request->acknowledgement_no? $request->acknowledgement_no:'']);
        }
        $complaint->ack_no=$request->acknowledgement_no;
        $complaint->age=$request->age;
        $complaint->profession=$request->profession;
        $complaint->save();

        return redirect()->back()->with('status', 'Profile updated successfully.');
    }

    public function getCaseNumber(Request $request){

            $sourcetype = $request->sourcetype;
            $firstThreeCharacters = Str::substr($sourcetype, 0, 3);
            $today = now()->format('Ymd');
            $lastCaseNumber = ComplaintOthers::where('source_type', $request->sourcetype_id)->latest()->value('case_number');
            if($lastCaseNumber == ''){

                $caseNumber = $firstThreeCharacters.'-'.$today.'-0001';

            }
            else{
                $lastNumberPart = (int)substr($lastCaseNumber, -4);
                $nextNumberPart = $lastNumberPart + 1;
                $caseNumber = $firstThreeCharacters.'-'.$today.'-'.str_pad($nextNumberPart, 4, '0', STR_PAD_LEFT);
            }
            return $caseNumber;
    }

    public function createDownloadTemplate(){
        
        $excelData = [];
        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->pluck('name')
        ->toArray();
    
        $uniqueItems = array_unique($evidenceTypes);
        $commaSeparatedString = implode(',', $uniqueItems);

        $firstRow = ['The evidence types should be the following :  ' . $commaSeparatedString];
        
        $additionalRowsData = [
            ['Sl.no', 'URL', 'Domain','IP','Registrar','Registry Details','Remarks','Ticket Number','Evidence Type','Source' ],
            ['1', 'https://forum.com', 'forum.com','192.0.2.16','GoDaddy','klkl','Site maintenance','TK0016','Instagram','Public'],
            ['2', 'https://abcd.com', 'abcd.com','192.2.2.16','sdsdds','rtrt','Site ghghg','TK0023','Website','Public'],
            ['3', 'https://dfdf.com', 'dfdf.com','192.3.2.16','bnnn','ghgh','ghgh gg','TK0052','Facebok','Open'],
         
        ];
        return Excel::download(new SampleExport($firstRow,$additionalRowsData), 'template.xlsx');
    } 

}
