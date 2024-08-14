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

// Retrieve all documents with non-null account_no_2
$documents = BankCasedata::whereNotNull('account_no_2')->get();

$accountNumbers = [];
foreach ($documents as $doc) {
    if (isset($doc['account_no_2'])) {
        preg_match('/(\d+)/', $doc['account_no_2'], $matches);
        if (!empty($matches[1])) {
            $number = $matches[1];
            $accountNumbers[$number] = ($accountNumbers[$number] ?? 0) + 1;
        }
    }
}

$frequentAccountNumbers = array_filter($accountNumbers, function ($count) {
    return $count > 2;
});

$frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);

// Fetch Layer 1 cases
$layer1Cases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where('Layer', 1)
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->get();

$layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();

$accountNumberPatterns = array_map(function ($number) {
    return "^$number\\b";
}, $frequentAccountNumbersKeys);

// Fetch other layer cases
$otherLayerCases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->whereNotIn('acknowledgement_no', $layer1AcknowledgementNos)
    ->where('Layer', '!=', 1)
    ->where(function ($query) use ($accountNumberPatterns) {
        foreach ($accountNumberPatterns as $pattern) {
            $query->orWhere('account_no_2', 'regexp', $pattern);
        }
    })
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->get();

    $filterDuplicates = function ($cases) {
        return $cases->unique(function ($case) {
            return $case->acknowledgement_no . '-' . $case->account_no_2;
        });
    };


$layer1Cases = $filterDuplicates($layer1Cases);
$otherLayerCases = $filterDuplicates($otherLayerCases);

// Group other layer cases by account_no_2
$groupedOtherLayerCases = $otherLayerCases->groupBy(function ($case) {
    return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
});

// Filter to keep only groups with more than one unique acknowledgement_no
$validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
    return $group->pluck('acknowledgement_no')->unique()->count() > 1;
});

// Merge layer 1 and valid other layer cases
$allCases = $layer1Cases->merge($validOtherLayerCases->flatten(1));

// Group by account_no_2 and remove duplicates
$groupedCases = $allCases->groupBy(function ($case) {
    return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
});


// Ensure each group is unique by account_no_2
$uniqueCases = $groupedCases->map(function ($group) {
    return $group->first();
});

// Apply search filter if search value is present
if (!empty($searchValue)) {
    $uniqueCases = $uniqueCases->filter(function ($item) use ($searchValue) {
        return stripos($item->account_no_2, $searchValue) !== false;
    });
}

$muleAccountCount = $uniqueCases->count();
// Count the number of unique cases



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
