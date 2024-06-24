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

    $layerOneQuery = BankCasedata::whereNull('deleted_at')
    ->where('Layer', 1)
    ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
    ->where(function($query) {
        $query->whereRaw(['$expr' => ['$ne' => [['$trim' => ['input' => ['$toLower' => '$action_taken_by_bank']]], ""]]]);
    })
    ->groupBy('account_no_2')
    ->pluck('account_no_2');

    $repeatedAccountNoQuery = BankCasedata::whereNull('deleted_at')
        ->whereIn('account_no_2', function ($subquery) {
            $subquery->select('account_no_2')
                ->from('bank_casedata')
                ->groupBy('account_no_2')
                ->havingRaw('COUNT(*) >= 3');
        })
        ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
        ->where(function($query) {
            $query->whereRaw(['$expr' => ['$ne' => [['$trim' => ['input' => ['$toLower' => '$action_taken_by_bank']]], ""]]]);
        })
        ->groupBy('account_no_2')
        ->pluck('account_no_2');

    $muleAccountCount = $layerOneQuery->merge($repeatedAccountNoQuery)->unique()->count();

        return view('dashboard.dashboard',compact('totalComplaints', 'totalOtherComplaints', 'muleAccountCount'));
    }

}
