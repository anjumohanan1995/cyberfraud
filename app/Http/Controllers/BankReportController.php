<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Bank;
use App\Models\Modus;
use League\Csv\Writer;
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

    $complaints = Complaint::whereBetween('entry_date', [$startdate, $enddate])
    ->where('com_status', 1)
    ->get();
    //dd($complaints);
    $acknowledgementNos = $complaints->pluck('acknowledgement_no')->toArray();

    $bankCasedata = BankCaseData::whereIn('acknowledgement_no', $acknowledgementNos)->where('com_status', 1)->get();
//dd($bankCasedata);

    //$complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

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
                    'total_holds' => 0,
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
                    //dd($entry_date_dt->format('Y-m-d'));
                    $transaction_date_dt = new \DateTime($data->transaction_date->toDateTime()->format('Y-m-d H:i:s'));
                    //dd($transaction_date_dt->format('Y-m-d'), $entry_date_dt->format('Y-m-d'));
                    if ($data->Layer == 1 && $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d')) {
                        $results[$district]['actual_amount_lost_on'] += $data->transaction_amount;
                    }

                    if ($data->action_taken_by_bank == "transaction put on hold") {
                       // dd($data->transaction_amount);
                        //dd($data->action_taken_by_bank);
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
                        $results[$district]['total_amount_lost_from_eco'] += $data->dispute_amount;
                    }
                    $results[$district]['total_holds'] = $results[$district]['actual_amount_hold_on'] + $results[$district]['hold_amount_otherthan'];
                    $results[$district]['amount_for_pending_action'] = $results[$district]['actual_amount'] - $results[$district]['total_holds'] - $results[$district]['total_amount_lost_from_eco'];
                    $results[$district]['total'] = $results[$district]['1930_count'] + $results[$district]['NCRP_count'];
                    // $actions = [
                    //     'Cash Withdrawal Through Cheque',
                    //     'Withdrawal through ATM',
                    //     'Other',
                    //     'Wrong Transaction',
                    //     'Withdrawal through POS'
                    // ];
                    // $totalAmountLost = BankCaseData::where('acknowledgement_no', $ackNo)
                    // ->whereBetween('transaction_date', [$fromDateStart, $toDateEnd])
                    //     ->whereIn('action_taken_by_bank', $actions)
                    //     ->sum('transaction_amount');
                    //     $results[$district]['total_amount_lost_from_eco'] = $totalAmountLost;

                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error parsing date: ' . $e->getMessage()], 400);
                }
            }
        }
        foreach ($results as $result) {
            //dd($result);
            //dd($result['actual_amount']);
            $result['total_holds'] = $result['actual_amount_hold_on'] + $result['hold_amount_otherthan'];
            $result['amount_for_pending_action'] = $result['actual_amount'] - $result['total_holds'] - $result['total_amount_lost_from_eco'];
            //$result['amount_for_pending_action'] = round($result['amount_for_pending_actions'], 2);
            $result['total'] = $result['1930_count'] + $result['NCRP_count'];
        }
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




public function getAboveData(Request $request)
{
    //dd($request->all());
    // Default dates to today if not provided
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $format = $request->input('format');
    //dd($format);
    // Extract search term from DataTable request
    $search_arr = $request->get('search');
    $searchValue = $search_arr['value'] ?? '';

    // Extract amount and operator from request
    $amount = (int) $request->input('amount', 100000);
    $operator = $request->input('amount_operator', '>');
    //dd($amount, $operator);
    // Convert date strings to DateTime objects
    try {
        $fromDateStart = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
        $toDateEnd = $toDate ? Carbon::parse($toDate)->endOfDay() : null;
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

    // Initial query to get BankCaseData based on entry_date from complaints table
    $complaintsQuery = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
        ->where('com_status', 1);
    //dd($complaintsQuery);
    // Apply search filters if a search term is provided
    if (!empty($searchValue)) {
        $complaintsQuery->where(function ($query) use ($searchValue) {
            $query->orWhere('district', 'like', '%' . $searchValue . '%')
                ->orWhere('url', 'like', '%' . $searchValue . '%')
                ->orWhere('domain', 'like', '%' . $searchValue . '%')
                ->orWhere('registrar', 'like', '%' . $searchValue . '%')
                ->orWhere('remarks', 'like', '%' . $searchValue . '%')
                ->orWhere('ip', 'like', '%' . $searchValue . '%')
                ->orWhere('source.name', 'like', '%' . $searchValue . '%');
        });
    }

    $complaints = $complaintsQuery->get();

    $results = [];
    foreach ($complaints as $complaint) {
        $date = Carbon::parse($complaint['entry_date']);
        $formattedDate = $date->format('d-m-Y H:i:s');
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

    // Apply amount filter using operator and amount
    $filteredResults = array_filter($results, function ($result) use ($amount, $operator) {
        switch ($operator) {
            case '>':
                //dd($result['total_amount'], $amount);
                return $result['total_amount'] > $amount;
            case '>=':
                return $result['total_amount'] >= $amount;
            case '<':
                return $result['total_amount'] < $amount;
            case '<=':
                return $result['total_amount'] <= $amount;
            case '=':
                return $result['total_amount'] == $amount;
            default:
                return true;
        }
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


        // Lien Amount calculation
        $lienAmount = BankCaseData::where('acknowledgement_no', $ackNo)
            ->where('action_taken_by_bank', 'transaction put on hold')
            ->whereBetween('transaction_date', [$fromDateStart, $toDateEnd])
            ->sum('transaction_amount');
            //dd($lienAmount);
        //$filteredResults[$ackNo]['lien_amount'] = !empty($lienAmount) ? $lienAmount : 0;

        // Amount Lost calculation
        $actions = [
            'Cash Withdrawal Through Cheque',
            'Withdrawal through ATM',
            'Other',
            'Wrong Transaction',
            'Withdrawal through POS'
        ];
        $totalAmountLost = BankCaseData::where('acknowledgement_no', $ackNo)
        ->whereBetween('transaction_date', [$fromDateStart, $toDateEnd])
            ->whereIn('action_taken_by_bank', $actions)
            ->sum('transaction_amount');
        $filteredResults[$ackNo]['amount_lost'] = !empty($totalAmountLost) ? $totalAmountLost : 0;

        // Amount Pending calculation
        $sum_amount = Complaint::where('acknowledgement_no', (int)$ackNo)->where('com_status', 1)->sum('amount');
        //dd($sum_amount);
        $results[$ackNo]['total_amount'] = $sum_amount;
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
            ->sum('transaction_amount');

        $pending_amount = $sum_amount - $hold_amount - $lost_amount;

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

            foreach ($current_layer as $current_data) {
                if (!in_array($current_data['transaction_id_sec'], $next_layer_utr_array)
                    && in_array($current_data['transaction_id_sec'], $current_layer_utr_array)) {
                    $pending_banks_array[] = $current_data['bank'];
                }
            }
        }
       // $filteredResults[$ackNo]['pending_banks'] = $pending_banks_array;
       $unique_pending_banks = array_unique($pending_banks_array);
        $concatenated_banks = implode(', ', $unique_pending_banks);

        $ACK = (string)$ackNo;
        $modus = ComplaintAdditionalData::Where('ack_no', $ACK)->pluck('modus')->first();
        $modus_name = Modus::Where('_id', $modus)->pluck('name')->first();
        $filteredResults[$ackNo]['pending_banks'] = $concatenated_banks;
        $filteredResults[$ackNo]['modus'] = $modus_name;
        $filteredResults[$ackNo]['amount_pending'] = $pending_amount;
        $filteredResults[$ackNo]['lien_amount'] = $hold_amount;
        $filteredResults[$ackNo]['amount_lost'] = $lost_amount;
        if (isset($filteredResults[$ackNo]['first_transaction_date']) && isset($filteredResults[$ackNo]['last_transaction_date'])) {
            $combinedDate = $filteredResults[$ackNo]['first_transaction_date'] . ' - ' . $filteredResults[$ackNo]['last_transaction_date'];
            $filteredResults[$ackNo]['transaction_period'] = $combinedDate;
        } else {
            $filteredResults[$ackNo]['transaction_period'] = 'N/A';
        }



    }

    $formattedResults = array_values($filteredResults);
    //dd($formattedResults);
if($format == 'csv'){


    if (empty($formattedResults)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for CSV export'], 422);
    }

    // Create a CSV writer
    $csv = Writer::createFromString('');
    $csv->insertOne([
         "Sl.no", "Acknowledgement No", "District", "Reported Date & Time", "Amount Reported",
        "Transaction Date", "Lien Amount", "Amount Lost", "Amount Pending", "Pending Banks"
    ]);
//print_r($data_arr_print);
    foreach ($formattedResults as $key => $row) {
        $row["Sl.no"] = $key + 1;
        $csv->insertOne([
            $row["Sl.no"], $row["acknowledgement_no"], $row["district"], $row["reported_date"],
            $row["total_amount"], $row["transaction_period"], $row["lien_amount"], $row["amount_lost"], $row["amount_pending"], $row["pending_banks"], $row["modus"],

        ]);
    }

    $csvOutput = $csv->toString();
    return response($csvOutput, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="Ncrp case data.csv"',
    ]);



}elseif($format == 'excel'){
    if (empty($formattedResults)) {
        // Return JSON response with the error message
        return response()->json(['errorMessage' => 'No data available for Excel export'], 422);
    }

    // Define the headings for the Excel file
    $headings = [
        "Sl.no", "Acknowledgement No", "District", "Reported Date & Time", "Amount Reported",
        "Transaction Date", "Lien Amount", "Amount Lost", "Amount Pending", "Pending Banks"
    ];

    // Remove 'id' field from data_arr_print
    $formattedResults = array_map(function ($row) {
        unset($row['Sl.no']);
        return $row;
    }, $formattedResults);

    // Generate and return Excel file with specified headings
    return Excel::download(new \App\Exports\ComplaintExport($formattedResults, $headings), 'Ncrp case data.xlsx');
}
    // Implement server-side processing logic for DataTables
    $draw = intval($request->input('draw'));
    $start = intval($request->input('start'));
    $length = intval($request->input('length'));


    // Apply pagination
    $paginatedResults = array_slice($formattedResults, $start, $length);

    return response()->json([
        'draw' => $draw,
        'recordsTotal' => count($results), // Total number of records without any filtering
        'recordsFiltered' => count($filteredResults), // Total number of records after filtering
        'data' => array_values($paginatedResults),
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
