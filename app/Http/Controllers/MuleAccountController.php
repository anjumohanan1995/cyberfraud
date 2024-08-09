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
        $start = $request->get('start'); // Offset (starting row index) for pagination
        $length = $request->get('length'); // Number of rows per page

        $order = $request->get('order');
        $columns = $request->get('columns');
        $search = $request->get('search');

        $columnIndex = $order[0]['column']; // Column index
        $columnName = $columns[$columnIndex]['data']; // Column name
        $columnSortOrder = $order[0]['dir']; // asc or desc
        $searchValue = $search['value']; // Search value

        // Aggregate frequent account numbers
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

        // Apply search condition if there is a search value
        if (!empty($searchValue)) {
            $cases = $cases->filter(function ($item) use ($searchValue) {
                return stripos($item->account_no_2, $searchValue) !== false;
            });
        }

        // Apply ordering and pagination
        $totalRecords = $cases->count();
        $filteredCases = $cases->sortBy([$columnName => $columnSortOrder])
            ->slice($start, $length);

        // Modify account_no_2 field to remove [ Reported x times ]
        $filteredCases = $filteredCases->map(function ($item) {
            $item->account_no_2 = preg_replace('/\[ Reported \d+ times \]/', '', $item->account_no_2);
            return $item;
        });

        // Prepare data for DataTables
        $data_arr = [];
        $i = $start + 1; // Initialize $i with $start value + 1
        foreach ($filteredCases as $record) {
            $data_arr[] = [
                'id' => $i++,
                'account_no_2' => $record->account_no_2,
                'Layer' => $record->Layer,
            ];
        }

        // Response data for DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data_arr,
        ];

        return response()->json($response);
    }



}
