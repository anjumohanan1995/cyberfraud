<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Modus;
use App\Models\ComplaintAdditionalData;
use App\Models\BankCasedata;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class BankReportController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $today = date('Y-m-d');
        return view('dashboard.bank-reports.index', compact('today'));
    }
    public function aboveIndex()
    {

        $today = date('Y-m-d');
        return view('dashboard.bank-reports.oneLakhAbove', compact('today'));
    }


    // public function getBankDetailsByDate(Request $request)
    // {
    //     // Get date inputs and validate them
    //     $from_date = $request->input('from_date', date('Y-m-d'));
    //     $to_date = $request->input('to_date', date('Y-m-d'));

    //     try {
    //         $from_date_dt = new \DateTime($from_date);
    //         $to_date_dt = new \DateTime($to_date);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Invalid date format'], 400);
    //     }

    //     // Get the data
    //     $bankCasedata = BankCaseData::whereBetween('transaction_date', [$from_date, $to_date])
    //         ->where('com_status', 1)
    //         ->get();

    //     $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();
    //     $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

    //     $complaintsByAcknowledgementNo = [];
    //     foreach ($complaints as $complaint) {
    //         $complaintsByAcknowledgementNo[$complaint->acknowledgement_no] = $complaint;
    //     }

    //     $results = [];

    //     foreach ($bankCasedata as $data) {
    //         $acknowledgementNo = $data->acknowledgement_no;
    //         $district = $complaintsByAcknowledgementNo[$acknowledgementNo]->district ?? null;
    //         $entry_date = $complaintsByAcknowledgementNo[$acknowledgementNo]->entry_date ?? null;

    //         if ($district) {
    //             if (!isset($results[$district])) {
    //                 $results[$district] = [
    //                     'district' => $district,
    //                     'actual_amount' => 0,
    //                     'actual_amount_lost_on' => 0,
    //                     'actual_amount_hold_on' => 0,
    //                     'hold_amount_otherthan' => 0,
    //                     'total_amount_lost_from_eco' => 0,
    //                     'total_hold' => 0,
    //                     'amount_for_pending_action' => 0,
    //                     '1930_count' => 0,
    //                     'NCRP_count' => 0,
    //                     'total' => 0,
    //                 ];
    //             }

    //             if ($data->Layer == 1) {
    //                 $results[$district]['actual_amount'] += $data->transaction_amount;
    //             }

    //             if (strpos($acknowledgementNo, '315') === 0) {
    //                 $results[$district]['1930_count'] += 1;
    //             } elseif (strpos($acknowledgementNo, '215') === 0) {
    //                 $results[$district]['NCRP_count'] += 1;
    //             }

    //             if ($entry_date) {
    //                 try {
    //                     $utc_date = Carbon::parse($entry_date, 'UTC')->setTimezone('Asia/Kolkata');
    //                     $entry_date = $utc_date->format('Y-m-d H:i:s');

    //                     $entry_date_dt = new \DateTime($entry_date);
    //                     $transaction_date_dt = new \DateTime($data->transaction_date);
    //                     // echo "<pre>";
    //     // print_r($data->action_taken_by_bank);
    //     if ($data->Layer == 1 && $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d')) {
    //     $results[$district]['actual_amount_lost_on'] += $data->transaction_amount;
    //  }
    //     //dd($transaction_date_dt, $entry_date_dt, $data->Layer, $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d'));
    //                     if ($data->action_taken_by_bank == "transaction put on hold") {
    //                         if($transaction_date_dt->format('Y-m-d') == $entry_date_dt->format('Y-m-d')){
    //                         $results[$district]['actual_amount_hold_on'] += $data->transaction_amount;
    //                     }}
    //                     if (($data->action_taken_by_bank == "transaction put on hold") && ($transaction_date_dt->format('Y-m-d') != $entry_date_dt->format('Y-m-d'))) {
    //                         $results[$district]['hold_amount_otherthan'] += $data->transaction_amount;
    //                     }

    //                     if (in_array($data->action_taken_by_bank, [
    //                         'cash withdrawal through cheque',
    //                         'withdrawal through atm',
    //                         'other',
    //                         'wrong transaction',
    //                         'withdrawal through pos'
    //                     ])) {
    //                         $results[$district]['total_amount_lost_from_eco'] += $data->transaction_amount;
    //                     }
    //                 } catch (\Exception $e) {
    //                     return response()->json(['error' => 'Error parsing date: ' . $e->getMessage()], 400);
    //                 }
    //             }
    //         }
    //     }

    //     foreach ($results as &$result) {
    //         $result['total_holds'] = $result['actual_amount_hold_on'] + $result['hold_amount_otherthan'];

    //         $result['amount_for_pending_actions'] = max(0, $result['actual_amount'] - $result['total_hold'] - $result['total_amount_lost_from_eco']);
    //         $result['amount_for_pending_action'] = round($result['amount_for_pending_actions'], 2);
    //         $result['total'] = $result['1930_count'] + $result['NCRP_count'];
    //     }
    //    // dd($results);
    //     // Convert results array to a format DataTables expects
    //     $data = array_values($results);

    //     // Implement server-side processing logic for DataTables
    //     $draw = intval($request->input('draw'));
    //     $start = intval($request->input('start'));
    //     $length = intval($request->input('length'));

    //     // Apply pagination
    //     $data = array_slice($data, $start, $length);

    //     return response()->json([
    //         'draw' => $draw,
    //         'recordsTotal' => count($results),
    //         'recordsFiltered' => count($results),
    //         'data' => $data,
    //     ]);
    // }


//     public function getBankDetailsByDate(Request $request)
// {
//     $fromDate = $request->get('from_date');
//     $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
//     $toDateEnd = $fromDate ? Carbon::parse($fromDate)->endOfDay() : null;
//     if ($fromDateStart && $toDateEnd) {
//         $startdate = new UTCDateTime($fromDateStart->timestamp * 1000);
//         $enddate = new UTCDateTime($toDateEnd->timestamp * 1000);
//     }
//    // dd($fromDateStart, $toDateEnd, $startdate, $enddate);
//     $bankCasedata = BankCaseData::whereBetween('transaction_date', [$startdate, $enddate])
//         ->where('com_status', 1)
//         ->get();
//     //dd($bankCasedata);
//     $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();
//     $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();
//     //dd($complaints);
//     $complaintsByAcknowledgementNo = [];
//     foreach ($complaints as $complaint) {
//         $complaintsByAcknowledgementNo[$complaint->acknowledgement_no] = $complaint;
//     }

//     $results = [];

//     foreach ($bankCasedata as $data) {
//         //dd($data);
//         $acknowledgementNo = $data->acknowledgement_no;
//         $district = $complaintsByAcknowledgementNo[$acknowledgementNo]->district ?? null;
//         $entry_date = $complaintsByAcknowledgementNo[$acknowledgementNo]->entry_date ?? null;

//         if ($district) {
//             if (!isset($results[$district])) {
//                 $results[$district] = [
//                     'district' => $district,
//                     'actual_amount' => 0,
//                     'actual_amount_lost_on' => 0,
//                     'actual_amount_hold_on' => 0,
//                     'hold_amount_otherthan' => 0,
//                     'total_amount_lost_from_eco' => 0,
//                     'total_hold' => 0,
//                     'amount_for_pending_action' => 0,
//                     '1930_count' => 0,
//                     'NCRP_count' => 0,
//                     'total' => 0,
//                 ];
//             }

//             if ($data->Layer == 1) {
//                 $results[$district]['actual_amount'] += $data->transaction_amount;
//             }

//             if (strpos($acknowledgementNo, '315') === 0) {
//                 $results[$district]['1930_count'] += 1;
//             } elseif (strpos($acknowledgementNo, '215') === 0) {
//                 $results[$district]['NCRP_count'] += 1;
//             }

//             if ($entry_date) {
//                 try {
//                     $utc_date = \Carbon\Carbon::parse($entry_date, 'UTC')->setTimezone('Asia/Kolkata');
//                     $entry_date = $utc_date->format('Y-m-d H:i:s');

//                     $entry_date_dt = new \DateTime($entry_date);
//                     $transaction_date_dt = new \DateTime($data->transaction_date);

//                     $transaction_date = $data->transaction_date; // Assume this is the MongoDB\BSON\UTCDateTime object
//                     $dateTime = $transaction_date->toDateTime(); // Converts to PHP DateTime object
//                     $formattedDate = $dateTime->format('Y-m-d\TH:i:s.vP');
//                     $result['transaction_date'] = $formattedDate;
//                    // dd($entry_date_dt, $transaction_date_dt);

//                     // $transaction_date = $data->transaction_date; // Assume this is the MongoDB\BSON\UTCDateTime object
//                     // $dateTime = $transaction_date->toDateTime(); // Converts to PHP DateTime object

//                     // // Optionally, format the DateTime object to a string
//                     // $transaction_date_dt = $dateTime->format('Y-m-d\TH:i:s.vP'); // 2024-08-01T04:30:22.000+00:00

//                     if ($data->Layer == 1 && $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d')) {
//                         $results[$district]['actual_amount_lost_on'] += $data->transaction_amount;
//                     }

//                     if ($data->action_taken_by_bank == "transaction put on hold") {
//                         if ($transaction_date_dt->format('Y-m-d') == $entry_date_dt->format('Y-m-d')) {
//                             $results[$district]['actual_amount_hold_on'] += $data->transaction_amount;
//                         }
//                     }
//                     if (($data->action_taken_by_bank == "transaction put on hold") && ($transaction_date_dt->format('Y-m-d') != $entry_date_dt->format('Y-m-d'))) {
//                         $results[$district]['hold_amount_otherthan'] += $data->transaction_amount;
//                     }

//                     if (in_array($data->action_taken_by_bank, [
//                         'cash withdrawal through cheque',
//                         'withdrawal through atm',
//                         'other',
//                         'wrong transaction',
//                         'withdrawal through pos'
//                     ])) {
//                         $results[$district]['total_amount_lost_from_eco'] += $data->transaction_amount;
//                     }
//                 } catch (\Exception $e) {
//                     return response()->json(['error' => 'Error parsing date: ' . $e->getMessage()], 400);
//                 }
//             }
//         }
//     }

//     foreach ($results as &$result) {
//         $result['total_holds'] = $result['actual_amount_hold_on'] + $result['hold_amount_otherthan'];

//         $result['amount_for_pending_actions'] = max(0, $result['actual_amount'] - $result['total_hold'] - $result['total_amount_lost_from_eco']);
//         $result['amount_for_pending_action'] = round($result['amount_for_pending_actions'], 2);
//         $result['total'] = $result['1930_count'] + $result['NCRP_count'];

//     }

//     // Convert results array to a format DataTables expects
//     $data = array_values($results);
// //dd($data);
//     // Implement server-side processing logic for DataTables
//     $draw = intval($request->input('draw'));
//     $start = intval($request->input('start'));
//     $length = intval($request->input('length'));

//     // Apply pagination
//     $data = array_slice($data, $start, $length);

//     return response()->json([
//         'draw' => $draw,
//         'recordsTotal' => count($results),
//         'recordsFiltered' => count($results),
//         'data' => $data,
//     ]);
// }

public function getBankDetailsByDate(Request $request)
{
    $fromDate = $request->get('from_date');
    $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
    $toDateEnd = $fromDate ? Carbon::parse($fromDate)->endOfDay() : null;
    if ($fromDateStart && $toDateEnd) {
        $startdate = new UTCDateTime($fromDateStart->timestamp * 1000);
        $enddate = new UTCDateTime($toDateEnd->timestamp * 1000);
    }

    $bankCasedata = BankCaseData::whereBetween('transaction_date', [$startdate, $enddate])
        ->where('com_status', 1)
        ->get();

    $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();
    $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

    $complaintsByAcknowledgementNo = [];
    foreach ($complaints as $complaint) {
        $complaintsByAcknowledgementNo[$complaint->acknowledgement_no] = $complaint;
    }

    $results = [];

    foreach ($bankCasedata as $data) {
        $acknowledgementNo = $data->acknowledgement_no;
        $district = $complaintsByAcknowledgementNo[$acknowledgementNo]->district ?? null;
        $entry_date = $complaintsByAcknowledgementNo[$acknowledgementNo]->entry_date ?? null;

        if ($district) {
            if (!isset($results[$district])) {
                $results[$district] = [
                    'district' => $district,
                    'actual_amount' => 0,
                    'actual_amount_lost_on' => 0,
                    'actual_amount_hold_on' => 0,
                    'hold_amount_otherthan' => 0,
                    'total_amount_lost_from_eco' => 0,
                    'total_hold' => 0,
                    'amount_for_pending_action' => 0,
                    '1930_count' => 0,
                    'NCRP_count' => 0,
                    'total' => 0,
                ];
            }

            if ($data->Layer == 1) {
                $results[$district]['actual_amount'] += $data->transaction_amount;
            }

            if (strpos($acknowledgementNo, '315') === 0) {
                $results[$district]['1930_count'] += 1;
            } elseif (strpos($acknowledgementNo, '215') === 0) {
                $results[$district]['NCRP_count'] += 1;
            }

            if ($entry_date) {
                try {
                    $utc_date = \Carbon\Carbon::parse($entry_date, 'UTC')->setTimezone('Asia/Kolkata');
                    $entry_date = $utc_date->format('Y-m-d H:i:s');

                    $entry_date_dt = new \DateTime($entry_date);
                    $transaction_date_dt = new \DateTime($data->transaction_date->toDateTime()->format('Y-m-d H:i:s'));

                    if ($data->Layer == 1 && $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d')) {
                        $results[$district]['actual_amount_lost_on'] += $data->transaction_amount;
                    }

                    if ($data->action_taken_by_bank == "transaction put on hold") {
                        if ($transaction_date_dt->format('Y-m-d') == $entry_date_dt->format('Y-m-d')) {
                            $results[$district]['actual_amount_hold_on'] += $data->transaction_amount;
                        }
                    }
                    if (($data->action_taken_by_bank == "transaction put on hold") && ($transaction_date_dt->format('Y-m-d') != $entry_date_dt->format('Y-m-d'))) {
                        $results[$district]['hold_amount_otherthan'] += $data->transaction_amount;
                    }

                    if (in_array($data->action_taken_by_bank, [
                        'cash withdrawal through cheque',
                        'withdrawal through atm',
                        'other',
                        'wrong transaction',
                        'withdrawal through pos'
                    ])) {
                        $results[$district]['total_amount_lost_from_eco'] += $data->transaction_amount;
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error parsing date: ' . $e->getMessage()], 400);
                }
            }
        }
    }

    foreach ($results as &$result) {
        $result['total_holds'] = $result['actual_amount_hold_on'] + $result['hold_amount_otherthan'];
        $result['amount_for_pending_actions'] = max(0, $result['actual_amount'] - $result['total_hold'] - $result['total_amount_lost_from_eco']);
        $result['amount_for_pending_action'] = round($result['amount_for_pending_actions'], 2);
        $result['total'] = $result['1930_count'] + $result['NCRP_count'];
    }

    // Convert results array to a format DataTables expects
    $data = array_values($results);

    // Implement server-side processing logic for DataTables
    $draw = intval($request->input('draw'));
    $start = intval($request->input('start'));
    $length = intval($request->input('length'));

    // Apply search filter
    $searchValue = $request->input('search.value');
    if ($searchValue) {
        $data = array_filter($data, function ($item) use ($searchValue) {
            return strpos(strtolower($item['district']), strtolower($searchValue)) !== false;
        });
    }

    // Apply pagination
    $data = array_slice($data, $start, $length);

    return response()->json([
        'draw' => $draw,
        'recordsTotal' => count($results),
        'recordsFiltered' => count($data),
        'data' => array_values($data),
    ]);
}




    //////////////////////////////////////////////////////////////////////////////////

//     public function getAboveData(Request $request)
//     {
//   // Default dates to today if not provided
//   $fromDate = $request->input('from_date');
//   $to_date = $request->input('to_date');





//    // Convert date strings to DateTime objects
//   try {
//     $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
//     $toDateEnd = $to_date ? Carbon::parse($to_date)->endOfDay() : null;
//     if ($fromDateStart && $toDateEnd) {
//         $startdate = new UTCDateTime($fromDateStart->timestamp * 1000);
//         $enddate = new UTCDateTime($toDateEnd->timestamp * 1000);
//     }
// } catch (\Exception $e) {
//     return response()->json([
//         'draw' => intval($request->input('draw')),
//         'recordsTotal' => 0,
//         'recordsFiltered' => 0,
//         'data' => [],
//         'error' => 'Invalid date format'
//     ]);
// }

//     $bankCasedata = BankCaseData::whereBetween('transaction_date', [$startdate, $enddate])
//         ->where('com_status', 1)
//         ->get();
//     //dd($bankCasedata);

// //dd($acknowledgementNos);

// // if (empty($bankCaseData)) {
// //     return response()->json([
// //         'draw' => intval($request->input('draw')),
// //         'recordsTotal' => 0,
// //         'recordsFiltered' => 0,
// //         'data' => [],
// //         //'error' => 'No bank case data found for the specified criteria'
// //     ]);
// // }
// $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();

//         // Fetch complaints details
//         $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();
// //dd($complaints);
//         // if ($complaints->isEmpty()) {
//         //     return response()->json([
//         //         'draw' => intval($request->input('draw')),
//         //         'recordsTotal' => 0,
//         //         'recordsFiltered' => 0,
//         //         'data' => [],
//         //         'error' => 'No complaints found for the specified acknowledgements'
//         //     ]);
//         // }
//         // Total Reported Amount
//         $results = [];
//         foreach ($complaints as $complaint) {
//             $date = Carbon::parse($complaint['entry_date']);

// // Format to d-m-Y
// $formattedDate = $date->format('d-m-Y');
//             $ackNo = (string) $complaint['acknowledgement_no'];
//             if (!isset($results[$ackNo])) {
//                 $results[$ackNo] = [
//                     'acknowledgement_no' => $ackNo,
//                     'district' => $complaint['district'],
//                     'reported_date' => $formattedDate,
//                     'total_amount' => 0
//                 ];
//             }
//             $results[$ackNo]['total_amount'] += $complaint['amount'];
//         }
// //dd($results);
//         $filteredResults = array_filter($results, function ($result) {
//             return $result['total_amount'] > 100000;
//         });
// //validate above 100000
//         // if (empty($filteredResults)) {
//         //     return response()->json([
//         //         'draw' => intval($request->input('draw')),
//         //         'recordsTotal' => 0,
//         //         'recordsFiltered' => 0,
//         //         'data' => [],
//         //         'error' => 'No complaints found with total amount greater than 100000'
//         //     ]);
//         // }

//         // Transaction Dates

//         foreach ($filteredResults as $ackNo => $complaintData) {
//             $firstTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
//                 return $collection->aggregate([
//                     ['$match' => ['acknowledgement_no' => $ackNo, 'Layer' => 1]],
//                     ['$sort' => ['transaction_date' => 1]],
//                     ['$limit' => 1]
//                 ])->toArray();
//             });

//             $lastTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
//                 return $collection->aggregate([
//                     ['$match' => ['acknowledgement_no' => $ackNo]],
//                     ['$sort' => ['Layer' => -1, 'transaction_date' => -1]],
//                     ['$limit' => 1]
//                 ])->toArray();
//             });

//             if (!empty($firstTransaction)) {
//                 // Extract milliseconds and convert to seconds
//                 $firstTransactionDate = $firstTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
//                 $filteredResults[$ackNo]['first_transaction_date'] = Carbon::createFromTimestamp($firstTransactionDate / 1000)->format('d-m-Y');
//             }

//             if (!empty($lastTransaction)) {
//                 // Extract milliseconds and convert to seconds
//                 $lastTransactionDate = $lastTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
//                 $filteredResults[$ackNo]['last_transaction_date'] = Carbon::createFromTimestamp($lastTransactionDate / 1000)->format('d-m-Y');
//             }
//             $combained_date = $filteredResults[$ackNo]['first_transaction_date'] . ' - ' . $filteredResults[$ackNo]['last_transaction_date'];
//             $filteredResults[$ackNo]['transaction_period']=$combained_date;
//             //Lien Amount calculation
//             //dd($ackNo);
//             $lienAmount = BankCaseData::where('acknowledgement_no', $ackNo)
//                 ->where('action_taken_by_bank', 'transaction put on hold')
//                 ->whereBetween('transaction_date', [$fromDate, $to_date])
//                 ->sum('transaction_amount');

//             // Since sum returns an integer, directly use it
//             $lienAmount = !empty($lienAmount) ? $lienAmount : 0;

//             $filteredResults[$ackNo]['lien_amount'] = $lienAmount;


//             //Amount lost calculation

//             $actions = [
//                 'cash withdrawal through cheque',
//                 'withdrawal through atm',
//                 'other',
//                 'wrong transaction',
//                 'withdrawal through pos'
//             ];


//             $totalAmountLost = BankCaseData::Where('acknowledgement_no', $ackNo)->WhereBetween('transaction_date', [$fromDate, $to_date])->WhereIn('action_taken_by_bank', $actions)->sum('transaction_amount');
//            //dd($totalAmountLost);
//             $totalAmountLost = !empty($totalAmountLost) ? $totalAmountLost : 0;
//             $filteredResults[$ackNo]['amount_lost'] = $totalAmountLost;

// ///////////////////////////Amount Pending calculation////////////////////////////////////////////


// $sum_amount = Complaint::where('acknowledgement_no', (int)$ackNo)->where('com_status',1)->sum('amount');
// $hold_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)->where('com_status',1)
// ->where('action_taken_by_bank','transaction put on hold')->sum('transaction_amount');
// //dd($hold_amount );
// // $lost_amount = BankCaseData::where('acknowledgement_no', (int)$id)->where('com_status',1)
// //                             ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos'])
// //                             ->sum('transaction_amount');
// $lost_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)->where('com_status',1)
//                             ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos' , 'aadhaar enabled payment System'])
//                             ->sum('dispute_amount');

// $pending_amount = $sum_amount - $hold_amount - $lost_amount;
// //dd($pending_amount);
// $layer_one_transactions = BankCasedata::where('acknowledgement_no',(int)$ackNo)->where('Layer',1)->where('com_status',1)->get();
//         $transaction_based_array_final = [];$final_array=[];
//         for($i=0;$i<count($layer_one_transactions);$i++){
//             // dd($layer_one_transactions[$i]);
//              $layer = 1;
//              $transaction_id_sec = $layer_one_transactions[$i]->transaction_id_sec;
//              $first_row = BankCaseData::where('acknowledgement_no', $ackNo)
//              ->where('transaction_id_sec', $transaction_id_sec)
//              ->get()
//              ->toArray();


//              $processed_ids = [];
//              $transaction_baed_array = [];
//              if($first_row){

//                 $transaction_baed_array =  $this->checkifempty($layer,$first_row,$ackNo,$processed_ids);

//              }

//                   $final_array = array_merge($final_array,$transaction_baed_array);


//          }
//         //dd($final_array);
//         $additional = ComplaintAdditionalData::where('ack_no', (string)$ackNo)->first();

//        // $transaction_numbers_layer1 = BankCasedata::where('acknowledgement_no',(int)$id)->where('Layer',1)->get();
//         $layers = BankCasedata::where('acknowledgement_no',(int)$ackNo)->groupBy('Layer')->pluck('Layer');
//         $pending_banks_array = [];
//         for ($i = 1; $i <= count($layers); $i++) {
//             $current_layer = BankCasedata::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i)
//                 ->where('com_status', 1)
//                 ->where('action_taken_by_bank', 'money transfer to')
//                 ->where('bank', '!=', 'Others')
//                 ->get(['transaction_id_sec', 'bank', 'transaction_amount', 'desputed_amount']);

//             $next_layer = BankCasedata::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i + 1)
//                 ->pluck('transaction_id_or_utr_no')
//                 ->toArray();

//             $current_layer_utr = BankCasedata::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i)
//                 ->pluck('transaction_id_or_utr_no')
//                 ->toArray();

//             // Convert to a simple array of transaction numbers
//             $next_layer_utr_array = $this->extractTransactionIds($next_layer);
//             $current_layer_utr_array = $this->extractTransactionIds($current_layer_utr);

//             foreach ($current_layer as $transaction) {
//                 if ($transaction->transaction_id_sec) {
//                     if (!in_array($transaction->transaction_id_sec, $next_layer_utr_array) &&
//                         !in_array($transaction->transaction_id_sec, $current_layer_utr_array)) {
//                         $pending_banks_array[] = [
//                             "pending_banks" => $transaction->bank,
//                             "transaction_id" => $transaction->transaction_id_sec,
//                             "transaction_amount" => $transaction->transaction_amount,
//                             "desputed_amount" => $transaction->desputed_amount
//                         ];
//                     }
//                 }
//             }
//         }

//      $groupedData = [];
//      $finalData_pending_banks=[];
//     foreach ($pending_banks_array as $item) {
//     $pendingBank = $item['pending_banks'];
//     $transactionId = $item['transaction_id'];
//     $transactionAmount = $item['transaction_amount'];
//     $desputedAmount = $item['desputed_amount'];

//     if (!isset($finalData_pending_banks)) {
//         $finalData_pending_banks = [];
//     }

//     $finalData_pending_banks[] = ['pending_banks' => $pendingBank, 'transaction_id' => $transactionId , 'transaction_amount'=> $transactionAmount, 'desputed_amount' => $desputedAmount];
//     }

//    // dd($finalData_pending_banks);

// //  $finalData_pending_banks = collect($finalData_pending_banks)->groupBy('pending_banks')->map(function ($group) {
// //         return [
// //             'pending_banks' => $group->first()['pending_banks'],
// //             'transaction_id'=> $group->count(),
// //             'transaction_amount' => $group->sum('transaction_amount'),
// //             'desputed_amount' => $group->first()['desputed_amount']

// //         ];
// //     })->values()->all();


// dd($finalData_pending_banks);



// /////////////////////////////Amount Pending calculation////////////////////////////////////////////






//             //Amount Pending calculation
//             $filteredResults[$ackNo]['amount_pending'] = $pending_amount;
//             //dd($filteredResults[$ackNo]['amount_pending']);
//            // dd($ackNo);
//            // $bankNames = BankCaseData::Where('acknowledgement_no', $ackNo)->Where('action_taken_by_bank', 'money transfer to')->groupBy('bank')->pluck('bank')->toArray();
//             //dd($bankNames);
//             //validation for empty array instead of [null]
//             // if ($bankNames == [null]) {
//             //     $bankNames = 'No pending banks';
//             // }

//             //$filteredResults[$ackNo]['pending_banks'] = $finalData_pending_banks['pending_banks'];
//             $ACK = (string)$ackNo;
//             //Fetching Modus
//             //dd($ACK);
//             $modus = ComplaintAdditionalData::Where('ack_no', $ACK)->pluck('modus')->first();
// //dd($modus);
// $modus_name = Modus::Where('_id', $modus)->pluck('name')->first();
// //dd($modus_name);
// $filteredResults[$ackNo]['modus'] = $modus_name;
//         }





//         //dd($filteredResults[$ackNo]['lien_amount']);



//         return response()->json([
//             'draw' => intval($request->input('draw')),
//             'recordsTotal' => count($filteredResults),
//             'recordsFiltered' => count($filteredResults),
//             'data' => array_values($filteredResults),
//         ]);






//     }



// public function getAboveData(Request $request)
// {
//     // Default dates to today if not provided
//     $fromDate = $request->input('from_date');
//     $to_date = $request->input('to_date');
//     // $search_arr = $request->get('search');
//     // $searchValue = $search_arr['value'];
//     // if (!empty($searchValue)) {
//     //     $matchStage['$or'] = [
//     //         ['district' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['url' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['remarks' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
//     //         ['source.name' => ['$regex' => $searchValue, '$options' => 'i']]  // Search by source name
//     //     ];
//     // }
//     // Convert date strings to DateTime objects
//     try {
//         $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
//         $toDateEnd = $to_date ? Carbon::parse($to_date)->endOfDay() : null;
//         if ($fromDateStart && $toDateEnd) {
//             $startdate = new UTCDateTime($fromDateStart->timestamp * 1000);
//             $enddate = new UTCDateTime($toDateEnd->timestamp * 1000);
//         }
//     } catch (\Exception $e) {
//         return response()->json([
//             'draw' => intval($request->input('draw')),
//             'recordsTotal' => 0,
//             'recordsFiltered' => 0,
//             'data' => [],
//             'error' => 'Invalid date format'
//         ]);
//     }

//     $bankCasedata = BankCaseData::whereBetween('transaction_date', [$startdate, $enddate])
//         ->where('com_status', 1)
//         ->get();

//     $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();

//     // Fetch complaints details
//     $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

//     $results = [];
//     foreach ($complaints as $complaint) {
//         $date = Carbon::parse($complaint['entry_date']);
//         $formattedDate = $date->format('d-m-Y');
//         $ackNo = (string) $complaint['acknowledgement_no'];
//         if (!isset($results[$ackNo])) {
//             $results[$ackNo] = [
//                 'acknowledgement_no' => $ackNo,
//                 'district' => $complaint['district'],
//                 'reported_date' => $formattedDate,
//                 'total_amount' => 0
//             ];
//         }
//         $results[$ackNo]['total_amount'] += $complaint['amount'];
//     }

//     $filteredResults = array_filter($results, function ($result) {
//         return $result['total_amount'] > 100000;
//     });

//     foreach ($filteredResults as $ackNo => $complaintData) {
//         $firstTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
//             return $collection->aggregate([
//                 ['$match' => ['acknowledgement_no' => $ackNo, 'Layer' => 1]],
//                 ['$sort' => ['transaction_date' => 1]],
//                 ['$limit' => 1]
//             ])->toArray();
//         });

//         $lastTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
//             return $collection->aggregate([
//                 ['$match' => ['acknowledgement_no' => $ackNo]],
//                 ['$sort' => ['Layer' => -1, 'transaction_date' => -1]],
//                 ['$limit' => 1]
//             ])->toArray();
//         });

//         if (!empty($firstTransaction)) {
//             $firstTransactionDate = $firstTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
//             $filteredResults[$ackNo]['first_transaction_date'] = Carbon::createFromTimestamp($firstTransactionDate / 1000)->format('d-m-Y');
//         } else {
//             $filteredResults[$ackNo]['first_transaction_date'] = null;
//         }

//         if (!empty($lastTransaction)) {
//             $lastTransactionDate = $lastTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
//             $filteredResults[$ackNo]['last_transaction_date'] = Carbon::createFromTimestamp($lastTransactionDate / 1000)->format('d-m-Y');
//         } else {
//             $filteredResults[$ackNo]['last_transaction_date'] = null;
//         }

//         // Check if the dates are set before using them
//         if (isset($filteredResults[$ackNo]['first_transaction_date']) && isset($filteredResults[$ackNo]['last_transaction_date'])) {
//             $combained_date = $filteredResults[$ackNo]['first_transaction_date'] . ' - ' . $filteredResults[$ackNo]['last_transaction_date'];
//             $filteredResults[$ackNo]['transaction_period'] = $combained_date;
//         } else {
//             $filteredResults[$ackNo]['transaction_period'] = 'N/A';
//         }

//         // Lien Amount calculation
//         $lienAmount = BankCaseData::where('acknowledgement_no', $ackNo)
//             ->where('action_taken_by_bank', 'transaction put on hold')
//             ->whereBetween('transaction_date', [$fromDate, $to_date])
//             ->sum('transaction_amount');
//         $filteredResults[$ackNo]['lien_amount'] = !empty($lienAmount) ? $lienAmount : 0;

//         // Amount Lost calculation
//         $actions = [
//             'cash withdrawal through cheque',
//             'withdrawal through atm',
//             'other',
//             'wrong transaction',
//             'withdrawal through pos'
//         ];
//         $totalAmountLost = BankCaseData::where('acknowledgement_no', $ackNo)
//             ->whereBetween('transaction_date', [$fromDate, $to_date])
//             ->whereIn('action_taken_by_bank', $actions)
//             ->sum('transaction_amount');
//         $filteredResults[$ackNo]['amount_lost'] = !empty($totalAmountLost) ? $totalAmountLost : 0;

//         // Amount Pending calculation
//         $sum_amount = Complaint::where('acknowledgement_no', (int)$ackNo)->where('com_status', 1)->sum('amount');
//         $hold_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)
//             ->where('com_status', 1)
//             ->where('action_taken_by_bank', 'transaction put on hold')
//             ->sum('transaction_amount');
//         $lost_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)->where('com_status', 1)
//             ->whereIn('action_taken_by_bank', [
//                 'cash withdrawal through cheque',
//                 'withdrawal through atm',
//                 'other',
//                 'wrong transaction',
//                 'withdrawal through pos',
//                 'aadhaar enabled payment System'
//             ])
//             ->sum('dispute_amount');

//         $pending_amount = $sum_amount - $hold_amount - $lost_amount;
//         $filteredResults[$ackNo]['amount_pending'] = $pending_amount;

//         // New logic for pending banks
//         $layer_one_transactions = BankCaseData::where('acknowledgement_no', (int)$ackNo)
//             ->where('Layer', 1)
//             ->where('com_status', 1)
//             ->get();

//         $pending_banks_array = [];
//         for ($i = 1; $i <= count($layer_one_transactions); $i++) {
//             $current_layer = BankCaseData::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i)
//                 ->where('com_status', 1)
//                 ->where('action_taken_by_bank', 'money transfer to')
//                 ->where('bank', '!=', 'Others')
//                 ->get(['transaction_id_sec', 'bank', 'transaction_amount', 'desputed_amount']);

//             $next_layer = BankCaseData::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i + 1)
//                 ->pluck('transaction_id_or_utr_no')
//                 ->toArray();

//             $current_layer_utr = BankCaseData::where('acknowledgement_no', (int)$ackNo)
//                 ->where('Layer', $i)
//                 ->pluck('transaction_id_or_utr_no')
//                 ->toArray();

//             $next_layer_utr_array = $this->extractTransactionIds($next_layer);
//             $current_layer_utr_array = $this->extractTransactionIds($current_layer_utr);

//             foreach ($current_layer as $transaction) {
//                 if ($transaction->transaction_id_sec) {
//                     if (!in_array($transaction->transaction_id_sec, $next_layer_utr_array) &&
//                         !in_array($transaction->transaction_id_sec, $current_layer_utr_array)) {
//                         $pending_banks_array[] = [
//                             "pending_banks" => $transaction->bank,
//                             "transaction_id" => $transaction->transaction_id_sec,
//                             "transaction_amount" => $transaction->transaction_amount,
//                             "desputed_amount" => $transaction->desputed_amount
//                         ];
//                     }
//                 }
//             }
//         }

//         $finalData_pending_banks = [];
//         foreach ($pending_banks_array as $item) {
//             $pendingBank = $item['pending_banks'];
//             $transactionId = $item['transaction_id'];
//             $transactionAmount = $item['transaction_amount'];
//             $desputedAmount = $item['desputed_amount'];

//             $finalData_pending_banks[] = [
//                 'pending_banks' => $pendingBank,
//                 'transaction_id' => $transactionId,
//                 'transaction_amount' => $transactionAmount,
//                 'desputed_amount' => $desputedAmount
//             ];
//         }

//         // Group by bank and summarize
//         $filteredResults[$ackNo]['pending_banks'] = collect($finalData_pending_banks)
//         ->groupBy('pending_banks')
//         ->keys() // Get only the unique bank names
//         ->toArray();
//         $filteredResults[$ackNo]['lien_amount'] = $hold_amount;
//         $filteredResults[$ackNo]['amount_lost'] = $lost_amount;

//         $ACK = (string)$ackNo;
//         $modus = ComplaintAdditionalData::Where('ack_no', $ACK)->pluck('modus')->first();
//         $modus_name = Modus::Where('_id', $modus)->pluck('name')->first();
//         $filteredResults[$ackNo]['modus'] = $modus_name;
//     }

//     // Prepare response
//     return response()->json([
//         'draw' => intval($request->input('draw')),
//         'recordsTotal' => count($filteredResults),
//         'recordsFiltered' => count($filteredResults),
//         'data' => array_values($filteredResults)
//     ]);
// }



public function getAboveData(Request $request)
{
    // Default dates to today if not provided
    $fromDate = $request->input('from_date');
    $to_date = $request->input('to_date');

    // Extract search term from DataTable request
    $search_arr = $request->get('search');
    $searchValue = $search_arr['value'] ?? '';

    // Convert date strings to DateTime objects
    try {
        $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDateEnd = $to_date ? Carbon::parse($to_date)->endOfDay() : null;
        if ($fromDateStart && $toDateEnd) {
            $startdate = new UTCDateTime($fromDateStart->timestamp * 1000);
            $enddate = new UTCDateTime($toDateEnd->timestamp * 1000);
        }
    } catch (\Exception $e) {
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Invalid date format'
        ]);
    }

    // Initial query to get BankCaseData
    $bankCasedataQuery = BankCaseData::whereBetween('transaction_date', [$startdate, $enddate])
        ->where('com_status', 1);

    // Apply search filters if a search term is provided
    if (!empty($searchValue)) {
        $bankCasedataQuery->where(function ($query) use ($searchValue) {
            $query->orWhere('district', 'like', '%' . $searchValue . '%')
                ->orWhere('url', 'like', '%' . $searchValue . '%')
                ->orWhere('domain', 'like', '%' . $searchValue . '%')
                ->orWhere('registrar', 'like', '%' . $searchValue . '%')
                ->orWhere('remarks', 'like', '%' . $searchValue . '%')
                ->orWhere('ip', 'like', '%' . $searchValue . '%')
                ->orWhere('source.name', 'like', '%' . $searchValue . '%');
        });
    }

    $bankCasedata = $bankCasedataQuery->get();

    $acknowledgementNos = $bankCasedata->pluck('acknowledgement_no')->toArray();

    // Fetch complaints details
    $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

    $results = [];
    foreach ($complaints as $complaint) {
        $date = Carbon::parse($complaint['entry_date']);
        $formattedDate = $date->format('d-m-Y');
        $ackNo = (string) $complaint['acknowledgement_no'];
        if (!isset($results[$ackNo])) {
            $results[$ackNo] = [
                'acknowledgement_no' => $ackNo,
                'district' => $complaint['district'],
                'reported_date' => $formattedDate,
                'total_amount' => 0
            ];
        }
        $results[$ackNo]['total_amount'] += $complaint['amount'];
    }

    $filteredResults = array_filter($results, function ($result) {
        return $result['total_amount'] > 100000;
    });

    foreach ($filteredResults as $ackNo => $complaintData) {
        $firstTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
            return $collection->aggregate([
                ['$match' => ['acknowledgement_no' => $ackNo, 'Layer' => 1]],
                ['$sort' => ['transaction_date' => 1]],
                ['$limit' => 1]
            ])->toArray();
        });

        $lastTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
            return $collection->aggregate([
                ['$match' => ['acknowledgement_no' => $ackNo]],
                ['$sort' => ['Layer' => -1, 'transaction_date' => -1]],
                ['$limit' => 1]
            ])->toArray();
        });

        if (!empty($firstTransaction)) {
            $firstTransactionDate = $firstTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
            $filteredResults[$ackNo]['first_transaction_date'] = Carbon::createFromTimestamp($firstTransactionDate / 1000)->format('d-m-Y');
        } else {
            $filteredResults[$ackNo]['first_transaction_date'] = null;
        }

        if (!empty($lastTransaction)) {
            $lastTransactionDate = $lastTransaction[0]['transaction_date']->toDateTime()->getTimestamp() * 1000;
            $filteredResults[$ackNo]['last_transaction_date'] = Carbon::createFromTimestamp($lastTransactionDate / 1000)->format('d-m-Y');
        } else {
            $filteredResults[$ackNo]['last_transaction_date'] = null;
        }

        // Check if the dates are set before using them
        if (isset($filteredResults[$ackNo]['first_transaction_date']) && isset($filteredResults[$ackNo]['last_transaction_date'])) {
            $combained_date = $filteredResults[$ackNo]['first_transaction_date'] . ' - ' . $filteredResults[$ackNo]['last_transaction_date'];
            $filteredResults[$ackNo]['transaction_period'] = $combained_date;
        } else {
            $filteredResults[$ackNo]['transaction_period'] = 'N/A';
        }

        // Lien Amount calculation
        $lienAmount = BankCaseData::where('acknowledgement_no', $ackNo)
            ->where('action_taken_by_bank', 'transaction put on hold')
            ->whereBetween('transaction_date', [$fromDate, $to_date])
            ->sum('transaction_amount');
        $filteredResults[$ackNo]['lien_amount'] = !empty($lienAmount) ? $lienAmount : 0;

        // Amount Lost calculation
        $actions = [
            'Cash Withdrawal Through Cheque',
            'Withdrawal through ATM',
            'Other',
            'Wrong Transaction',
            'Withdrawal through POS'

        ];
        $totalAmountLost = BankCaseData::where('acknowledgement_no', $ackNo)
            ->whereBetween('transaction_date', [$fromDate, $to_date])
            ->whereIn('action_taken_by_bank', $actions)
            ->sum('transaction_amount');
        $filteredResults[$ackNo]['amount_lost'] = !empty($totalAmountLost) ? $totalAmountLost : 0;

        // Amount Pending calculation
        $sum_amount = Complaint::where('acknowledgement_no', (int)$ackNo)->where('com_status', 1)->sum('amount');
        $hold_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)
            ->where('com_status', 1)
            ->where('action_taken_by_bank', 'transaction put on hold')
            ->sum('transaction_amount');
        $lost_amount = BankCaseData::where('acknowledgement_no', (int)$ackNo)->where('com_status', 1)
            ->whereIn('action_taken_by_bank', [
                'cash withdrawal through cheque',
                'withdrawal through atm',
                'other',
                'wrong transaction',
                'withdrawal through pos',
                'aadhaar enabled payment System'
            ])
            ->sum('dispute_amount');

        $pending_amount = $sum_amount - $hold_amount - $lost_amount;
        $filteredResults[$ackNo]['amount_pending'] = $pending_amount;

        // New logic for pending banks
        $layer_one_transactions = BankCaseData::where('acknowledgement_no', (int)$ackNo)
            ->where('Layer', 1)
            ->where('com_status', 1)
            ->get();

        $pending_banks_array = [];
        for ($i = 1; $i <= count($layer_one_transactions); $i++) {
            $current_layer = BankCaseData::where('acknowledgement_no', (int)$ackNo)
                ->where('Layer', $i)
                ->where('com_status', 1)
                ->where('action_taken_by_bank', 'money transfer to')
                ->where('bank', '!=', 'Others')
                ->get(['transaction_id_sec', 'bank', 'transaction_amount', 'desputed_amount']);

            $next_layer = BankCaseData::where('acknowledgement_no', (int)$ackNo)
                ->where('Layer', $i + 1)
                ->pluck('transaction_id_or_utr_no')
                ->toArray();

            $current_layer_utr = BankCaseData::where('acknowledgement_no', (int)$ackNo)
                ->where('Layer', $i)
                ->pluck('transaction_id_or_utr_no')
                ->toArray();

            $next_layer_utr_array = $this->extractTransactionIds($next_layer);
            $current_layer_utr_array = $this->extractTransactionIds($current_layer_utr);


             foreach ($current_layer as $transaction) {
                 if ($transaction->transaction_id_sec) {
                     if (!in_array($transaction->transaction_id_sec, $next_layer_utr_array) &&
                         !in_array($transaction->transaction_id_sec, $current_layer_utr_array)) {
                         $pending_banks_array[] = [
                             "pending_banks" => $transaction->bank,
                             "transaction_id" => $transaction->transaction_id_sec,
                             "transaction_amount" => $transaction->transaction_amount,
                             "desputed_amount" => $transaction->desputed_amount
                         ];
                     }
                 }
             }
         }

            foreach ($current_layer as $transaction) {
                if ($transaction->transaction_id_sec) {
                    if (!in_array($transaction->transaction_id_sec, $next_layer_utr_array) &&
                        !in_array($transaction->transaction_id_sec, $current_layer_utr_array)) {
                        $pending_banks_array[] = [
                            "pending_banks" => $transaction->bank,
                            "pending_bank_amount" => $transaction->transaction_amount,
                            "pending_bank_disputed_amount" => $transaction->desputed_amount
                        ];
                    }
                }
            }


        $finalData_pending_banks = [];
                 foreach ($pending_banks_array as $item) {
                     $pendingBank = $item['pending_banks'];
                    $transactionId = $item['transaction_id'];
                    $transactionAmount = $item['transaction_amount'];
                     $desputedAmount = $item['desputed_amount'];

                     $finalData_pending_banks[] = [
                         'pending_banks' => $pendingBank,
                         'transaction_id' => $transactionId,
                         'transaction_amount' => $transactionAmount,
                         'desputed_amount' => $desputedAmount
                     ];
                 }


        $filteredResults[$ackNo]['pending_banks'] = collect($finalData_pending_banks)
         ->groupBy('pending_banks')
         ->keys() // Get only the unique bank names
         ->toArray();
                  $ACK = (string)$ackNo;
        $modus = ComplaintAdditionalData::Where('ack_no', $ACK)->pluck('modus')->first();
         $modus_name = Modus::Where('_id', $modus)->pluck('name')->first();
         $filteredResults[$ackNo]['modus'] = $modus_name;
    }
    //dd($filteredResults);
    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => count($filteredResults),
        'recordsFiltered' => count($filteredResults),
        'data' => array_values($filteredResults)
    ]);
}

// Helper function to extract transaction IDs from UTR
// private function extractTransactionIds($utrArray)
// {
//     $transactionIds = [];
//     foreach ($utrArray as $utr) {
//         if (is_array($utr)) {
//             $transactionIds = array_merge($transactionIds, array_map('trim', $utr));
//         } else {
//             $transactionIds[] = trim($utr);
//         }
//     }
//     return $transactionIds;
// }










    //////////////////////////////////////////////////////////////////////////////////////


    // public function getAboveData(Request $request)
    // {
    //     $from_date = $request->input('from_date', date('Y-m-d'));
    //     $to_date = $request->input('to_date', date('Y-m-d'));
    //     $results = [];

    //     try {
    //         $from_date_dt = new \DateTime($from_date);
    //         $to_date_dt = new \DateTime($to_date);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Invalid date format'], 400);
    //     }

    //     // Step 1: Get the data
    //     $bankCaseData = BankCaseData::whereBetween('transaction_date', [$from_date, $to_date])
    //         ->where('com_status', 1)
    //         ->where('Layer', 1)
    //         ->get();

    //     // Debugging: Check the retrieved data
    //     if ($bankCaseData->isEmpty()) {
    //         return response()->json(['error' => 'No data found for the specified date range'], 404);
    //     }

    //     $acknowledgementNos = $bankCaseData->pluck('acknowledgement_no')->toArray();

    //     // Step 2: Group by Acknowledgement Numbers and Calculate Sum of Amounts for Layer 1
    //     $acknowledgements = $bankCaseData
    //         ->groupBy('acknowledgement_no')
    //         ->map(function ($items) {
    //             return $items->sum('amount');
    //         })
    //         ->filter(function ($totalAmount) {
    //             return $totalAmount > 100000;
    //         });

    //     // Debugging: Check the grouped and filtered data
    //     if ($acknowledgements->isEmpty()) {
    //         return response()->json(['error' => 'No acknowledgements found with amount greater than 100000'], 404);
    //     }

    //     // Step 3: Fetch the Corresponding Complaints and Transaction Dates
    //     $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgements->keys()->toArray())->get();

    //     // Debugging: Check the complaints data
    //     if ($complaints->isEmpty()) {
    //         return response()->json(['error' => 'No complaints found for the specified acknowledgements'], 404);
    //     }

    //     $complaintsByAcknowledgementNo = [];
    //     foreach ($complaints as $complaint) {
    //         $complaintsByAcknowledgementNo[(string) $complaint['acknowledgement_no']] = $complaint;
    //     }

    //     foreach ($acknowledgements as $ackNo => $totalAmount) {
    //         // Get the first transaction date in Layer 1
    //         $firstTransaction = BankCaseData::where('acknowledgement_no', $ackNo)
    //             ->where('Layer', 1)
    //             ->orderBy('transaction_date', 'asc')
    //             ->first();

    //         $firstTransactionDate = $firstTransaction ? $firstTransaction->transaction_date : null;

    //         // Get the last layer
    //         $lastLayer = BankCaseData::where('acknowledgement_no', $ackNo)
    //             ->orderBy('Layer', 'desc')
    //             ->first();

    //         $lastLayerNumber = $lastLayer ? $lastLayer->Layer : null;

    //         // Get the transaction date in the last layer
    //         $lastTransaction = BankCaseData::where('acknowledgement_no', $ackNo)
    //             ->where('Layer', $lastLayerNumber)
    //             ->orderBy('transaction_date', 'desc')
    //             ->first();

    //         $lastTransactionDate = $lastTransaction ? $lastTransaction->transaction_date : null;

    //         $district = $complaintsByAcknowledgementNo[$ackNo]['district'] ?? null;
    //         $entry_date = $complaintsByAcknowledgementNo[$ackNo]['entry_date'] ?? null;
    //         $entry_date_dt = new \DateTime($entry_date);
    //         $entry_date = $entry_date_dt->format('d M Y H:i:s');

    //         if ($district) {
    //             $results[$ackNo] = [
    //                 'acknowledgement_no' => $ackNo,
    //                 'district' => $district,
    //                 'reported_date' => $entry_date,
    //                 'total_amount' => $totalAmount,
    //                 'first_transaction_date' => $firstTransactionDate,
    //                 'last_transaction_date' => $lastTransactionDate,
    //             ];
    //         }
    //     }

    //     // Implement server-side processing logic for DataTables
    //     $data = array_values($results);
    //     $draw = intval($request->input('draw'));
    //     $start = intval($request->input('start'));
    //     $length = intval($request->input('length'));

    //     // Apply pagination
    //     $data = array_slice($data, $start, $length);

    //     return response()->json([
    //         'draw' => $draw,
    //         'recordsTotal' => count($results),
    //         'recordsFiltered' => count($results),
    //         'data' => $data,
    //     ]);
    // }
    public function getAboveDataOld(Request $request)
    {
        // Default dates to today if not provided
        $from_date = $request->input('from_date', date('Y-m-d'));
        $to_date = $request->input('to_date', date('Y-m-d'));

        // Convert date strings to DateTime objects
        try {
            $from_date_dt = new \DateTime($from_date);
            $to_date_dt = new \DateTime($to_date);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Invalid date format'
            ]);
        }

        // Format dates to Y-m-d
        $from_date = $from_date_dt->format('Y-m-d');
        $to_date = $to_date_dt->format('Y-m-d');

        \Log::info("Fetching data between {$from_date} and {$to_date}");

//dd($totalAmountOnHold);

      //  \Log::info('Total amount on hold:', ['total' => $totalAmountOnHold]);
//dd($totalAmountOnHold);

//dd($totalAmountOnHold);


        // Aggregation for total amount lost
        $actions = [
            'cash withdrawal through cheque',
            'withdrawal through atm',
            'other',
            'wrong transaction',
            'withdrawal through pos'
        ];

        $totalAmountLost = BankCaseData::raw(function ($collection) use ($from_date, $to_date, $actions) {
            return $collection->aggregate([
                ['$match' => [
                    'transaction_date' => ['$gte' => $from_date, '$lte' => $to_date],
                    'current_status' => ['$in' => $actions]
                ]],
                ['$group' => ['_id' => null, 'total' => ['$sum' => '$amount']]]
            ])->toArray();
        });

        \Log::info('Total amount lost:', ['total' => $totalAmountLost]);

        $totalAmountLost = !empty($totalAmountLost) ? $totalAmountLost[0]['total'] : 0;

        // Fetch complaints for acknowledgment numbers with total amount > 100000
        $bankCaseData = BankCaseData::raw(function ($collection) use ($from_date, $to_date) {
            return $collection->aggregate([
                ['$match' => [
                    'transaction_date' => ['$gte' => $from_date, '$lte' => $to_date],
                    'com_status' => 1,
                    'Layer' => 1
                ]],
                ['$group' => ['_id' => '$acknowledgement_no', 'total_amount' => ['$sum' => '$amount']]]
            ])->toArray();
        });
//dd($bankCaseData);
        \Log::info('Bank case data:', ['data' => $bankCaseData]);

        if (empty($bankCaseData)) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
               // 'error' => 'No bank case data found for the specified criteria'
            ]);
        }

        $acknowledgementNos = array_column($bankCaseData, '_id');

        // Fetch complaints details
        $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

        \Log::info('Complaints:', ['data' => $complaints]);

        if ($complaints->isEmpty()) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No complaints found for the specified acknowledgements'
            ]);
        }

        $results = [];
        foreach ($complaints as $complaint) {
            $ackNo = (string) $complaint['acknowledgement_no'];
            if (!isset($results[$ackNo])) {
                $results[$ackNo] = [
                    'acknowledgement_no' => $ackNo,
                    'district' => $complaint['district'],
                    'reported_date' => $complaint['entry_date'],
                    'total_amount' => 0
                ];
            }
            $results[$ackNo]['total_amount'] += $complaint['amount'];
        }

        \Log::info('Results before filtering:', ['results' => $results]);

        // Filter results with total_amount > 100000
        $filteredResults = array_filter($results, function ($result) {
            return $result['total_amount'] > 100000;
        });

        \Log::info('Filtered results:', ['results' => $filteredResults]);

        if (empty($filteredResults)) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No complaints found with total amount greater than 100000'
            ]);
        }

        foreach ($filteredResults as $ackNo => $complaintData) {
            $firstTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
                return $collection->aggregate([
                    ['$match' => ['acknowledgement_no' => $ackNo, 'Layer' => 1]],
                    ['$sort' => ['transaction_date' => 1]],
                    ['$limit' => 1]
                ])->toArray();
            });

            $lastTransaction = BankCaseData::raw(function ($collection) use ($ackNo) {
                return $collection->aggregate([
                    ['$match' => ['acknowledgement_no' => $ackNo]],
                    ['$sort' => ['Layer' => -1, 'transaction_date' => -1]],
                    ['$limit' => 1]
                ])->toArray();
            });

            if (!empty($firstTransaction)) {
                $filteredResults[$ackNo]['first_transaction_date'] = $firstTransaction[0]['transaction_date'];
            }

            if (!empty($lastTransaction)) {
                $filteredResults[$ackNo]['last_transaction_date'] = $lastTransaction[0]['transaction_date'];
            }
            $combained_date = $filteredResults[$ackNo]['first_transaction_date'] . ' - ' . $filteredResults[$ackNo]['last_transaction_date'];
            $filteredResults[$ackNo]['transaction_period']=$combained_date;

            //Pending Banks


        }

        \Log::info('Final results:', ['results' => $filteredResults]);

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => count($filteredResults),
            'recordsFiltered' => count($filteredResults),
            'data' => array_values($filteredResults),
        ]);
    }












    /**
     * Show the form for creating a new transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bank_reports.create');
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     private function extractTransactionIds($transactions){
        $transaction_ids = [];
        foreach ($transactions as $transaction) {
            $transaction_ids = array_merge($transaction_ids, array_map('trim', explode(',', trim($transaction, '[]'))));
        }
        return array_map('trim', $transaction_ids);
    }
     public function checkifempty($layer, $first_rows, $id, &$processed_ids = [])
     {
         $layer++;

         $main_array = [];

         foreach ($first_rows as $first_row) {
             if ($first_row['transaction_id_sec'] != null) {
                 if (in_array($first_row['transaction_id_sec'], $processed_ids)) {
                     continue; // Skip processing if already processed
                 }
             }

             // Add current transaction_id_sec to processed list
             $processed_ids[] = $first_row['transaction_id_sec'];

             // Add current first row to main array
             $main_array[] = $first_row;

             $next_layer_rows = BankCasedata::where('acknowledgement_no', (int)$id)
                 ->where('Layer', $layer)
                 ->where('transaction_id_or_utr_no', 'like', '%' . $first_row['transaction_id_sec'] . '%')
                 ->get()
                 ->toArray();

             $same_layer_rows = BankCasedata::where('acknowledgement_no', (int)$id)
                 ->where('Layer', $layer - 1)
                 ->where('transaction_id_or_utr_no', 'like', '%' . $first_row['transaction_id_sec'] . '%')
                 ->get()
                 ->toArray();

             if (!empty($next_layer_rows)) {
                 if ($first_row['transaction_id_sec'] === null) {
                     continue;
                 }

                 $nested_results = $this->checkifempty($layer, $next_layer_rows, $id, $processed_ids);
                 $main_array = array_merge($main_array, $nested_results);
             } elseif (!empty($same_layer_rows)) {
                 if ($first_row['transaction_id_sec'] === null) {
                     continue;
                 }

                 $nested_results = $this->checkifempty($layer - 1, $same_layer_rows, $id, $processed_ids);
                 $main_array = array_merge($main_array, $nested_results);
             }
         }

         return $main_array;
     }

}
