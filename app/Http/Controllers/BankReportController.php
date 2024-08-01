<?php

namespace App\Http\Controllers;
use App\Models\BankCasedata;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Complaint;
use Carbon\Carbon;
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

public function getAboveData(Request $request){
    $from_date = $request->input('from_date', date('Y-m-d'));
    $to_date = $request->input('to_date', date('Y-m-d'));
    $results = [];
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
    //dd($complaints, $acknowledgementNos);
    foreach ($bankCasedata as $data) {
       // print_r($data);
        $acknowledgementNo = $data->acknowledgement_no;
        $district = $complaintsByAcknowledgementNo[$acknowledgementNo]->district ?? null;
        $entry_date = $complaintsByAcknowledgementNo[$acknowledgementNo]->entry_date ?? null;
        $entry_date_dt = new \DateTime($entry_date);
        $entry_date = $entry_date_dt->format('Y-m-d H:i:s');
//dd($district);
    if ($district) {
        if (!isset($results[$district])) {
            $results[$district] = [
                'acknowledgement_no' => $acknowledgementNo,
                'district' => $district,
                'reported_date' => $entry_date,
            ];
        }

    }

}
 //dd($results);
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
