<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankCasedata;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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


    public function getBankDetailsByDate(Request $request)
    {
        // Get date inputs and validate them
        $from_date = $request->input('from_date', date('Y-m-d'));
        $to_date = $request->input('to_date', date('Y-m-d'));

        try {
            $from_date_dt = new \DateTime($from_date);
            $to_date_dt = new \DateTime($to_date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Get the data
        $bankCasedata = BankCaseData::whereBetween('transaction_date', [$from_date, $to_date])
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
                        $utc_date = Carbon::parse($entry_date, 'UTC')->setTimezone('Asia/Kolkata');
                        $entry_date = $utc_date->format('Y-m-d H:i:s');

                        $entry_date_dt = new \DateTime($entry_date);
                        $transaction_date_dt = new \DateTime($data->transaction_date);
                        // echo "<pre>";
    // print_r($data->action_taken_by_bank);
    if ($data->Layer == 1 && $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d')) {
        $results[$district]['actual_amount_lost_on'] += $data->transaction_amount;
    }
    //dd($transaction_date_dt, $entry_date_dt, $data->Layer, $transaction_date_dt->format('Y-m-d') === $entry_date_dt->format('Y-m-d'));
                        if ($data->action_taken_by_bank == "transaction put on hold") {
                            if($transaction_date_dt->format('Y-m-d') == $entry_date_dt->format('Y-m-d')){
                            $results[$district]['actual_amount_hold_on'] += $data->transaction_amount;
                        }}
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
       // dd($results);
        // Convert results array to a format DataTables expects
        $data = array_values($results);

        // Implement server-side processing logic for DataTables
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));

        // Apply pagination
        $data = array_slice($data, $start, $length);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $data,
        ]);
    }



    //////////////////////////////////////////////////////////////////////////////////

    public function getAboveData(Request $request)
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

   // Fetch complaints for acknowledgment numbers with total amount > 100000
   $bankCaseData = BankCaseData::raw(function ($collection) use ($from_date, $to_date) {
    return $collection->aggregate([
        ['$match' => [
            'transaction_date' => ['$gte' => $from_date, '$lte' => $to_date],
            'com_status' => 1,
            // 'Layer' => 1
        ]],
        ['$group' => ['_id' => '$acknowledgement_no']]
    ])->toArray();
});


if (empty($bankCaseData)) {
    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'No bank case data found for the specified criteria'
    ]);
}
        $acknowledgementNos = array_column($bankCaseData, '_id');

        // Fetch complaints details
        $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

        if ($complaints->isEmpty()) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No complaints found for the specified acknowledgements'
            ]);
        }
        // Total Reported Amount
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

        $filteredResults = array_filter($results, function ($result) {
            return $result['total_amount'] > 100000;
        });
//validate above 100000
        if (empty($filteredResults)) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No complaints found with total amount greater than 100000'
            ]);
        }

        // Transaction Dates

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
            //Lien Amount calculation
            //dd($ackNo);
            $lienAmount = BankCaseData::where('acknowledgement_no', $ackNo)
                ->where('action_taken_by_bank', 'transaction put on hold')
                ->whereBetween('transaction_date', [$from_date, $to_date])
                ->sum('transaction_amount');

            // Since sum returns an integer, directly use it
            $lienAmount = !empty($lienAmount) ? $lienAmount : 0;

            $filteredResults[$ackNo]['lien_amount'] = $lienAmount;


            //Amount lost calculation

            $actions = [
                'cash withdrawal through cheque',
                'withdrawal through atm',
                'other',
                'wrong transaction',
                'withdrawal through pos'
            ];


            $totalAmountLost = BankCaseData::Where('acknowledgement_no', $ackNo)->WhereBetween('transaction_date', [$from_date, $to_date])->WhereIn('action_taken_by_bank', $actions)->sum('transaction_amount');
           //dd($totalAmountLost);
            $totalAmountLost = !empty($totalAmountLost) ? $totalAmountLost : 0;
            $filteredResults[$ackNo]['amount_lost'] = $totalAmountLost;

            //Amount Pending calculation
            $filteredResults[$ackNo]['amount_pending'] = max(0,$filteredResults[$ackNo]['total_amount'] - $filteredResults[$ackNo]['amount_lost'] - $filteredResults[$ackNo]['lien_amount']);
            //dd($filteredResults[$ackNo]['amount_pending']);
           // dd($ackNo);
            $bankNames = BankCaseData::Where('acknowledgement_no', $ackNo)->Where('action_taken_by_bank', 'money transfer to')->groupBy('bank')->pluck('bank')->toArray();
            //dd($bankNames);
            //validation for empty array instead of [null]
            if ($bankNames == [null]) {
                $bankNames = 'No pending banks';
            }
            $filteredResults[$ackNo]['pending_banks'] = $bankNames;



        }





        //dd($filteredResults[$ackNo]['lien_amount']);



        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => count($filteredResults),
            'recordsFiltered' => count($filteredResults),
            'data' => array_values($filteredResults),
        ]);






    }



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
                'error' => 'No bank case data found for the specified criteria'
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

}
