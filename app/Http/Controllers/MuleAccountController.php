<?php

namespace App\Http\Controllers;
use App\Models\BankCasedata;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

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

        $documents = BankCasedata::whereNotNull('account_no_2')
        ->where(function($query) {
            $query->whereRaw([
                'acknowledgement_no' => [
                    '$in' => Complaint::pluck('acknowledgement_no')->toArray()
                ]
            ]);
        })
        ->get();

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

        $totalRecords = $uniqueCases->count();

        // Sort the cases
        $sortedCases = $uniqueCases->sortBy(function ($item) use ($columnName) {
            return $item->$columnName;
        }, SORT_REGULAR, $columnSortOrder === 'desc');


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
            // Check the value of $key to ensure it is sequential
            // dd($key);

            $data_arr[] = [
                'id' => $start + $key + 1, // Correct SL No by ensuring $start and $index are integers
                'account_no_2' => $record->account_no_2,
                // 'Layer' => $record->Layer,
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
