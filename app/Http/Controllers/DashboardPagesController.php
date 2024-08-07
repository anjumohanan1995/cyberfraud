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
$filteredQuery = BankCasedata::whereNull('deleted_at')
    ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where(function($query) {
        $query->whereRaw(['$expr' => ['$ne' => [['$trim' => ['input' => ['$toLower' => '$action_taken_by_bank']]], ""]]]);
    });

// Get frequent account numbers
$frequentAccountNumbers = BankCasedata::raw(function ($collection) {
    return $collection->aggregate([
        [
            '$project' => [
                'sanitized_account_no_2' => [
                    '$cond' => [
                        'if' => ['$regexMatch' => ['input' => '$account_no_2', 'regex' => '\[ Reported \d+ times \]']],
                        'then' => [
                            '$substr' => [
                                '$account_no_2',
                                0,
                                ['$indexOfBytes' => ['$account_no_2', ' [ Reported ']]
                            ]
                        ],
                        'else' => '$account_no_2'
                    ]
                ]
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

// Get mule account count based on frequent account numbers
$muleAccountCount = $filteredQuery->where(function ($query) use ($frequentAccountNumbers) {
    $query->where('Layer', 1)
        ->orWhereIn('account_no_2', $frequentAccountNumbers)
        ->whereNotNull('account_no_2');
})->groupBy('account_no_2')
  ->pluck('account_no_2')
  ->unique()
  ->count();

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
