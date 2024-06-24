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

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');

    $columnIndex = $columnIndex_arr[0]['column']; // Column index
    $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    $searchValue = $search_arr['value']; // Search value

// Initialize base query with necessary conditions
$query = BankCasedata::whereNull('deleted_at');

// Apply search condition if there is a search value
if (!empty($searchValue)) {
    $query->where(function ($q) use ($searchValue) {
        $q->where('account_no_2', 'like', "%{$searchValue}%");
    });
}

// Exclude specific values from action_taken_by_bank
$query->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where(function($query) {
        $query->whereRaw(['$expr' => ['$ne' => [['$trim' => ['input' => ['$toLower' => '$action_taken_by_bank']]], ""]]]);
    });

// Filtered query for specific conditions
$filteredQuery = $query->where(function ($q) {
    $q->where('Layer', 1)
        ->orWhere(function ($q) {
            $q->whereIn('account_no_2', function ($subquery) {
                $subquery->select('account_no_2')
                    ->from('bank_casedata')
                    ->groupBy('account_no_2')
                    ->havingRaw('COUNT(*) >= 3');
            });
        });
});

// Group the filtered query by account_no_2
$groupedQuery = $filteredQuery->groupBy('account_no_2')
    ->select('account_no_2', DB::raw('MAX(Layer) as Layer'));

    $totalFilteredRecords = $groupedQuery->get()->count();

// Apply ordering and pagination
$groupedQuery->orderBy($columnName, $columnSortOrder)
    ->skip($start)
    ->take($length);

// Fetch the filtered and grouped records
$filteredAccounts = $groupedQuery->get();

// Modify account_no_2 field to remove [ Reported x times ]
$filteredAccounts->transform(function ($item) {
    $item->account_no_2 = preg_replace('/\[ Reported \d+ times \]/', '', $item->account_no_2);
    return $item;
});

    // Prepare data for DataTables
    $data_arr = [];
    $i = $start; // Initialize $i with $start value
    foreach ($filteredAccounts as $record) {
        $data_arr[] = [
            'id' => ++$i,
            'account_no_2' => $record->account_no_2,
            'Layer' => $record->Layer,
        ];
    }

    // Response data for DataTables
    $response = [
        'draw' => intval($draw),
        'recordsTotal' => $totalFilteredRecords,
        'recordsFiltered' => $totalFilteredRecords,
        'data' => $data_arr,
    ];

    return response()->json($response);
}



}
