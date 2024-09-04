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
use MongoDB\BSON\Regex;

class DashboardPagesController extends Controller
{


    public function dashboard(){

    $totalComplaints = Complaint::groupBy('acknowledgement_no')->get()->count();

    $totalOtherComplaints = ComplaintOthers::groupBy('case_number')->get()->count();


    $acknowledgementNos = Complaint::pluck('acknowledgement_no')->toArray();
    // dd($acknowledgementNos);
    $documents = BankCasedata::whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->get();
    // dd($documents);

    // Count occurrences of account_no_2 with different acknowledgment numbers
    $accountCounts = [];
    foreach ($documents as $doc) {
    preg_match('/(\d+)/', $doc->account_no_2, $matches);
    if (!empty($matches[1])) {
    $number = $matches[1];
    if (!isset($accountCounts[$number])) {
    $accountCounts[$number] = [];
    }
    $accountCounts[$number][] = $doc->acknowledgement_no;
    }
    }
    // dd($accountCounts);

    // Filter account_no_2 that repeat more than twice with different acknowledgment numbers
    $frequentAccountNumbers = array_filter($accountCounts, function($acknos) {
    return count(array_unique($acknos)) > 2;
    });
    // dd($frequentAccountNumbers);

    $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);
    // dd($frequentAccountNumbersKeys);

    $frequentAccountNumbers = array_filter($frequentAccountNumbersKeys, function($count) {
    return $count > 2;
    });

    // dd($frequentAccountNumbers);

    $layer1Cases = BankCasedata::where('Layer', 1)
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->whereIn('acknowledgement_no', $acknowledgementNos)
    ->get();

    // dd($layer1Cases);

    $layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();
    // dd($layer1AcknowledgementNos);

    $accountNumberPatterns = array_map(function($number) {
    return new Regex("^$number\\b", ''); // Match the start of the string
    }, $frequentAccountNumbersKeys);

    // dd($accountNumberPatterns);

    $otherLayerCases = BankCasedata::where('Layer', '!=', 1)
    ->where(function($query) use ($accountNumberPatterns) {
        foreach ($accountNumberPatterns as $pattern) {
            $query->orWhere('account_no_2', 'regexp', $pattern);
        }
    })
    ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->whereIn('acknowledgement_no', $acknowledgementNos)
    ->get();


    $withdrawalCases = BankCasedata::where('Layer', '!=', 1)
    ->whereNotNull('account_no_2')
    ->where('account_no_2', '!=', '')
    ->whereIn('action_taken_by_bank', ['withdrawal through atm', 'cash withdrawal through cheque'])
    ->whereIn('acknowledgement_no', $acknowledgementNos)
    ->get();


        // Remove duplicates based on account_no_2 and acknowledgment_no
        $filterDuplicates = function ($cases) {
            return $cases->unique(function ($case) {
                return $case->acknowledgement_no . '-' . $case->account_no_2;
            });
        };


        $layer1Cases = $filterDuplicates($layer1Cases);
        $otherLayerCases = $filterDuplicates($otherLayerCases);
        $withdrawalCases = $filterDuplicates($withdrawalCases);

        // Group other layer cases by account_no_2
        $groupedOtherLayerCases = $otherLayerCases->groupBy(function ($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });
                    // dd($groupedOtherLayerCases);

        // Filter valid other layer cases
        $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
            return $group->pluck('acknowledgement_no')->unique()->count() >=1;
        });
        // dd($validOtherLayerCases);

        // Merge Layer 1 and valid other layer cases
        $merge=$layer1Cases->merge($withdrawalCases);
        $allCases = $merge->merge($validOtherLayerCases->flatten(1));
        // dd($allCases);

        // Group by account_no_2 and remove duplicates
        $groupedCases = $allCases->groupBy(function ($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });
        // dd($groupedCases);

        // // Ensure each group is unique by account_no_2
        // $uniqueCases = $groupedCases->map(function ($group) {
        //     return $group->first();
        // });

    // Ensure each group is unique by account_no_2
    $uniqueCases = $groupedCases->map(function ($group) {
        return $group->first();
    })->values();

$muleAccountCount = $uniqueCases->count();
// Count the number of unique cases



            // dd($muleAccountCount);

    // //pending amount calculation
    // $sum_amount=0;$hold_amount=0;$lost_amount=0;$pending_amount=0;
    // $sum_amount = Complaint::where('com_status',1)->sum('amount');
    // $hold_amount = BankCaseData::where('com_status',1)
    //     ->where('action_taken_by_bank','transaction put on hold')->sum('transaction_amount');

    // $lost_amount = BankCaseData::where('com_status',1)
    //                             ->whereIn('action_taken_by_bank',['cash withdrawal through cheque', 'withdrawal through atm', 'other','wrong transaction','withdrawal through pos'])
    //                             ->sum('transaction_amount');
    // $pending_amount = $sum_amount - $hold_amount - $lost_amount;

        $sum_amount = 0;
        $hold_amount = 0;
        $lost_amount = 0;
        $pending_amount = 0;

        $sum_amount = Complaint::where('com_status', 1)->sum('amount');
        // dd($sum_amount);
        $hold_amount = BankCaseData::where('com_status', 1)
            ->where('action_taken_by_bank', 'transaction put on hold')
            ->sum('transaction_amount');
            // dd($hold_amount);

        // Calculate hold amount percentage
        $hold_amount_percentage = 0;
        if ($sum_amount > 0) {
            $hold_amount_percentage = ($hold_amount / $sum_amount) * 100;
        }

        // Round to 2 decimal places
        $hold_amount_percentage = round($hold_amount_percentage, 2);


        return view('dashboard.dashboard',compact('totalComplaints', 'totalOtherComplaints', 'muleAccountCount','hold_amount_percentage'));
    }

}
