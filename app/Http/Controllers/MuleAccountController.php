<?php

namespace App\Http\Controllers;
use App\Models\BankCasedata;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MuleAccountController extends Controller
{
    public function Muleaccount(){
        return view('Muleaccount.muleaccount');
    }

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

        $documents = BankCasedata::where('account_no_2', '!=', null)->get();

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

        $frequentAccountNumbers = array_filter($accountNumbers, function($count) {
            return $count > 2;
        });

        $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);

        $layer1Cases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->where('Layer', 1)
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->get();

            $layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();

        $accountNumberPatterns = array_map(function($number) {
            return "^$number\\b";
        }, $frequentAccountNumbersKeys);

        $otherLayerCases = BankCasedata::whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
        ->whereNotIn('acknowledgement_no', $layer1AcknowledgementNos)
            ->where('Layer', '!=', 1)
            ->where(function($query) use ($accountNumberPatterns) {
                foreach ($accountNumberPatterns as $pattern) {
                    $query->orWhere('account_no_2', 'regexp', $pattern);
                }
            })
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->get();

        $filterDuplicates = function($cases) {
            return $cases->unique(function($case) {
                return $case->acknowledgement_no . '-' . $case->account_no_2;
            });
        };

        $layer1Cases = $filterDuplicates($layer1Cases);
        $otherLayerCases = $filterDuplicates($otherLayerCases);

        $groupedOtherLayerCases = $otherLayerCases->groupBy(function($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });

        $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
            return $group->pluck('acknowledgement_no')->unique()->count() > 1;
        });

        $allCases = $layer1Cases->merge($validOtherLayerCases->flatten(1));

        $groupedCases = $allCases->groupBy(function($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });

        $groupedCases->transform(function ($group) {
            // Ensure each group has unique acknowledgement_no values
            return $group->unique('acknowledgement_no');
        });

        if (!empty($searchValue)) {
            $allCases = $allCases->filter(function ($item) use ($searchValue) {
                return stripos($item->account_no_2, $searchValue) !== false;
            });
        }
        // dd($allCases);

        $totalRecords = $allCases->count();

        $sortedCases = $allCases->sortBy(function ($item) use ($columnName) {
            return $item->$columnName;
        }, SORT_REGULAR, $columnSortOrder === 'asc');

        $filteredCases = $sortedCases->slice($start, $length);

        $filteredCases = $filteredCases->map(function ($item) {
            $item->account_no_2 = preg_replace('/\[ Reported \d+ times \]/', '', $item->account_no_2);
            return $item;
        });

        $data_arr = [];
        foreach ($filteredCases as $index => $record) {
            $data_arr[] = [
                'id' => $start + $index + 1,
                'account_no_2' => $record->account_no_2,
                'Layer' => $record->Layer,
            ];
        }

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data_arr,
        ];

        return response()->json($response);
    }



}
