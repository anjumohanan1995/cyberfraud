<?php

namespace App\Http\Controllers;
use App\Models\SourceType;

use Maatwebsite\Excel\Facades\Excel;
use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Complaint;
use App\Models\BankCasedata;
use App\Models\ComplaintOthers;
use App\Models\EvidenceType;
use App\Models\Evidence;
use App\Exports\ComplaintExport;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Crypt;
use MongoDB\Client;
use DateTime;


class ReportsController extends Controller
{
    public function index()
    {

        $evidenceTypes = EvidenceType::where('status', 'active')
                             ->whereNull('deleted_at')
                             ->get();

        $getevidencename = ComplaintOthers::select('evidence_type')
                             ->groupBy('evidence_type')
                             ->get();

        $lowercaseEvidences = $getevidencename->map(function ($item) {
            return strtolower($item->evidence_type);
        });
                            //  dd($lowercaseEvidences);

        return view("dashboard.reports.index", compact('evidenceTypes','lowercaseEvidences'));
    }


    public function getDatalistNcrp(Request $request)
    {

        // Extract the format from the request parameters
        $format = $request->query('format');

        // Get request parameters
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page.

        $searchValue = $request->get('search')['value'] ?? '';

        // Custom filters
        $dailyDate = $request->get('daily_date');
        //dd($dailyDate);
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $current_date = $request->get('current_date');
        // dd($current_date);
        $bank_action_status = $request->get('bank_action_status');
        $evidence_type_ncrp = $request->get('evidence_type_ncrp');
        $search_value_ncrp = $request->get('search_value_ncrp');
        // dd($evidence_type_ncrp);
        $normalizedBankActionStatus = strtolower(trim($bank_action_status));

        $normalizedBankActionStatus = preg_replace('/\s+/', '', $normalizedBankActionStatus);
        // dd($normalizedBankActionStatus);
        $query = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null);
// dd($query);
        if ($fromDate && $toDate) {
            $query->whereBetween('entry_date', [Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay(), Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay()]);
        }

        // New filter condition for current_date = "today"
if ($current_date === 'today') {
    $todayStart = Carbon::now()->startOfDay();
    $todayEnd = Carbon::now()->endOfDay();

    $query->whereBetween('entry_date', [$todayStart, $todayEnd]);

    // dd($todayEnd);
}

if ($dailyDate) {
    $formattedDate = Carbon::createFromFormat('Y-m-d', $dailyDate)->format('d-m-Y');

    $acknowledgementNos = BankCasedata::where('transaction_date', 'like', '%' . $dailyDate . '%')
                                      ->pluck('acknowledgement_no');

    if ($acknowledgementNos->isNotEmpty()) {
        $query->whereIn('acknowledgement_no', $acknowledgementNos);
        dd($query);
    } else {
        // If no acknowledgement numbers are found, apply an empty filter to return no results
        $query->whereIn('acknowledgement_no', []);
    }
}


// if ($dailyDate) {

//     $date = Carbon::parse($dailyDate);
//     //dd($date);
//     $startOfDay = $date->startOfDay();
//     $endOfDay = $date->endOfDay();
//     //            dd($endOfDay);

//     $query->whereBetween('entry_date', [$startOfDay, $endOfDay]);
// }
// Filter based on bank_action_status
if ($normalizedBankActionStatus) {
    $acknowledgementNos = BankCasedata::whereRaw([
        '$expr' => [
            '$regexMatch' => [
                'input' => ['$replaceAll' => ['input' => ['$toLower' => '$action_taken_by_bank'], 'find' => ' ', 'replacement' => '']],
                'regex' => $normalizedBankActionStatus,
                'options' => 'i'
            ]
        ]
    ])->pluck('acknowledgement_no');

    $query->whereIn('acknowledgement_no', $acknowledgementNos);
}

// Filter based on evidence_type_ncrp and search_value_ncrp
if ($evidence_type_ncrp || $search_value_ncrp) {
    // Initialize filtered acknowledgment numbers
    $filteredAckNos = [];

    // If evidence type and search value are both provided
    if ($evidence_type_ncrp && $search_value_ncrp) {
        // Retrieve acknowledgment numbers based on both filters
        $filteredAckNos = Evidence::where('evidence_type_id', $evidence_type_ncrp)
                                  ->where(function ($query) use ($search_value_ncrp) {
                                      $query->where('url', 'like', '%' . $search_value_ncrp . '%')
                                            ->orWhere('mobile', 'like', '%' . $search_value_ncrp . '%');
                                  })
                                  ->pluck('ack_no');
    } elseif ($evidence_type_ncrp) {
        // If only evidence type is provided
        $filteredAckNos = Evidence::where('evidence_type_id', $evidence_type_ncrp)
                                  ->pluck('ack_no');
    } elseif ($search_value_ncrp) {
        // If only search value is provided
        $filteredAckNos = Evidence::where(function ($query) use ($search_value_ncrp) {
                                      $query->where('url', 'like', '%' . $search_value_ncrp . '%')
                                            ->orWhere('mobile', 'like', '%' . $search_value_ncrp . '%');
                                  })
                                  ->pluck('ack_no');
    }

    if ($filteredAckNos->isNotEmpty()) {

                    // Extract acknowledgment numbers from collection
            $ackNumbers = $filteredAckNos->map(function ($ackNumber) {
                return (int) $ackNumber;
            })->toArray();
            // dd($ackNumbers);

            $query->whereIn('acknowledgement_no', $ackNumbers);

            } else {
                // If no acknowledgment numbers are found, apply an empty filter to return no results
                $query->whereIn('acknowledgement_no', []);
            }
        }



// // Filter based on evidence_type_ncrp and search_value_ncrp
// if ($evidence_type_ncrp || $search_value_ncrp) {
//     // Initialize filtered acknowledgment numbers
//     $filteredAckNos = [];

//     // If evidence type and search value are both provided
//     if ($evidence_type_ncrp && $search_value_ncrp) {
//         // Retrieve acknowledgment numbers based on both filters
//         $filteredAckNos = Evidence::where('evidence_type_id', $evidence_type_ncrp)
//                                   ->where('url', 'like', '%' . $search_value_ncrp . '%')
//                                   ->pluck('ack_no');
//                                 //   dd($filteredAckNos);
//     } elseif ($evidence_type_ncrp) {
//         // If only evidence type is provided
//         $filteredAckNos = Evidence::where('evidence_type_id', $evidence_type_ncrp)
//                                   ->pluck('ack_no');
//     } elseif ($search_value_ncrp) {
//         // If only search value is provided
//         $filteredAckNos = Evidence::where('url', 'like', '%' . $search_value_ncrp . '%')
//                                   ->pluck('ack_no');
//     }

//     // Total records count with filter
//     // $totalRecords = 0;
//     // $q = " ";

//     if ($filteredAckNos->isNotEmpty()) {

//             // Extract acknowledgment numbers from collection
//     $ackNumbers = $filteredAckNos->map(function ($ackNumber) {
//         return (int) $ackNumber;
//     })->toArray();
//     // dd($ackNumbers);

//     $query->whereIn('acknowledgement_no', $ackNumbers);

//     } else {
//         // If no acknowledgment numbers are found, apply an empty filter to return no results
//         $query->whereIn('acknowledgement_no', []);
//     }
// }

if (!empty($searchValue)) {
    $query->where(function ($q) use ($searchValue) {
        // Check if the search value is numeric
        if (is_numeric($searchValue)) {
            $numericValue = $searchValue + 0; // Converts to int or float automatically
            $q->orWhere('acknowledgement_no', (int)$numericValue)
              ->orWhere('amount', $numericValue);
        }

        // String-based searches
        $q->orWhere('district', 'like', '%' . $searchValue . '%')
          ->orWhere('complainant_name', 'like', '%' . $searchValue . '%')
          ->orWhere('complainant_mobile', 'like', '%' . $searchValue . '%')
          ->orWhere('transaction_id', 'like', '%' . $searchValue . '%')
          ->orWhere('account_id', 'like', '%' . $searchValue . '%')
          ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
          ->orWhere('police_station', 'like', '%' . $searchValue . '%');
    });
}

                // Total records count
                $totalRecords = $query->get()->count();
                // dd($totalRecords);
                // $records_action = $query->get();
                // dd($records_action);
                // Fetch records
                $records = $query->orderBy('created_at', 'desc')
                                 ->orderBy('acknowledgement_no', 'asc')
                                 ->skip($start)
                                 ->take($rowperpage)
                                 ->get();



        if ($format === 'ncrp') {

        $data_arr = array();
        $i = $start;
        $action_taken_by_bank = "";
        foreach ($records as $record){
            // echo($record->acknowledgement_no);
            $com = Complaint::where('acknowledgement_no',$record->acknowledgement_no)->take(10)->get();
            $action = BankCasedata::where('acknowledgement_no', $record->acknowledgement_no)->get();
            // dd($action);
            $evidences = Evidence::where('ack_no', (string)$record->acknowledgement_no)->take(10)->get(['evidence_type', 'url','mobile']);

            $i++;
            $id = $record->id;
            $source_type = $record->source_type;
            $acknowledgement_no = $record->acknowledgement_no;

            $transaction_id="";$amount="";$bank_name="";$evidence_type="";$url="";
            foreach($com as $com){
                $transaction_id .= $com->transaction_id."<br>";
                // $amount .= '<span class="editable" data-ackno="'.$record->acknowledgement_no.'" data-transaction="'.$com->transaction_id.'" >'.$com->amount."</span><br>";
                $amount .= $com->amount . "<br>";
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
            }

// // Process BankCasedata (assuming potentially multiple records)
// $action_taken_by_bank = "";
// print_r($action);
// foreach ($action as $act) {
//     $action_taken_by_bank .= $act["action_taken_by_bank"] . "<br>"; // Assuming $act is an array
//     // dd($action_taken_by_bank);
// }

            foreach ($evidences as $evidence) {
                $evidence_type .= $evidence->evidence_type."<br>";// Ensure the correct property name
                if (isset($evidence->url)) {
                    $url .= $evidence->url."<br>";
                } elseif (isset($evidence->mobile)) {
                    $url .= $evidence->mobile."<br>";
                }

            }

            $countMoneyTransferTo = 0;
            $countCashWithdrawal = 0;
            $countTransactionPutOnHold = 0;
            $countOther = 0;
            $countwithdrawalThroughAtm  = 0;
            $countWithdrawalThroughPos = 0;
            $countWrongTransaction = 0;
            $countClosedComplaint = 0;
            $countOldTransaction = 0;
            $action_taken_by_bank = "";

            foreach ($action as $bankData) {
                // Check if $normalizedBankActionStatus matches specific conditions
                if ($normalizedBankActionStatus == "moneytransferto" && $bankData->action_taken_by_bank == "money transfer to") {
                    if ($countMoneyTransferTo < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countMoneyTransferTo++;
                    }
                } elseif ($normalizedBankActionStatus == "cashwithdrawalthroughcheque" && $bankData->action_taken_by_bank == "cash withdrawal through cheque") {
                    if ($countCashWithdrawal < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countCashWithdrawal++;
                    }
                } elseif ($normalizedBankActionStatus == "transactionputonhold" && $bankData->action_taken_by_bank == "transaction put on hold") {
                    if ($countTransactionPutOnHold < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countTransactionPutOnHold++;
                    }
                } elseif ($normalizedBankActionStatus == "other" && $bankData->action_taken_by_bank == "other") {
                    if ($countOther < 10) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countOther++;
                    }
                } elseif ($normalizedBankActionStatus == "withdrawalthroughatm" && $bankData->action_taken_by_bank == "withdrawal through atm") {
                    if ($countwithdrawalThroughAtm < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countwithdrawalThroughAtm++;
                    }
                } elseif ($normalizedBankActionStatus == "withdrawalthroughpos" && $bankData->action_taken_by_bank == "withdrawal through pos") {
                    if ($countWithdrawalThroughPos < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countWithdrawalThroughPos++;
                    }
                } elseif ($normalizedBankActionStatus == "wrongtransaction" && $bankData->action_taken_by_bank == "wrong transaction") {
                    if ($countWrongTransaction < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countWrongTransaction++;
                    }
                } elseif ($normalizedBankActionStatus == "closedcomplaint" && $bankData->action_taken_by_bank == "closed complaint") {
                    if ($countClosedComplaint < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countClosedComplaint++;
                    }
                } elseif ($normalizedBankActionStatus == "oldtransaction" && $bankData->action_taken_by_bank == "old transaction") {
                    if ($countOldTransaction < 1) {
                        $action_taken_by_bank .= $bankData->action_taken_by_bank . "<br>"; // Concatenate action_taken_by_bank values
                        $countOldTransaction++;
                    }
                }

                // Break the loop if both conditions have reached 10 entries
                if ($countMoneyTransferTo >= 10 && $countCashWithdrawal >= 10 && $countTransactionPutOnHold >= 10 && $countOther >= 10 && $countwithdrawalThroughAtm >= 10 && $countWithdrawalThroughPos >= 10 && $countWrongTransaction >= 10 && $countClosedComplaint >= 10 && $countOldTransaction  >= 10) {
                    break;
                }
            }
            // dd($url);
            // $ack_no ='<form action="' . route('case-data.view') . '" method="POST">' .
            // '<input type="hidden" name="_token" value="' . csrf_token() . '">' . // Add CSRF token
            // '<input type="hidden" name="acknowledgement_no" value="' . $acknowledgement_no . '">' . // Hidden field for the acknowledgment number
            // '<button class="btn btn-outline-success" type="submit">' . $acknowledgement_no . '</button>' . // Submit button with the acknowledgment number as text
            // '</form>';
            $id = Crypt::encrypt($acknowledgement_no);
            $ack_no = '<a class="btn btn-outline-primary" target="_blank" href="' . route('case-data.view', ['id' => $id]) . '">' . $acknowledgement_no . '</a>';
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
                'action_taken_by_bank' => $action_taken_by_bank,
                'action_taken_by_name' => $action_taken_by_name,
                "evidence_type" => $evidence_type,
                "url" => $url,


                // "edit" => $edit
            );
        }
        // dd();
    }

    if ($format === 'csv' || $format === 'excel') {



        $data_arr = [];
        $acknowledgementNumbers = [];
        $start = 0; // Assuming $start is defined somewhere
        $i = $start;

        foreach ($records as $record) {
            $i++;
            $current_i = $i;

            // Check if the acknowledgment number has already been processed
            if (!in_array($record->acknowledgement_no, $acknowledgementNumbers)) {
                // Add the acknowledgment number to the processed list
                $acknowledgementNumbers[] = $record->acknowledgement_no;

                // Fetch evidence for the current acknowledgment number
                $evidence = Evidence::where('ack_no', (string)$record->acknowledgement_no)->get(['evidence_type', 'url', 'mobile']);
                $action = BankCasedata::where('acknowledgement_no', $record->acknowledgement_no)->get();
                // dd($evidence);

                $complaints = Complaint::where('acknowledgement_no', $record->acknowledgement_no)->get();

                $source_type = $record->source_type;
                $acknowledgement_no = $record->acknowledgement_no;

                $j = 0; // Counter for Sl.no within each acknowledgment_no
                foreach ($complaints as $complaint) {
                    $j++;
                    if ($format === "ncrp" && $j > 3) {
                        break; // Exit the loop if more than 10 records are processed for this acknowledgment number
                    }

                    $transaction_id = $complaint->transaction_id;
                    // $amount = '<span class="editable" data-ackno="' . $record->acknowledgement_no . '" data-transaction="' . $complaint->transaction_id . '">' . $complaint->amount . '</span>';
                    $amount = $complaint->amount;
                    $bank_name = $complaint->bank_name;
                    $complainant_name = $complaint->complainant_name;
                    $complainant_mobile = $complaint->complainant_mobile;
                    $district = $complaint->district;
                    $police_station = $complaint->police_station;
                    $account_id = $complaint->account_id;
                    $entry_date = Carbon::parse($complaint->entry_date)->format('Y-m-d H:i:s');
                    $current_status = $complaint->current_status;
                    $date_of_action = $complaint->date_of_action;
                    $action_taken_by_name = $complaint->action_taken_by_name;

                    // Initialize evidence type and URL
                    $evidenceType = '';
                    $url = '';

                    // Fetch evidence type and URL for the current complaint
                    foreach ($evidence as $item) {
                        if (isset($item->url)) {
                            $url .= $item->url . ', ';
                        } elseif (isset($item->mobile)) {
                            $url .= $item->mobile . ', ';
                        }
                        $evidenceType .= $item->evidence_type . ', ';
                    }

                    $countMoneyTransferTo = 0;
                    $countCashWithdrawal = 0;
                    $countTransactionPutOnHold = 0;
                    $countOther = 0;
                    $countwithdrawalThroughAtm  = 0;
                    $countWithdrawalThroughPos = 0;
                    $countWrongTransaction = 0;
                    $countClosedComplaint = 0;
                    $countOldTransaction = 0;
                    $action_taken_by_bank = "";

                    foreach ($action as $bankData) {
                        // Check if $normalizedBankActionStatus matches specific conditions
                        if ($normalizedBankActionStatus == "moneytransferto" && $bankData->action_taken_by_bank == "money transfer to") {
                            if ($countMoneyTransferTo < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countMoneyTransferTo++;
                            }
                        } elseif ($normalizedBankActionStatus == "cashwithdrawalthroughcheque" && $bankData->action_taken_by_bank == "cash withdrawal through cheque") {
                            if ($countCashWithdrawal < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countCashWithdrawal++;
                            }
                        } elseif ($normalizedBankActionStatus == "transactionputonhold" && $bankData->action_taken_by_bank == "transaction put on hold") {
                            if ($countTransactionPutOnHold < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countTransactionPutOnHold++;
                            }
                        } elseif ($normalizedBankActionStatus == "other" && $bankData->action_taken_by_bank == "other") {
                            if ($countOther < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countOther++;
                            }
                        } elseif ($normalizedBankActionStatus == "withdrawalthroughatm" && $bankData->action_taken_by_bank == "withdrawal through atm") {
                            if ($countwithdrawalThroughAtm < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countwithdrawalThroughAtm++;
                            }
                        } elseif ($normalizedBankActionStatus == "withdrawalthroughpos" && $bankData->action_taken_by_bank == "withdrawal through pos") {
                            if ($countWithdrawalThroughPos < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countWithdrawalThroughPos++;
                            }
                        } elseif ($normalizedBankActionStatus == "wrongtransaction" && $bankData->action_taken_by_bank == "wrong transaction") {
                            if ($countWrongTransaction < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countWrongTransaction++;
                            }
                        } elseif ($normalizedBankActionStatus == "closedcomplaint" && $bankData->action_taken_by_bank == "closed complaint") {
                            if ($countClosedComplaint < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countClosedComplaint++;
                            }
                        } elseif ($normalizedBankActionStatus == "oldtransaction" && $bankData->action_taken_by_bank == "old transaction") {
                            if ($countOldTransaction < 1) {
                                $action_taken_by_bank .= $bankData->action_taken_by_bank; // Concatenate action_taken_by_bank values
                                $countOldTransaction++;
                            }
                        }

                        // Break the loop if all conditions have been met
                        if ($countMoneyTransferTo >= 1 && $countCashWithdrawal >= 1 && $countTransactionPutOnHold >= 1 && $countOther >= 1 && $countwithdrawalThroughAtm >= 1 && $countWithdrawalThroughPos >= 1 && $countWrongTransaction >= 1 && $countClosedComplaint >= 1 && $countOldTransaction >= 1) {
                            break;
                        }
                    }


                    // Trim trailing comma and space
                    $evidenceType = rtrim($evidenceType, ', ');
                    $url = rtrim($url, ', ');
                    //dd($action_taken_by_bank);
                    $data_arr_print[] = [
                        "id" => $current_i,
                        "acknowledgement_no" => ($j == 1) ? $acknowledgement_no : '', // Only display the acknowledgment number for the first row of each group
                        "Sl.no" => ($j == 1) ? $i - $start : '', // Only display Sl.no for the first row of each group
                        "district" => $district,
                        "police_station" => $police_station,
                        "complainant_name" => $complainant_name,
                        "complainant_mobile" => $complainant_mobile,
                        "transaction_id" => $transaction_id,
                        "bank_name" => $bank_name,
                        "account_id" => $account_id, // Add the appropriate value or leave blank as needed
                        "amount" => $amount,
                        "entry_date" => $entry_date, // Add the appropriate value or leave blank as needed
                        "current_status" => $current_status, // Add the appropriate value or leave blank as needed
                        "date_of_action" => $date_of_action, // Add the appropriate value or leave blank as needed
                        'action_taken_by_bank' => $action_taken_by_bank,
                        "action_taken_by_name" => $action_taken_by_name, // Add the appropriate value or leave blank as needed
                        "evidence_type" => $evidenceType, // Evidence type for the current complaint
                        "url" => $url, // URL for the evidence associated with the current complaint
                        "edit" => ''
                    ];
                    $current_i = '';
                }
            }
        }
    }

// Generate response based on format
if ($format === 'ncrp') {
    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecords,
        "aaData" => $data_arr
    ];

    return response()->json($response);
} elseif ($format === 'csv') {
    if (empty($data_arr_print)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for CSV export'], 422);
    }

    // Create a CSV writer
    $csv = Writer::createFromString('');
    $csv->insertOne([
         "Sl.no", "Acknowledgement No", "District", "Police Station", "Complainant Name",
        "Complainant Mobile", "Transaction ID", "Bank Name", "Account ID", "Amount",
        "Entry Date", "Current Status", "Date of Action", "Action Taken By Bank", "Action Taken By Name","evidence_type","url"
    ]);
//print_r($data_arr_print);
    foreach ($data_arr_print as $row) {
        $csv->insertOne([
            $row["Sl.no"], $row["acknowledgement_no"], $row["district"], $row["police_station"],
            $row["complainant_name"], $row["complainant_mobile"], $row["transaction_id"], $row["bank_name"],
            $row["account_id"], strip_tags($row["amount"]), $row["entry_date"], $row["current_status"],
            $row["date_of_action"],$row["action_taken_by_bank"], $row["action_taken_by_name"], $row["evidence_type"], $row["url"]
        ]);
    }

    $csvOutput = $csv->toString();
    return response($csvOutput, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="Ncrp case data.csv"',
    ]);
} elseif ($format === 'excel') {
    if (empty($data_arr_print)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for Excel export'], 422);
    }

    // Define the headings for the Excel file
    $headings = [
        "Sl.no","Acknowledgement No", "District", "Police Station", "Complainant Name",
        "Complainant Mobile", "Transaction ID", "Bank Name", "Account ID", "Amount",
        "Entry Date", "Current Status", "Date of Action","Action Taken By Bank", "Action Taken By Name","evidence_type","url"
    ];

    // Remove 'id' field from data_arr_print
    $data_arr_print = array_map(function ($row) {
        unset($row['Sl.no']);
        return $row;
    }, $data_arr_print);

    // Generate and return Excel file with specified headings
    return Excel::download(new \App\Exports\ComplaintExport($data_arr_print, $headings), 'Ncrp case data.xlsx');
} else {
    return response()->json(['error' => 'Invalid format'], 400);
}


}



public function getDatalistOthersourcetype(Request $request)
{

    $format = $request->query('format');
    // dd($format);
    // Get request parameters
    $draw = $request->get('draw');
    $start = $request->get("start", 0);
    $rowperpage = $request->get("length", 10); // Default to 10 if not set

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');

    $columnIndex = $columnIndex_arr[0]['column'] ?? 0; // Column index
    $columnName = $columnName_arr[$columnIndex]['data'] ?? '_id'; // Column name
    $columnSortOrder = $order_arr[0]['dir'] ?? 'asc'; // asc or desc
    $searchValue = $search_arr['value'] ?? ''; // Search value

    // Get all source types
    $source_types = SourceType::all();

    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');
    $current_value = $request->get('current_value');
    $evidence_type_others = $request->get('evidence_type_others');
    $search_value_others = $request->get('search_value_others');
    // dd($evidence_type_others);
    // $complaints = ComplaintOthers::all();
    // dd($complaints);

    // Ensure $start and $rowperpage are integers and $rowperpage is positive
    $start = max(0, (int)$start);
    $rowperpage = max(1, (int)$rowperpage);

    // Build the MongoDB aggregation pipeline
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
                'evidence_type' => ['$addToSet' => '$evidence_type'],
            ]
        ],
        [
            '$sort' => [
                '_id' => 1,
                'created_at' => -1
            ]
        ],
        [
            '$skip' => (int)$start
        ],
        [
            '$limit' => (int)$rowperpage
        ]
    ];

    // Add search filtering to the pipeline if $searchValue is not empty
if (!empty($searchValue)) {
    array_unshift($pipeline, [
        '$match' => [
            '$or' => [
                ['source_type' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['case_number' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['url' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['domain' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['evidence_type' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['ip' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['registrar' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]],
                ['remarks' => ['$regex' => new \MongoDB\BSON\Regex($searchValue, 'i')]]
            ]
        ]
    ]);
}

    // Conditional pipeline stages
    if ($current_value === 'today') {
        $today = new DateTime('today');
        array_unshift($pipeline, [
            '$match' => [
                'created_at' => ['$gte' => new UTCDateTime($today->getTimestamp() * 1000)]
            ]
        ]);
    }

    if ($fromDate && $toDate) {
        array_unshift($pipeline, [
            '$match' => [
                'created_at' => [
                    '$gte' => new UTCDateTime((new DateTime($fromDate))->getTimestamp() * 1000),
                    '$lte' => new UTCDateTime((new DateTime($toDate))->getTimestamp() * 1000)
                ]
            ]
        ]);
    }


    if ($evidence_type_others || $search_value_others) {
        array_unshift($pipeline, [
            '$match' => [
                'evidence_type' => ['$regex' => new \MongoDB\BSON\Regex($evidence_type_others, 'i')],
                'url' => ['$regex' => new \MongoDB\BSON\Regex($search_value_others, 'i')]
            ]
        ]);
    }

    // Debug output to check pipeline stages
    // Log::info('Aggregation Pipeline:', $pipeline);

    // Fetch the complaints
    $complaints = ComplaintOthers::raw(function ($collection) use ($pipeline) {
        return $collection->aggregate($pipeline);
    });

    // Count distinct case numbers for total records
    $distinctCaseNumbersPipeline = [
        [
            '$group' => [
                '_id' => '$case_number'
            ]
        ]
    ];

    if (isset($current_value)) {
        $today = new DateTime('today');
        array_unshift($distinctCaseNumbersPipeline, [
            '$match' => [
                'created_at' => ['$gte' => new UTCDateTime($today->getTimestamp() * 1000)]
            ]
        ]);
    }

    if ($fromDate && $toDate) {
        array_unshift($distinctCaseNumbersPipeline, [
            '$match' => [
                'created_at' => ['$gte' => new UTCDateTime(strtotime($fromDate) * 1000), '$lte' => new UTCDateTime(strtotime($toDate) * 1000)]
            ]
        ]);
    }

    if ($evidence_type_others && $search_value_others) {
        array_unshift($distinctCaseNumbersPipeline, [
            '$match' => [
                'evidence_type' => ['$regex' => new \MongoDB\BSON\Regex($evidence_type_others, 'i')],
                'url' => ['$regex' => new \MongoDB\BSON\Regex($search_value_others, 'i')]
            ]
        ]);
    }

    $distinctCaseNumbers = ComplaintOthers::raw(function($collection) use ($distinctCaseNumbersPipeline) {
        return $collection->aggregate($distinctCaseNumbersPipeline);
    });

    $totalRecords = count($distinctCaseNumbers->toArray());
    $data_arr = [];
    $i = $start;
    $totalRecordswithFilter = $totalRecords;

    if ($format === 'others') {
// dd($complaints);
    foreach($complaints as $record){

        $i++;
        $evidence_type = "";$url = "";$domain="";$ip="";$registrar="";$remarks=""; $source_type="";

        $case_number = '<a href="' . route('other-case-details', ['id' => Crypt::encryptString($record->_id)]) . '">'.$record->_id.'</a>';

        foreach ($record->evidence_type as $item) {
            $evidence_type .= $item."<br>";
        }

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
                "evidence_type" => $evidence_type,
                "url" => $url,
                "domain" => $domain,
                "ip" => $ip,
                "registrar"=>$registrar,
                "remarks" => $remarks,
                );

    }


}

if ($format === 'csv' || $format === 'excel') {

    $uniqueCaseNumbers = []; // Array to store unique case numbers

    foreach ($complaints as $record) {
        $i++;
        $current_i = $i;

        // Store the case number if it's not already stored
        if (!in_array($record->_id, $uniqueCaseNumbers)) {
            $uniqueCaseNumbers[] = $record->_id;
            if ($format === "others") {
                $case_number = '<a href="' . route('other-case-details', ['id' => $record->_id]) . '">' . $record->_id . '</a>';
            }else{
                $case_number = $record->_id;
            }
            $recordCounter = 0; // Counter for records per case number
        } else {
            $case_number = ''; // If the case number is already stored, leave it empty
        }

        // Get the total number of items for each field
        // $totalEvdenceType = count($record->evidence_type);
        $totalUrls = count($record->url);
        $totalSourceTypes = count($record->source_type);
        $totalDomains = count($record->domain);
        $totalIps = count($record->ip);
        $totalRegistrars = count($record->registrar);
        $totalRemarks = count($record->remarks);
        $totalEvidenceType = count($record->evidence_type);

        // Determine the maximum total to loop through
        $maxTotal = max($totalEvidenceType, $totalUrls, $totalSourceTypes, $totalDomains, $totalIps, $totalRegistrars, $totalRemarks);

        for ($j = 0; $j < $maxTotal; $j++) {

            if ($format !== "others" || ($format === "others" && $recordCounter < 3)) { // Check if the record count for the case number is less than 10
                $evidence_type = $record->evidence_type[$j] ?? '';
                $url = $record->url[$j] ?? '';
                $source_type = '';
                foreach ($source_types as $st) {
                    if ($j < $totalSourceTypes && $st->_id == $record->source_type[$j]) {
                        $source_type = $st->name;
                        break;
                    }
                }
                $domain = $record->domain[$j] ?? '';
                $ip = $record->ip[$j] ?? '';
                $registrar = $record->registrar[$j] ?? '';
                $remarks = $record->remarks[$j] ?? '';

                // Add each field to the data array as a separate row
                $data_arr_print[] = [
                    "id" => $current_i,
                    "source_type" => $source_type,
                    "case_number" => $case_number,
                    "evidence_type" => $evidence_type,
                    "url" => $url,
                    "domain" => $domain,
                    "ip" => $ip,
                    "registrar" => $registrar,
                    "remarks" => $remarks,
                ];

                $recordCounter++; // Increment record counter for the case number
            }

            // Reset $case_number to empty after the first iteration
            $case_number = '';
            $current_i = '';
        }
    }
}
// Generate response based on format
if ($format === 'others') {
    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
    ];

    return response()->json($response);
} elseif ($format === 'csv') {
    if (empty($data_arr_print)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for CSV export'], 422);
    }


    // Create a CSV writer
    $csv = Writer::createFromString('');
    $csv->insertOne([
        "Sl.no", "Source Type", "Case Number","Evidence Type", "URL", "Domain", "IP", "Registrar", "Remarks"
    ]);

    foreach ($data_arr_print as $row) {
        $csv->insertOne([
            $row["id"], $row["source_type"], $row["case_number"], $row["evidence_type"], $row["url"],
            $row["domain"], $row["ip"], $row["registrar"],
            $row["remarks"]
        ]);
    }

    $csvOutput = $csv->toString();
    return response($csvOutput, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="Other case data.csv"',
    ]);
} elseif ($format === 'excel') {
    if (empty($data_arr_print)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for Excel export'], 422);
        // echo "<script>alert('No data available for Excel export');</script>";
    }

    // Define the headings for the Excel file
    $headings = [
        "Sl.no", "Source Type", "Case Number","Evidence Type", "URL", "Domain", "IP", "Registrar", "Remarks"
    ];

    // Generate and return Excel file with specified headings
    return Excel::download(new \App\Exports\ComplaintExport($data_arr_print, $headings), 'Other case data.xlsx');
} else {
    return response()->json(['error' => 'Invalid format'], 400);
}

}

}
