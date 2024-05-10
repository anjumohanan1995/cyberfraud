<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use App\Models\Complaint;
use Illuminate\Http\Request;

class CaseDataController extends Controller
{
    public function index()
    {
        return view('dashboard.case-data-list.index');
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
        ## Read value.
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
        $totalRecord = Complaint::where('deleted_at', null)->orderBy('created_at', 'desc');
        $totalRecords = $totalRecord->select('count(*) as allcount')->count();


        $totalRecordswithFilte = Complaint::where('deleted_at', null)->orderBy('created_at', 'desc');
        $totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();

        // Fetch records.
        $items = Complaint::where('deleted_at', null)->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder);
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

            $edit = '<div><form action="' . url("case-data/bank-case-data") . '" method="GET"><input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '"><input type="hidden" name="account_id" value="' . $account_id . '"><button type="submit" class="btn btn-danger">Show Case</button></form></div>';

            $data_arr[] = array(
                "id" => $i,
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
