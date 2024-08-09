<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintOthers;
use App\Models\BankCasedata;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardPagesController extends Controller
{


    public function dashboard(){

    $totalComplaints = Complaint::groupBy('acknowledgement_no')->get()->count();

    $totalOtherComplaints = ComplaintOthers::groupBy('case_number')->get()->count();
    // Get the current date in ISO 8601 format
    // $currentDate = now()->startOfDay();
    // $nextDate = now()->addDay()->startOfDay();

    // Create MongoDB UTCDateTime objects for the date range
    // $currentDateUTC = new \MongoDB\BSON\UTCDateTime($currentDate->timestamp * 1000);
    // $nextDateUTC = new \MongoDB\BSON\UTCDateTime($nextDate->timestamp * 1000);

    // $newComplaints = Complaint::where('entry_date', '>=', $currentDateUTC)
    //                            ->where('entry_date', '<', $nextDateUTC)
    //                            ->groupBy('acknowledgement_no')
    //                            ->get()
    //                            ->count();
    // dd($newComplaints);

// Exclude specific values from action_taken_by_bank
// $filteredQuery = BankCasedata::whereNull('deleted_at')
//     ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
//     ->where(function($query) {
//         $query->whereRaw(['$expr' => ['$ne' => [['$trim' => ['input' => ['$toLower' => '$action_taken_by_bank']]], ""]]]);
//     });

// Get frequent account numbers
$frequentAccountNumbers = BankCasedata::raw(function ($collection) {
    return $collection->aggregate([
        [
            '$addFields' => [
                'sanitized_account_no_2' => [
                    '$arrayElemAt' => [
                        ['$split' => ['$account_no_2', ' [ Reported ']],
                        0
                    ]
                ]
            ]
        ],
        [
            '$addFields' => [
                'reported_count' => [
                    '$arrayElemAt' => [
                        ['$split' => [
                            ['$arrayElemAt' => [
                                ['$split' => ['$account_no_2', ' [ Reported ']],
                                1
                            ]],
                            ' times ]'
                        ]],
                        0
                    ]
                ]
            ]
        ],
        [
            '$addFields' => [
                'reported_count' => ['$toInt' => '$reported_count']
            ]
        ],
        [
            '$group' => [
                '_id' => '$sanitized_account_no_2',
                'count' => ['$sum' => 1]
            ]
        ],
        [
            '$match' => [
                'count' => ['$gte' => 3]
            ]
        ]
    ]);
})->pluck('_id')->toArray();

// Fetch cases where Layer is 1
$layer1Cases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where('Layer', 1)
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->get();

// Extract acknowledgement numbers from Layer 1 cases
$layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();

// Fetch cases where Layer is not 1 and account_no_2 is in frequentAccountNumbers
$otherLayerCases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where('Layer', '!=', 1)
    ->whereIn('account_no_2', $frequentAccountNumbers)
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->get();

// Combine results
$cases = $layer1Cases->merge($otherLayerCases);

$muleAccountCount = $cases->count();



            // dd($muleAccountCount);

    //pending amount calculation
    $sum_amount=0;$hold_amount=0;$lost_amount=0;$pending_amount=0;
    $sum_amount = Complaint::where('com_status',1)->sum('amount');
    $hold_amount = BankCaseData::where('com_status',1)
        ->where('action_taken_by_bank','transaction put on hold')->sum('transaction_amount');

    $lost_amount = BankCaseData::where('com_status',1)
                                ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos'])
                                ->sum('transaction_amount');
    $pending_amount = $sum_amount - $hold_amount - $lost_amount;

        return view('dashboard.dashboard',compact('totalComplaints', 'totalOtherComplaints', 'muleAccountCount','pending_amount'));
    }

}
