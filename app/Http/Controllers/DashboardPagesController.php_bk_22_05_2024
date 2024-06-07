<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardPagesController extends Controller
{


    public function dashboard(){

        $totalComplaints = Complaint::count();
    // Get the current date in ISO 8601 format
    $currentDate = now()->toDateString();
    // dd($currentDate);


    $newComplaints = Complaint::where('entry_date', '>=', new \MongoDB\BSON\UTCDateTime(strtotime($currentDate) * 1000))
                               ->where('entry_date', '<', new \MongoDB\BSON\UTCDateTime(strtotime($currentDate . ' +1 day') * 1000))
                               ->count();

    // dd($newComplaints);

        return view('dashboard.dashboard',compact('totalComplaints', 'newComplaints'));
    }


    // public function filterCaseData()
    // {
    //     // Get the current date in ISO 8601 format
    //     $currentDate = Carbon::today()->toDateString();

    //     // Calculate the start and end of today in UTCDateTime format
    //     $startOfDay = new UTCDateTime(strtotime($currentDate) * 1000);
    //     $endOfDay = new UTCDateTime(strtotime($currentDate . ' +1 day') * 1000);

    //     // Fetch the rows where entry_date matches today's date
    //     $filteredData = DB::collection('complaints')
    //                       ->where('entry_date', '>=', $startOfDay)
    //                       ->where('entry_date', '<', $endOfDay)
    //                       ->get();
    //     dd($filteredData);

    //     // Pass the filtered data to the view
    //     return view('dashboard.filter-case-data-list.index', ['data' => $filteredData]);
    // }



}
