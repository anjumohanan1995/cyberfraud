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
use App\Models\Modus;
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
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use DateTime;
use MongoDB\BSON\ObjectId;



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
        // Initialize variables (this part remains largely unchanged)
        $draw = $request->get('draw');
        $start = (int)$request->get("start");
        $rowperpage = (int)$request->get("length");
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
        $acknowledgement_no = $request->get('acknowledgement_no');
        $filled_by = $request->get('filled_by');
        $search_by = $request->get('search_by');
        $options = $request->get('options');
        $com_status = $request->get('com_status');
        $fir_lodge = $request->get('fir_lodge');
        $filled_by_who = $request->get('filled_by_who');
        $transaction_id = $request->get('transaction_id');
        $account_id = $request->get('account_id');

        // Convert fromDate and toDate to start and end of the day
        $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDateEnd = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

        // Base pipeline
        $pipeline = [
            ['$match' => ['deleted_at' => null]]
        ];

        // Apply conditions
        if ($com_status == "1" || $com_status == "0") {
            $pipeline[0]['$match']['com_status'] = (int)$com_status;
        }

        if ($fromDateStart && $toDateEnd) {
            // $dump = var_dump(class_exists('MongoDB\BSON\UTCDateTime'));
            //  dd($dump);
            $pipeline[0]['$match']['entry_date'] = [
                '$gte' => new UTCDateTime($fromDateStart->timestamp * 1000),
                '$lte' => new UTCDateTime($toDateEnd->timestamp * 1000)
            ];
        }

        if (!empty($mobile)) {
            $pipeline[0]['$match']['complainant_mobile'] = (string)$mobile;
        }

        if (!empty($transaction_id)) {
            $pipeline[0]['$match']['transaction_id'] = ['$in' => [(string)$transaction_id, (string)$transaction_id]];
        }

        if (!empty($account_id)) {
            $pipeline[0]['$match']['account_id'] = (string)$account_id;
        }

        if (!empty($options) && $options != 'null') {
            $pipeline[0]['$match']['bank_name'] = $options;
        }

        if (!empty($acknowledgement_no)) {
            $pipeline[0]['$match']['acknowledgement_no'] = (int)$acknowledgement_no;
        }

        if (!empty($filled_by) && in_array($filled_by, ['citizen', 'cyber'])) {
            $pipeline[0]['$match']['entry_date'] = [
                '$gte' => new UTCDateTime(Carbon::now()->subDay()->startOfDay()->timestamp * 1000),
                '$lte' => new UTCDateTime(Carbon::now()->endOfDay()->timestamp * 1000)
            ];
            $pipeline[0]['$match']['acknowledgement_no'] = [
                '$gte' => $filled_by === 'citizen' ? 21500000000000 : 31500000000000,
                '$lte' => $filled_by === 'citizen' ? 21599999999999 : 31599999999999
            ];
        }

        if (!empty($filled_by_who) && in_array($filled_by_who, ['citizen', 'cyber'])) {
            $pipeline[0]['$match']['acknowledgement_no'] = [
                '$gte' => $filled_by_who === 'citizen' ? 21500000000000 : 31500000000000,
                '$lte' => $filled_by_who === 'citizen' ? 21599999999999 : 31599999999999
            ];
        }

        if (!empty($searchValue)) {
            $pipeline[0]['$match']['$or'] = [
                ['acknowledgement_no' => ['$regex' => $searchValue, '$options' => 'i']],
                ['district' => ['$regex' => $searchValue, '$options' => 'i']],
                ['complainant_name' => ['$regex' => $searchValue, '$options' => 'i']],
                ['bank_name' => ['$regex' => $searchValue, '$options' => 'i']],
                ['police_station' => ['$regex' => $searchValue, '$options' => 'i']],
                ['account_id' => ['$regex' => $searchValue, '$options' => 'i']],
                ['transaction_id' => ['$regex' => $searchValue, '$options' => 'i']],
                ['complainant_mobile' => ['$regex' => $searchValue, '$options' => 'i']],
                ['amount' => (int)$searchValue],
                // ['entry_date' => new UTCDateTime(new DateTime($searchValue))],
                ['current_status' => ['$regex' => $searchValue, '$options' => 'i']]
            ];
        }

        // FIR Lodge filter
        if ($fir_lodge == "1" || $fir_lodge == "0") {
            $ackNumbers = ComplaintAdditionalData::whereNotNull('fir_doc')->pluck('ack_no')->toArray();
            $pipeline[0]['$match']['acknowledgement_no'] = [
                $fir_lodge == "1" ? '$in' : '$nin' => array_map('intval', $ackNumbers)
            ];
        }

        // Grouping and sorting
        $pipeline[] = [
            '$group' => [
                '_id' => '$acknowledgement_no',
                'latest_entry_date' => ['$max' => '$entry_date'],
                'doc' => ['$first' => '$$ROOT']
            ]
        ];
        $pipeline[] = ['$replaceRoot' => ['newRoot' => '$doc']];
        $pipeline[] = ['$sort' => [$columnName => $columnSortOrder === 'desc' ? 1 : -1]];

        // Count total records
        $countPipeline = $pipeline;
        $countPipeline[] = ['$count' => 'total'];
        $totalRecords = Complaint::raw(function($collection) use ($countPipeline) {
            return $collection->aggregate($countPipeline);
        })->first()['total'] ?? 0;

        // Apply pagination
        $pipeline[] = ['$skip' => $start];
        $pipeline[] = ['$limit' => $rowperpage];

        // Execute the main query
        $records = Complaint::raw(function($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });

        // Fetch user permissions
        $user = Auth::user();
        $role = $user->role;
        $permission = RolePermission::where('role', $role)->first();
        $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
        $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
        $hasShowSelfAssignPermission = $user->role == 'Super Admin' || in_array('Self Assign', $sub_permissions);
        $hasShowActivatePermission = $user->role == 'Super Admin' || in_array('Activate / Deactivate', $sub_permissions);

        $data_arr = [];
        foreach ($records as $record) {
            $com = Complaint::where('acknowledgement_no', $record['acknowledgement_no'])->take(10)->get();

            $transaction_id = $amount = $bank_name = "";
            foreach ($com as $c) {
                $transaction_id .= $c->transaction_id . "<br>";
                $amount .= '<span class="editable" data-ackno="' . $record['acknowledgement_no'] . '" data-transaction="' . $c->transaction_id . '" >' . $c->amount . "</span><br>";
                $bank_name .= $c->bank_name . "<br>";
            }

            $id = Crypt::encrypt($record['acknowledgement_no']);
            $ack_no = '<a class="btn btn-outline-primary" href="' . route('case-data.view', ['id' => $id]) . '">' . $record['acknowledgement_no'] . '</a>';

            $edit = '';
            if ($hasShowActivatePermission) {
                $edit .= '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                    <input
                        data-id="' . $record['acknowledgement_no'] . '"
                        onchange="confirmActivation(this)"
                        class="form-check-input"
                        type="checkbox"
                        id="SwitchCheckSizesm' . $record['_id'] . '"
                        ' . ($record['com_status'] == 1 ? 'checked   title="Deactivate"' : '  title="Activate"') . '>
                 </div>';
            }

            if ($hasShowSelfAssignPermission) {
                $CUser = Auth::id();
                if (($record['assigned_to'] == $CUser) && ($record['case_status'] != null)) {
                    $edit .= '<div class="form-check form-switch1 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                        <div><p class="text-success"><strong>Case Status: ' . $record['case_status'] . '</strong></p>
                        <button class="btn btn-success" data-id="' . $record['acknowledgement_no'] . '" onClick="upStatus(this)" type="button">Update Status</button>
                        </div>
                    </div>';
                } elseif ($record['assigned_to'] == $CUser) {
                    $edit .= '<div class="form-check form-switch2 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                        <button class="btn btn-success" data-id="' . $record['acknowledgement_no'] . '" onClick="upStatus(this)" type="button">Update Status</button>
                    </div>';
                } elseif ($record['assigned_to'] == null) {
                    $edit .= '<div class="form-check form-switch3 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                        <form action="" method="GET">
                        <button data-id="' . $record['acknowledgement_no'] . '" onClick="selfAssign(this)" class="btn btn-warning btn-sm" type="button">Self Assign</button>
                        </form>
                    </div>';
                } else {
                    $user = User::find($record['assigned_to']);
                    if ($user != null) {
                        $edit .= '<p class="text-success"><strong>Case Status: ' . $record['case_status'] . '</strong></p>
                        <div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                        <p class="text-success">Assigned To: ' . $user->name . '</p>
                        </div>';
                    }
                }
            }

            $data_arr[] = [
                "id" => $start + 1,
                "acknowledgement_no" => $ack_no,
                "district" => $record['district'] . "<br>" . $record['police_station'],
                "complainant_name" => $record['complainant_name'] . "<br>" . $record['complainant_mobile'],
                "transaction_id" => $transaction_id,
                "bank_name" => $bank_name,
                "account_id" => $record['account_id'],
                "amount" => $amount,
                "entry_date" => $record['entry_date']->toDateTime()->format('d-m-Y H:i:s'),
                "current_status" => $record['current_status'],
                "date_of_action" => $record['date_of_action'],
                "action_taken_by_name" => $record['action_taken_by_name'],
                "edit" => $edit
            ];

            $start++;
        }

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }

    //     public function getDatalist(Request $request)
    //     {
    //         // Initialize variables
    //         $draw = $request->get('draw');
    //         $start = $request->get("start");
    //         $rowperpage = $request->get("length");
    //         $columnIndex_arr = $request->get('order');
    //         $columnName_arr = $request->get('columns');
    //         $order_arr = $request->get('order');
    //         $search_arr = $request->get('search');
    //         $columnIndex = $columnIndex_arr[0]['column'];
    //         $columnName = $columnName_arr[$columnIndex]['data'];
    //         $columnSortOrder = $order_arr[0]['dir'];
    //         $searchValue = $search_arr['value'];
    //         $fromDate = $request->get('from_date');
    //         $toDate = $request->get('to_date');
    //         $mobile = $request->get('mobile');
    //         $acknowledgement_no= $request->get('acknowledgement_no');
    //         $filled_by = $request->get('filled_by');
    //         $search_by = $request->get('search_by');
    //         $options = $request->get('options');
    //         $com_status = $request->get('com_status');
    //         // dd($com_status);
    //         $fir_lodge = $request->get('fir_lodge');
    //         $filled_by_who = $request->get('filled_by_who');
    //         $transaction_id = $request->get('transaction_id');
    //         // dd($transaction_id);
    //         $account_id = $request->get('account_id');

    //     // Convert fromDate and toDate to start and end of the day
    //     $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    //     $toDateEnd = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

    //     // Base query
    //     $query = Complaint::raw(function($collection) use (
    //         $com_status, $fromDateStart, $toDateEnd, $mobile, $transaction_id, $account_id,
    //         $options, $acknowledgement_no, $filled_by, $filled_by_who, $searchValue,
    //         $fir_lodge, $columnName, $columnSortOrder
    //     ) {
    //         $pipeline = [
    //             ['$match' => ['deleted_at' => null]]
    //         ];

    //         // Apply conditions
    //         if ($com_status == "1") {
    //             $pipeline[0]['$match']['com_status'] = 1;
    //         } elseif ($com_status == "0") {
    //             $pipeline[0]['$match']['com_status'] = 0;
    //         }

    //         if ($fromDateStart && $toDateEnd) {
    //             $pipeline[0]['$match']['entry_date'] = [
    //                 '$gte' => new MongoDB\BSON\UTCDateTime($fromDateStart->timestamp * 1000),
    //                 '$lte' => new MongoDB\BSON\UTCDateTime($toDateEnd->timestamp * 1000)
    //             ];
    //         }

    //         if (!empty($mobile)) {
    //             $pipeline[0]['$match']['complainant_mobile'] = (int)$mobile;
    //         }

    //         if (!empty($transaction_id)) {
    //             $pipeline[0]['$match']['transaction_id'] = ['$in' => [(int)$transaction_id, (string)$transaction_id]];
    //         }

    //         if (!empty($account_id)) {
    //             $pipeline[0]['$match']['account_id'] = (int)$account_id;
    //         }

    //         if (!empty($options) && $options != 'null') {
    //             $pipeline[0]['$match']['bank_name'] = $options;
    //         }

    //         if (!empty($acknowledgement_no)) {
    //             $pipeline[0]['$match']['acknowledgement_no'] = (int)$acknowledgement_no;
    //         }

    //         if (!empty($filled_by) && in_array($filled_by, ['citizen', 'cyber'])) {
    //             $pipeline[0]['$match']['entry_date'] = [
    //                 '$gte' => new MongoDB\BSON\UTCDateTime(Carbon::now()->subDay()->startOfDay()->timestamp * 1000),
    //                 '$lte' => new MongoDB\BSON\UTCDateTime(Carbon::now()->endOfDay()->timestamp * 1000)
    //             ];
    //             $pipeline[0]['$match']['acknowledgement_no'] = [
    //                 '$gte' => $filled_by === 'citizen' ? 21500000000000 : 31500000000000,
    //                 '$lte' => $filled_by === 'citizen' ? 21599999999999 : 31599999999999
    //             ];
    //         }

    //         if (!empty($filled_by_who) && in_array($filled_by_who, ['citizen', 'cyber'])) {
    //             $pipeline[0]['$match']['acknowledgement_no'] = [
    //                 '$gte' => $filled_by_who === 'citizen' ? 21500000000000 : 31500000000000,
    //                 '$lte' => $filled_by_who === 'citizen' ? 21599999999999 : 31599999999999
    //             ];
    //         }

    //         if (!empty($searchValue)) {
    //             $pipeline[0]['$match']['$or'] = [
    //                 ['acknowledgement_no' => ['$regex' => $searchValue, '$options' => 'i']],
    //                 ['district' => ['$regex' => $searchValue, '$options' => 'i']],
    //                 ['complainant_name' => ['$regex' => $searchValue, '$options' => 'i']],
    //                 ['bank_name' => ['$regex' => $searchValue, '$options' => 'i']],
    //                 ['police_station' => ['$regex' => $searchValue, '$options' => 'i']]
    //             ];
    //         }

    //         // FIR Lodge filter
    //         if ($fir_lodge == "1" || $fir_lodge == "0") {
    //             $ackNumbers = ComplaintAdditionalData::whereNotNull('fir_doc')->pluck('ack_no')->toArray();
    //             $pipeline[0]['$match']['acknowledgement_no'] = [
    //                 $fir_lodge == "1" ? '$in' : '$nin' => array_map('intval', $ackNumbers)
    //             ];
    //         }

    //         // Grouping and sorting
    //         $pipeline[] = [
    //             '$group' => [
    //                 '_id' => '$acknowledgement_no',
    //                 'latest_entry_date' => ['$max' => '$entry_date'],
    //                 'doc' => ['$first' => '$$ROOT']
    //             ]
    //         ];
    //         $pipeline[] = ['$replaceRoot' => ['newRoot' => '$doc']];
    //         $pipeline[] = ['$sort' => [$columnName => $columnSortOrder === 'asc' ? 1 : -1]];

    //         return $pipeline;
    //     });

    //     // Total records count
    //     $totalRecords = $query->count();

    //     // Fetch records
    //     $records = $query->skip($start)->take($rowperpage)->get();
    //                 //  ->map(function ($item) {
    //                 //     // Convert entry_date to Carbon instance
    //                 //     if (isset($item->entry_date)) {
    //                 //         $item->entry_date = Carbon::createFromFormat('d-m-Y, h:i A', $item->entry_date);
    //                 //     }
    //                 //     return $item;
    //                 // });


    // //    dd($records);




    //                          $user = Auth::user();
    //                                      $role = $user->role;
    //                                      $permission = RolePermission::where('role', $role)->first();
    //                                      $permissions = $permission && is_string($permission->permission) ? json_decode($permission->permission, true) : ($permission->permission ?? []);
    //                                      $sub_permissions = $permission && is_string($permission->sub_permissions) ? json_decode($permission->sub_permissions, true) : ($permission->sub_permissions ?? []);
    //                                      if ($sub_permissions || $user->role == 'Super Admin') {
    //                                          $hasShowSelfAssignPermission = in_array('Self Assign', $sub_permissions);
    //                                          $hasShowActivatePermission = in_array('Activate / Deactivate', $sub_permissions);
    //                                      } else{
    //                                              $hasShowSelfAssignPermission = $hasShowActivatePermission = false;
    //                                          }


    //         $data_arr = array();
    //         $i = $start;

    //         // $totalRecordswithFilter =  $totalRecords;

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

    //                 // $entry_date = new DateTime($com->entry_date);

    //                 $entry_date = $com->entry_date;
    //                 $entry_date = $entry_date->format('d-m-Y H:i:s');
    //                 // $entry_date = $date->format('l, F j, Y g:i A');
    //                 $current_status = $com->current_status;

    //                 // $date_of_action = new DateTime($com->date_of_action);
    //                 $date_of_action = $com->date_of_action;
    //                 // $date_of_action = $date_of_action->format('l, F j, Y g:i A');

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
    //             if ($hasShowActivatePermission) {
    //             $edit = '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
    //             <input
    //                 data-id="' . $acknowledgement_no . '"
    //                 onchange="confirmActivation(this)"
    //                 class="form-check-input"
    //                 type="checkbox"
    //                 id="SwitchCheckSizesm' . $com->id . '"
    //                 ' . ($com->com_status == 1 ? 'checked   title="Deactivate"' : '  title="Activate"') . '>
    //          </div>';
    //             }
    //          //dd($com);
    //          $CUser =Auth::user()->id;
    //             if($hasShowSelfAssignPermission) {
    //          if(($com->assigned_to == $CUser) && ($com->case_status != null)) {
    //             $edit.='<div class="form-check form-switch1 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
    //                 <div><p class="text-success"><strong>Case Status: '.$com->case_status.'</strong></p>
    //             <button  class="btn btn-success"  data-id="' . $acknowledgement_no . '" onClick="upStatus(this)" type="button">Update Status</button>
    // </div>
    //             </div>';
    //          }elseif($com->assigned_to == $CUser){
    //             $edit.='
    //             <div class="form-check form-switch2 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">

    //                 <button  class="btn btn-success"  data-id="' . $acknowledgement_no . '" onClick="upStatus(this)" type="button">Update Status</button>

    //                 </div>';
    //          } elseif($com->assigned_to == null) {
    //             $edit.= '<div class="form-check form-switch3 form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
    //                 <form action="" method="GET">
    //                 <button data-id="' . $acknowledgement_no . '" onClick="selfAssign(this)" class="btn btn-warning btn-sm" type="button">Self Assign</button>
    //                 </form>
    //                 </div>';
    //          } else {
    //             $user = User::find($com->assigned_to);
    //            // dd($user);
    //             if($user != null){
    //             $edit.= '<p class="text-success"><strong>Case Status: '.$com->case_status.'</strong></p>
    //             <div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
    //             <p class="text-success">Assigned To: '. $user->name.'</p>
    //             </div>';
    //         }
    //          }
    //         }

    //             $data_arr[] = array(
    //                 "id" => $i,
    //                 "acknowledgement_no" => $ack_no,
    //                 "district" => $district."<br>".$police_station,
    //                 "complainant_name" => $complainant_name."<br>".$complainant_mobile,
    //                 "transaction_id" => $transaction_id,
    //                 "bank_name" => $bank_name,
    //                 "account_id" => $account_id,
    //                 "amount" => $amount,
    //                 "entry_date" => $entry_date, // Use the formatted entry_date array here
    //                 "current_status" => $current_status,
    //                 "date_of_action" => $date_of_action,
    //                 "action_taken_by_name" => $action_taken_by_name,
    //                 "edit" => $edit
    //             );
    //         }

    //         $response = array(
    //             "draw" => intval($draw),
    //             "iTotalRecords" => $totalRecords,
    //             "iTotalDisplayRecords" => $totalRecords,
    //             "aaData" => $data_arr
    //         );

    //         return response()->json($response);
    //     }
    public function updateStatusOthers(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'caseNo' => 'required', // Validate that caseNo exists in the complaints table
        'status' => 'required', // Validate the status
    ]);

    $caseNo = $request->caseNo;
    $status = $request->status;
   // dd(Carbon::now());
    $current_date = new DateTime(Carbon::now());
    $formated_date = $current_date->format('Y-m-d');
    // Log the incoming request data
    Log::info('Received update status request', ['caseNo' => $caseNo, 'status' => $status]);

    try {
        // Update all complaints with the matching case_number
        $affected = ComplaintOthers::where('case_number', $caseNo)
        ->update(['case_status' => $status, 'status_changed'=>$formated_date]);

        if ($affected > 0) {
            Log::info('Complaints status updated successfully', ['caseNo' => $caseNo, 'status' => $status]);
            return response()->json(['message' => 'Case statuses updated successfully']);
        } else {
            Log::warning('No complaints found for caseNo', ['caseNo' => $caseNo]);
            return response()->json(['message' => 'No complaints found for caseNo'], 404);
        }
    } catch (\Exception $e) {
        // Log any exceptions that occur
        Log::error('An error occurred while updating complaint statuses', [
            'caseNo' => $caseNo,
            'status' => $status,
            'error' => $e->getMessage()
        ]);

        return response()->json(['message' => 'An error occurred while updating the statuses'], 500);
    }
}

public function updateStatus(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'ackno' => 'required|integer', // Validate that ackno exists and is an integer
        'status' => 'required|string', // Validate the status and ensure it is a string
    ]);

    $ackno = (int) $request->ackno;
    $status = $request->status;
    $current_date = new DateTime(Carbon::now());
    $formated_date = $current_date->format('Y-m-d');
    // Log the incoming request data
    Log::info('Received update status request', ['ackno' => $ackno, 'status' => $status]);

    try {

        // Update all complaints with the matching acknowledgement_no
        $affected = Complaint::where('acknowledgement_no', $ackno)
        ->update(['case_status' => $status, 'status_changed'=> $formated_date]);
//dd($affected);
        if ($affected > 0) {
            Log::info('Complaints status updated successfully', ['ackno' => $ackno, 'status' => $status]);
            return response()->json(['message' => 'Case statuses updated successfully']);
        } else {
            Log::warning('No complaints found for ackno', ['ackno' => $ackno]);
            return response()->json(['message' => 'No complaints found for ackno'], 404);
        }
    } catch (\Exception $e) {
        // Log any exceptions that occur
        Log::error('An error occurred while updating complaint statuses', [
            'ackno' => $ackno,
            'status' => $status,
            'error' => $e->getMessage()
        ]);

        return response()->json(['message' => 'An error occurred while updating the statuses'], 500);
    }
}



    public function detailsView(){
        return view('dashboard.case-data-list.index');
    }



    public function caseDataView(Request $request,$id){
        $id = Crypt::decrypt($id);
        $sum_amount=0;$hold_amount=0;$lost_amount=0;$pending_amount=0;

        $complaints = Complaint::with('bankCaseData')->get();

        $transaction_date = null;

        if ($id !== null) {
            $complaint = Complaint::where('acknowledgement_no', (int)$id)->first();
            if ($complaint) {
                $bankCaseData = $complaint->bankCaseData;
                if ($bankCaseData) {
                    $transaction_date = $bankCaseData->transaction_date;
                }
            }
        }

        $complaints = Complaint::where('acknowledgement_no',(int)$id)->get();


        $bank_datas = BankCasedata::where('acknowledgement_no',(int)$id)->get();
        $layer_one_transactions = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',1)->where('com_status',1)->get();

// ============================FOR FINDONG DESPUTED AMOUNT=======================================


function updateDisputeAmounts($parentLayer, $nextLayer, $id, &$updatedObjectIds) {
    // Retrieve all documents for the current parent layer
    $parents = BankCaseData::where('Layer', $parentLayer)
        ->where('acknowledgement_no', (int)$id)
        ->get();

    foreach ($parents as $parent) {
        // Retrieve child rows in the next layer
        $children = BankCaseData::where('Layer', $nextLayer)
            ->where('transaction_id_or_utr_no', 'like', '%' . $parent->transaction_id_sec)
            ->get();

        // Initialize capital amount with parent's transaction amount
        $capitalAmount = $parent->transaction_amount;

        foreach ($children as $child) {
            // Update the dispute_amount based on the current capital amount
            if ($capitalAmount <= 0) {
                // If capital amount is zero or negative, no need to process further
                break;
            }

            if ($child->transaction_amount < $capitalAmount) {
                // Child's amount is less than capital amount
                $disputeAmount = $child->transaction_amount;
                $capitalAmount -= $disputeAmount;
            } elseif ($child->transaction_amount == $capitalAmount) {
                // Child's amount equals the capital amount
                $disputeAmount = $capitalAmount;
                $capitalAmount = 0;
            } else {
                // Child's amount is greater than the capital amount
                //echo $child->transaction_amount."<br>";
                $disputeAmount = $capitalAmount;
                $capitalAmount = -1; // Set to negative to stop further processing
            }

            // Update the child's dispute_amount only if it hasn't been updated yet
            if (!isset($updatedObjectIds[$child->_id])) {

                $child->dispute_amount = $disputeAmount;
                $child->save();
                // Mark child as updated
                $updatedObjectIds[$child->_id] = true;
            }
        }

        // Mark parent as updated if it's not already updated
        if (!isset($updatedObjectIds[$parent->_id])) {

            $updatedObjectIds[$parent->_id] = true;
        }
    }
}

// Initialize and process records for the base layer (Layer 1)
$updatedObjectIds = [];
$records = BankCaseData::where('acknowledgement_no', (int)$id)
    ->where('Layer', 1)
    ->where('com_status', 1)
    ->get();

foreach ($records as $record) {
    // Skip updating if already updated
    if (!isset($updatedObjectIds[$record->_id])) {
        $record->dispute_amount = $record->transaction_amount;
        $record->save();
        // Mark record as updated
        $updatedObjectIds[$record->_id] = true;
    }
}

// Process each layer iteratively
$currentLayer = 1;

while (BankCaseData::where('Layer', $currentLayer)->where('acknowledgement_no', (int)$id)->exists()) {
    $nextLayer = $currentLayer + 1;

    if (BankCaseData::where('Layer', $nextLayer)->where('acknowledgement_no', (int)$id)->exists()) {

        updateDisputeAmounts($currentLayer, $nextLayer, (int)$id, $updatedObjectIds);
    }

    $currentLayer = $nextLayer;
}


//================================FOR FINDING DESPUTE AMOUNT====================================

$sum_amount = Complaint::where('acknowledgement_no', (int)$id)->where('com_status',1)->sum('amount');
$hold_amount = BankCaseData::where('acknowledgement_no', (int)$id)->where('com_status',1)
->where('action_taken_by_bank','transaction put on hold')->sum('transaction_amount');
//dd($hold_amount );
// $lost_amount = BankCaseData::where('acknowledgement_no', (int)$id)->where('com_status',1)
//                             ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos'])
//                             ->sum('transaction_amount');
$lost_amount = BankCaseData::where('acknowledgement_no', (int)$id)->where('com_status',1)
                            ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos' , 'aadhaar enabled payment System'])
                            ->sum('dispute_amount');

$pending_amount = $sum_amount - $hold_amount - $lost_amount;

        $transaction_based_array_final = [];$final_array=[];
        for($i=0;$i<count($layer_one_transactions);$i++){
            // dd($layer_one_transactions[$i]);
             $layer = 1;
             $transaction_id_sec = $layer_one_transactions[$i]->transaction_id_sec;
             $first_row = BankCaseData::where('acknowledgement_no', $id)
             ->where('transaction_id_sec', $transaction_id_sec)
             ->get()
             ->toArray();


             $processed_ids = [];
             $transaction_baed_array = [];
             if($first_row){

                $transaction_baed_array =  $this->checkifempty($layer,$first_row,$id,$processed_ids);

             }

                  $final_array = array_merge($final_array,$transaction_baed_array);


         }
        //dd($final_array);
        $additional = ComplaintAdditionalData::where('ack_no', (string)$id)->first();

       // $transaction_numbers_layer1 = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',1)->get();
        $layers = BankCasedata::where('acknowledgement_no',(int)$id)->groupBy('Layer')->pluck('Layer');
        $pending_banks_array = [];
        for($i=1;$i<=count($layers);$i++){

            // $transaction_numbers_left_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)->where('com_status',1)->pluck('transaction_id_or_utr_no');

            $transaction_numbers_right_side="";$transaction_numbers_left_side="";$transaction_numbers_left_side_array="";
            $transaction_numbers_left_side_array_final="";

            $transaction_numbers_right_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)->where('com_status',1)
                                                            ->where('action_taken_by_bank','money transfer to')
                                                            ->where('bank','!=','Others')->get();

            ++$i;

            $transaction_numbers_left_side = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$i)->pluck('transaction_id_or_utr_no');
             //dd($transaction_numbers_left_side);

            $transaction_numbers_left_side_array = explode(" ",$transaction_numbers_left_side);

            $mergedArray = [];

            foreach ($transaction_numbers_left_side_array as $item) {

                $values = explode(',', trim($item, '[]'));
                $values = array_map('trim', $values);
                $mergedArray = array_merge($mergedArray, $values);
            }

            $transaction_numbers_left_side_array_final = [];

            foreach ($mergedArray as $value) {
                $cleanedValue = trim($value, '"');
                $transaction_numbers_left_side_array_final[] = $cleanedValue;
            }

            foreach ($transaction_numbers_right_side as $tn){
                if($tn->transaction_id_sec){
                    if (!in_array($tn->transaction_id_sec, $transaction_numbers_left_side_array_final)) {
                        $j=$i-1;
                        $pending_banks_array[] = array(
                            "pending_banks" => $tn->bank,
                            "transaction_id" => $tn->transaction_id_sec,
                            "transaction_amount" => $tn->transaction_amount,
                            "desputed_amount" => $tn->desputed_amount
                        );

                     }


                }


             }
             --$i;
        }

     $groupedData = [];
     $finalData_pending_banks=[];
    foreach ($pending_banks_array as $item) {
    $pendingBank = $item['pending_banks'];
    $transactionId = $item['transaction_id'];
    $transactionAmount = $item['transaction_amount'];
    $desputedAmount = $item['desputed_amount'];

    if (!isset($finalData_pending_banks)) {
        $finalData_pending_banks = [];
    }

    $finalData_pending_banks[] = ['pending_banks' => $pendingBank, 'transaction_id' => $transactionId , 'transaction_amount'=> $transactionAmount, 'desputed_amount' => $desputedAmount];
    }

    // dd($finalData_pending_banks);

 $finalData_pending_banks = collect($finalData_pending_banks)->groupBy('pending_banks')->map(function ($group) {
        return [
            'pending_banks' => $group->first()['pending_banks'],
            'transaction_id'=> $group->count(),
            'transaction_amount' => $group->sum('transaction_amount'),
            'desputed_amount' => $group->first()['desputed_amount']

        ];
    })->values()->all();

        $professions = Profession::where('status', 'active')
        ->whereNull('deleted_at')
        ->get();

        $modus = Modus::where('status','1')
        ->whereNull('deleted_at')
        ->get();

        return view('dashboard.case-data-list.details',compact('complaint','complaints','final_array','sum_amount','additional','professions','modus','finalData_pending_banks','hold_amount','lost_amount','pending_amount','transaction_date'));
    }

    public function updateTransactionAmount(Request $request)
    {
        // Get the input values
        $transaction_amount = $request->transaction_amount;
        $transaction_id = $request->transaction_id;
        $pending_banks = $request->pending_banks;
        // dd($transaction_amount);

        // Update the document in the BankCasedata collection
        $updateResult = BankCasedata::where('transaction_id_or_utr_no', $transaction_id)
                                    ->where('bank', $pending_banks)
                                    ->update(['desputed_amount' => $transaction_amount]);

        if ($updateResult) {
            return response()->json(['success' => true, 'message' => 'Transaction amount updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'No matching record found or update failed.']);
        }
    }


    public function checkifempty($layer, $first_rows, $id, &$processed_ids = [])
    {
        $layer++;

        $main_array = [];


        foreach ($first_rows as $first_row) {


            if($first_row['transaction_id_sec']!=null){
                if (in_array($first_row['transaction_id_sec'], $processed_ids)) {
                    continue; // Skip processing if already processed
                }
            }

            // Add current transaction_id_sec to processed list
            $processed_ids[] = $first_row['transaction_id_sec'];
            //dd($processed_ids);

            // Add current first row to main array
            $main_array[] = $first_row;

            $next_layer_rows = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$layer)->where('transaction_id_or_utr_no','like','%'.$first_row['transaction_id_sec'])->get()->toArray();

            if (!empty($next_layer_rows)) {

                if ($first_row['transaction_id_sec'] === null) {
                    continue;
                }

                $nested_results = $this->checkifempty($layer, $next_layer_rows, $id, $processed_ids);

                $main_array = array_merge($main_array, $nested_results);

            }
        }

        return $main_array;
    }

    public function change_status_layerwise($layer, $first_rows, $id, &$processed_ids = [] ,$status )
    {
        $layer++;

        $main_array = [];

        foreach ($first_rows as $first_row) {
           // dd($first_row['transaction_id_sec']);
        if($first_row['transaction_id_sec']!=null){
                if (in_array($first_row['transaction_id_sec'], $processed_ids)) {
                    continue;
                }
            }
            $res =  BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$layer-1)->where('transaction_id_sec',$first_row['transaction_id_sec'])->update([
                'com_status' => $status,
            ]);

            $processed_ids[] = $first_row['transaction_id_sec'];
          // dd($first_row['transaction_id_sec']);
            $main_array[] = $first_row;

            $next_layer_rows = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',$layer)->where('transaction_id_or_utr_no','like','%'.$first_row['transaction_id_sec'])->get()->toArray();

            if (!empty($next_layer_rows)){

                if ($first_row['transaction_id_sec'] === null) {
                    continue;
                }


                $nested_results = $this->change_status_layerwise($layer, $next_layer_rows, $id, $processed_ids , $status);

                $main_array = array_merge($main_array, $nested_results);

            }
        }

        return $main_array;
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
    public function AssignedTo(Request $request)
    {
//dd($request->all());
        $UserId = $request->userid;
        $ackno = (int) $request->acknowledgement_no;

        Complaint::where('acknowledgement_no', $ackno)
                 ->update(['assigned_to' => $UserId]);


        return response()->json(['status'=>'Self Assigned.']);
    }

    public function AssignedToOthers(Request $request)
    {
//dd($request->all());
        $UserId = $request->userid;
        $caseNo = $request->caseNo;

        ComplaintOthers::where('case_number', $caseNo)
                 ->update(['assigned_to' => $UserId]);


        return response()->json(['status'=>'Self Assigned.']);
    }
    public function activateLinkIndividual(Request $request)
    {

      //  $id = Crypt::decrypt($request->com_id);
        $com_id =$request->com_id;
        $transaction_id_sec = $request->transaction_id_sec;
        $status = (int) $request->status;
        $ackid = (int)$request->ackno;

        $res = Complaint::where('_id', $com_id)
                 ->update(['com_status' => $status]);

        $layer = 1;
        $transaction_id_sec = $transaction_id_sec;

        $first_row = BankCaseData::where('acknowledgement_no', $ackid)
        ->where('transaction_id_sec', $transaction_id_sec)
        ->get()
        ->toArray();

        $processed_ids = [];
        $transaction_baed_array = [];

        if($first_row){
            // $res =  BankCasedata::where('acknowledgement_no',$ackid)->where('Layer',$layer)->where('transaction_id_sec',$first_row[0]['transaction_id_sec'])->update([
            //     'com_status' => $status,
            // ]);

        $res_bank_case_data =  $this->change_status_layerwise($layer,$first_row,$ackid,$processed_ids,$status);

        }
        //return response()->json(['success'=>true]);

        if($res_bank_case_data){
            return response()->json(['success'=>true]);
        }
        else{
            return response()->json(['success'=>false]);
        }


    }

    public function activateLinkIndividualOthers(Request $request)
    {

        //$id = Crypt::decrypt($request->com_id);
        $case_id =$request->case_id;
        $status = (int) $request->status;

        ComplaintOthers::where('_id', $case_id)
                 ->update(['status' => $status]);


        return response()->json(['status'=>'Status changed successfully.']);
    }

    public function caseDataOthers(){
        $source=SourceType::get();
        //dd($source);
       return view('dashboard.case-data-list.case-data-list-others', compact('source'));
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
        // dd($searchValue);
        $source_types = SourceType::all();
        $casenumber = $request->casenumber;
        $domain = $request->domain;
        $url = $request->url;
        $registrar = $request->registrar;
        $ip = $request->ip;
        $source_type = $request->source_type;

        // dd($source_type);
        // dd($searchValue, $casenumber, $url, $domain, $registrar, $ip);
        $pipeline = [];

        // Build the $match stage for search filters
        $matchStage = [];

        if (!empty($searchValue)) {
            $matchStage['$or'] = [
                ['case_number' => ['$regex' => $searchValue, '$options' => 'i']],
                ['url' => ['$regex' => $searchValue, '$options' => 'i']],
                ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
                ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
                ['remarks' => ['$regex' => $searchValue, '$options' => 'i']],
                ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
                ['source.name' => ['$regex' => $searchValue, '$options' => 'i']]  // Search by source name
            ];
        }

        // Add additional match conditions for filters
        if (isset($casenumber)) {
            $matchStage['case_number'] = $casenumber;
        }
        //need to check status is 1
        if (isset($url)) {
            $matchStage['url'] = $url;
        }
        if (isset($domain)) {
            $matchStage['domain'] = $domain;
        }
        if (isset($registrar)) {
            $matchStage['registrar'] = $registrar;
        }
        if (isset($ip)) {
            $matchStage['ip'] = $ip;
        }
        if (isset($source_type)) {
            $matchStage['source_type'] = $source_type;
        }

        if (!empty($matchStage)) {
            $pipeline[] = ['$match' => $matchStage];
        }

        // Add the $lookup stage to join sourcetype with complaint_others based on the source_type field
        $pipeline[] = [
            '$lookup' => [
                'from' => 'sourcetype',  // Name of the sourcetype collection
                'localField' => 'source_type',  // Field in complaint_others
                'foreignField' => '_id',  // Field in sourcetype
                'as' => 'source'
            ]
        ];

        // Unwind the source array to flatten the results
        $pipeline[] = [
            '$unwind' => [
                'path' => '$source',
                'preserveNullAndEmptyArrays' => true
            ]
        ];

        // Group the results by case_number and aggregate other fields
        $pipeline[] = [
            '$group' => [
                '_id' => '$case_number',
                'source_type' => ['$addToSet' => '$source_type'],
                'source_name' => ['$first' => '$source.name'],  // Group source name from sourcetype
                'url' => [
                    '$addToSet' => [
                        '$cond' => [
                            'if' => ['$eq' => ['$status', 1]],
                            'then' => '$url',
                            'else' => null
                        ]
                    ]
                ],
                'domain' => [
                    '$addToSet' => [
                        '$cond' => [
                            'if' => ['$eq' => ['$status', 1]],
                            'then' => '$domain',
                            'else' => null
                        ]
                    ]
                ],
                'ip' => [
                    '$addToSet' => [
                        '$cond' => [
                            'if' => ['$eq' => ['$status', 1]],
                            'then' => '$ip',
                            'else' => null
                        ]
                    ]
                ],
                'registrar' => [
                    '$addToSet' => [
                        '$cond' => [
                            'if' => ['$eq' => ['$status', 1]],
                            'then' => '$registrar',
                            'else' => null
                        ]
                    ]
                ],
                'registry_details' => ['$addToSet' => '$registry_details'],
                'remarks' => ['$addToSet' => '$remarks'],
                'assigned_to' => ['$first' => '$assigned_to'],
                'case_status' => ['$first' => '$case_status'],
                'status' => ['$first' => '$status'],
            ]
        ];



        // Sort stage (optional)
        $pipeline[] = ['$sort' => ['_id' => 1]];

        // Pagination stages
        $pipeline[] = ['$skip' => (int)$start];
        $pipeline[] = ['$limit' => (int)$rowperpage];

        // Execute the aggregation query
        $complaints = ComplaintOthers::raw(function($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });


        $distinctCaseNumbers = ComplaintOthers::raw(function($collection) use ($casenumber, $url, $domain, $registrar, $ip, $source_type) {
            $pipeline = [];


            // Build the $match stage
            $matchStage = [];

            if (!empty($casenumber)) {
                $matchStage['case_number'] = $casenumber;
            }
            if (!empty($url)) {
                $matchStage['url'] = $url;
            }
            if (!empty($domain)) {
                $matchStage['domain'] = $domain;
            }
            if (!empty($registrar)) {
                $matchStage['registrar'] = $registrar;
            }
            if (!empty($ip)) {
                $matchStage['ip'] = $ip;
            }
            if (!empty($source_type)) {
                $matchStage['source_type'] = $source_type;
            }

            if (!empty($matchStage)) {
                $pipeline[] = ['$match' => $matchStage];
            }

            // Group by case_number
            $pipeline[] = [
                '$group' => [
                    '_id' => '$case_number'
                ]
            ];

            // Execute the aggregation pipeline
            return $collection->aggregate($pipeline);
        });



        //dd($complaints);
        //  dd($distinctCaseNumbers);



        $totalRecords = count($distinctCaseNumbers);
        $data_arr = array();
        $i = $start;
        // dd($totalRecords);


        $totalRecordswithFilter =  $totalRecords;
        foreach($complaints as $record){
         //dd($record);
            $i++;
            $url = "";$domain="";$ip="";$registrar="";$remarks=""; $source_type="";

            $case_number = '<a href="' . route('other-case-details', ['id' => Crypt::encryptString($record->_id)]) . '">'.$record->_id.'</a>';

            // foreach ($record->url as $item) {
            //     $url .= $item."<br>";
            // }
//dd($record->status);
            if($record->status === 1) { // Check if status is 1
               // dd($record->url);
                foreach ($record->url as $item) {
                    $url .= $item."<br>";
                }
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
            $caseNo = $record->_id;
            //dd($caseNo);
            $CUser =Auth::user()->id;
                    //dd($record);
            if(($record->assigned_to == $CUser) && ($record->case_status != null)) {
                $edit='<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                            <div><p class="text-success"><strong>Case Status: '.$record->case_status.'</strong></p>
                            <button  class="btn btn-success"  data-id="' . $caseNo . '" onClick="upStatus(this)" type="button">Update Status</button>
                            </div>
                        </div>';
            }elseif($record->assigned_to == $CUser){

                $edit='<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                    <button  class="btn btn-success"  data-id="' . $caseNo . '" onClick="upStatus(this)" type="button">Update Status</button>
                    </div>';
            } elseif($record->assigned_to == null) {
                            //dd($casenumber);
                    $edit= '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                               <form action="" method="GET">
                               <button data-id="' . $caseNo. '" onClick="selfAssign(this)" class="btn btn-warning btn-sm" type="button">Self Assign</button>
                               </form>
                               </div>';
                        } else {
                           $user = User::find($record->assigned_to);
                          // dd($user);
                           if($user != null){
                        if($record->case_status != null){
                            $edit = '<p class="text-success"><strong>Case Status: '.$record->case_status.'</strong></p>';
                        }
                           $edit .= '<div class="form-check form-switch form-switch-sm d-flex justify-content-center align-items-center" dir="ltr">
                           <p class="text-success">Assigned To: '. $user->name.'</p>
                           </div>';
                        }
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
                    "action" => $edit
                    );

        }
//dd($data_arr);
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
        $complaint->modus=$request->modus;
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
            ['3', 'https://dfdf.com', 'dfdf.com','192.3.2.16','bnnn','ghgh','ghgh gg','TK0052','Facebook','Open'],

        ];
        return Excel::download(new SampleExport($firstRow,$additionalRowsData), 'template.xlsx');
    }


}

