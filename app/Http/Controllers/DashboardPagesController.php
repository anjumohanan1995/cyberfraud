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

    // Get acknowledgment numbers from Complaint model
    $acknowledgementNos = Complaint::pluck('acknowledgement_no')->toArray();

    // Retrieve documents with non-null account_no_2
    $documents = BankCasedata::whereNotNull('account_no_2')
        ->where('account_no_2', '!=', '')
        ->get();

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

    // Filter account_no_2 that repeat more than twice with different acknowledgment numbers
    $frequentAccountNumbers = array_filter($accountCounts, function($acknos) {
        return count(array_unique($acknos)) > 2;
    });
    $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);

    // Get Layer 1 cases
    $layer1Cases = BankCasedata::where('Layer', 1)
        ->whereNotNull('account_no_2')
        ->where('account_no_2', '!=', '')
        ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
        ->whereIn('acknowledgement_no', $acknowledgementNos)
        ->get();

    // Get patterns for frequent account numbers
    $accountNumberPatterns = array_map(function($number) {
        return new Regex("^$number\\b", ''); // Match the start of the string
    }, $frequentAccountNumbersKeys);

    // Get other layer cases
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

    // Get withdrawal cases
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

    // Filter valid other layer cases
    $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
        return $group->pluck('acknowledgement_no')->unique()->count() >= 1;
    });

    // Merge Layer 1 and valid other layer cases
    $merge = $layer1Cases->merge($withdrawalCases);
    $allCases = $merge->merge($validOtherLayerCases->flatten(1));

    // Group by account_no_2 and remove duplicates
    $groupedCases = $allCases->groupBy(function ($case) {
        return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
    });

// Merge layer 1 and valid other layer cases
$allCases = $layer1Cases->merge($validOtherLayerCases->flatten(1));
// dd($allCases);

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

    // Filter cases based on Complaint model and Layer
    $filteredCases = $uniqueCases->filter(function ($item) use ($acknowledgementNos) {
        // Extract numeric part of account_no_2 for comparison
        $cleanedAccountNo2 = preg_replace('/\s*\[.*\]$/', '', $item->account_no_2);
        $cleanedAccountNo2 = trim($cleanedAccountNo2);

        if ($item->Layer === 1) {
            // Check if the cleaned account_no_2 and acknowledgement_no from the filteredCases exist in the Complaint model
            $complaintExists = Complaint::where('acknowledgement_no', $item->acknowledgement_no)
                ->where('account_id', $cleanedAccountNo2)
                ->exists();

            // If not found in Complaint, keep this record
            return !$complaintExists;
        } else {
            // For other layers, include all records
            return true;
        }
    })->values();

    // Include count of filtered cases
    $muleAccountCount = $filteredCases->count();

    // Apply search filter if search value is present
    if (!empty($searchValue)) {
        $filteredCases = $filteredCases->filter(function ($item) use ($searchValue) {
            return stripos($item->account_no_2, $searchValue) !== false;
        });
    }

    $totalRecords = $filteredCases->count();
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
