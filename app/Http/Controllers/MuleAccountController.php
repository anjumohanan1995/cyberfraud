<?php

namespace App\Http\Controllers;
use App\Models\BankCasedata;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Regex;

use Illuminate\Http\Request;

class MuleAccountController extends Controller
{
    public function Muleaccount(){
        return view('Muleaccount.muleaccount');
    }

//     public function muleaccountList(Request $request)
// {
//     $draw = $request->get('draw');
//     $start = $request->get('start');
//     $length = $request->get('length');

//     $order = $request->get('order');
//     $columns = $request->get('columns');
//     $search = $request->get('search');

//     $columnIndex = $order[0]['column'];
//     $columnName = $columns[$columnIndex]['data'];
//     $columnSortOrder = $order[0]['dir'];
//     $searchValue = $search['value'];

//     // Fetch and filter the account numbers
//     $documents = BankCasedata::where('account_no_2', '!=', null)->get();

//     $accountNumbers = [];
//     foreach ($documents as $doc) {
//         if (isset($doc['account_no_2'])) {
//             preg_match('/(\d+)/', $doc['account_no_2'], $matches);
//             if (!empty($matches[1])) {
//                 $number = $matches[1];
//                 $accountNumbers[$number] = $number;
//             }
//         }
//     }

//     // Get unique account numbers
//     $uniqueAccountNumbers = array_values($accountNumbers);

//     // Apply filtering if search is provided
//     if (!empty($searchValue)) {
//         $uniqueAccountNumbers = array_filter($uniqueAccountNumbers, function ($item) use ($searchValue) {
//             return stripos($item, $searchValue) !== false;
//         });
//     }

//     $totalRecords = count($uniqueAccountNumbers);

//     // Apply sorting
//     if ($columnSortOrder === 'asc') {
//         sort($uniqueAccountNumbers);
//     } else {
//         rsort($uniqueAccountNumbers);
//     }

//     // Paginate the results
//     $paginatedAccountNumbers = array_slice($uniqueAccountNumbers, $start, $length);

//     // Prepare data array
//     $data_arr = [];
//     foreach ($paginatedAccountNumbers as $index => $accountNo) {
//         $data_arr[] = [
//             'id' => $start + $index + 1,
//             'account_no_2' => $accountNo,
//         ];
//     }

//     $response = [
//         'draw' => intval($draw),
//         'recordsTotal' => $totalRecords,
//         'recordsFiltered' => $totalRecords,
//         'data' => $data_arr,
//     ];

//     return response()->json($response);
// }


public function muleaccountList(Request $request)
{
    $draw = $request->get('draw');
    $start = $request->get('start');
    $length = $request->get('length');

    $order = $request->get('order');
    $columns = $request->get('columns');
    $search = $request->get('search');

    $columnIndex = $order[0]['column'];
    $columnName = $columns[$columnIndex]['data'];
    $columnSortOrder = $order[0]['dir'];
    $searchValue = $search['value'];

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

    // dd($otherLayerCases);
    $withdrawalCases = BankCasedata::where('Layer','!=', 1)
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

    // Apply search filter if search value is present
    if (!empty($searchValue)) {
        $uniqueCases = $uniqueCases->filter(function ($item) use ($searchValue) {
            return stripos($item->account_no_2, $searchValue) !== false;
        });
    }

    $totalRecords = $uniqueCases->count();

    // Sort the cases
    $sortedCases = $uniqueCases->sortBy(function ($item) use ($columnName) {
        return $item->$columnName;
    }, SORT_REGULAR, $columnSortOrder === 'desc');

    // Slice for pagination
    // $slicedCases = $sortedCases->slice($start, $length)->values();

    // Slice for pagination
    $slicedCases = $sortedCases->slice($start, $length)->values();

    // Clean up account_no_2 field
    $filteredCases = $slicedCases->map(function ($item) {
        $item->account_no_2 = preg_replace('/\[ Reported \d+ times \]/', '', $item->account_no_2);
        return $item;
    });

    // Prepare data array for response
    $data_arr = [];
    foreach ($filteredCases as $key => $record) {
        $data_arr[] = [
            'id' => $start + $key + 1,
            'account_no_2' => $record->account_no_2,
            'bank' => $record->bank,
            'status' => "pending",
        ];
    }

    // Prepare response
    $response = [
        'draw' => intval($draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data_arr,
    ];

    return response()->json($response);
}


}
