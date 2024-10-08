<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintOthers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MongoDB;

class ComplaintGraphController extends Controller
{
    public function index()
    {
        return view('complaints.index');
    }
/////////////////////////////////////////////////////////////////////////////////////////

    // public function chartData(Request $request)
    // {
    //     $day = $request->input('day');
    //     $month = $request->input('month', date('m'));
    //     $year = $request->input('year', date('Y'));
    //     $source = $request->input('source', 'NCRP'); // Default to 'NCRP'
    //     //dd($day, $month, $year, $source);
    //     // Define the default collection to use
    //     $collection = ($source === 'NCRP') ? Complaint::query() : ComplaintOthers::query();
    //    // dd($day);
    //     // Define date range strictly for the selected day
    //     if ($day) {
    //         $startDate = "$year-$month-$day 00:00:00";
    //         $endDate = "$year-$month-$day 23:59:59";
    //     } else {
    //         // Handle the case where no specific day is provided
    //         $startDate = "$year-$month-01 00:00:00";
    //         $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
    //     }
    //     //dd($startDate, $endDate);
    //     // Adjust endDate to include the entire day
    //     $endDate = $day ? date('Y-m-d 23:59:59', strtotime($startDate)) : $endDate;
    //         //$dday = new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000);
    //     //dd($dday);
    //     // Cases per day grouped by acknowledgement_no (for the specific day)
    //     $casesPerDayData = $collection->raw(function($collection) use ($startDate, $endDate, $source) {
    //         $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

    //         return $collection->aggregate([
    //             ['$match' => [
    //                 'entry_date' => [
    //                     '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
    //                     '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
    //                 ]
    //             ]],
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$entry_date']],
    //                 'distinct_acknowledgements' => ['$addToSet' => $groupField]
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     });
    //     // dd($casesPerDayData);
    //     // Cases per month grouped by acknowledgement_no (for the specific month only)
    //     $casesPerMonthData = $month ? $collection->raw(function($collection) use ($startDate, $endDate, $source) {
    //         $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

    //         return $collection->aggregate([
    //             ['$match' => [
    //                 'entry_date' => [
    //                     '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
    //                     '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
    //                 ]
    //             ]],
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$entry_date']],
    //                 'distinct_acknowledgements' => ['$addToSet' => $groupField]
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     }) : [];

    //     // Cases per year grouped by acknowledgement_no (only for the selected year)
    //     $casesPerYearData = $year ? $collection->raw(function($collection) use ($year, $source) {
    //         $startOfYear = "$year-01-01 00:00:00";
    //         $endOfYear = "$year-12-31 23:59:59";

    //         $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

    //         return $collection->aggregate([
    //             ['$match' => [
    //                 'entry_date' => [
    //                     '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startOfYear) * 1000),
    //                     '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($endOfYear) * 1000)
    //                 ]
    //             ]],
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y', 'date' => '$entry_date']],
    //                 'distinct_acknowledgements' => ['$addToSet' => $groupField]
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     }) : [];
    //     //dd($casesPerDayData, $casesPerMonthData, $casesPerYearData);
    //     $casesPerDay = array_column($casesPerDayData, 'cases', '_id');
    //     $casesPerMonth = array_column($casesPerMonthData, 'cases', '_id');
    //     $casesPerYear = array_column($casesPerYearData, 'cases', '_id');

    //     return response()->json([
    //         'cases_per_day' => $casesPerDay,
    //         'cases_per_month' => $casesPerMonth,
    //         'cases_per_year' => $casesPerYear
    //     ]);
    // }

////////////////////////////////////////////////////////////////////////////////////////////


public function chartData(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $source = $request->input('source', 'NCRP'); // Default to 'NCRP'

    // Define the default collection to use
    $collection = ($source === 'NCRP') ? Complaint::query() : ComplaintOthers::query();

    // Set the start and end dates to UTC explicitly
    $startDate = Carbon::parse($fromDate)->startOfDay()->timezone('+05:30');
    $endDate = Carbon::parse($toDate)->endOfDay()->timezone('+05:30');

    // Cases per day grouped by acknowledgement_no within the date range
    $casesPerDayData = $collection->raw(function($collection) use ($startDate, $endDate, $source) {
        $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

        // Adjust end date to include only until the end of the day
        $adjustedEndDate = $endDate->copy()->endOfDay();
        $adjustedStartDate = $startDate->copy()->startOfDay();
        $startofendDate = $endDate->copy()->startOfDay();
        return $collection->aggregate([
            ['$match' => [
                'entry_date' => [
                    '$gte' => new MongoDB\BSON\UTCDateTime($startofendDate->timestamp * 1000),
                    '$lte' => new MongoDB\BSON\UTCDateTime($adjustedEndDate->timestamp * 1000)
                ]
            ]],
            ['$group' => [
                '_id' => ['$dateToString' => [
                    'format' => '%Y-%m-%d',
                    'date' => '$entry_date',
                    'timezone' => '+05:30' // Add the timezone offset here
                ]],
                'distinct_acknowledgements' => ['$addToSet' => $groupField]
            ]],
            ['$project' => [
                'cases' => ['$size' => '$distinct_acknowledgements']
            ]],
            ['$sort' => ['_id' => 1]]
        ])->toArray();
    });



    $casesPerMonthData = $collection->raw(function($collection) use ($startDate, $endDate, $source) {
        $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

        // Adjust date range to cover full months
        $startDate = $startDate->copy()->startOfMonth();
        $endDate = $endDate->copy()->endOfMonth();

        // Convert to MongoDB BSON UTCDateTime
        $adjustedStartDate = new MongoDB\BSON\UTCDateTime($startDate->timestamp * 1000);
        $adjustedEndDate = new MongoDB\BSON\UTCDateTime($endDate->timestamp * 1000);

        // Step 1: Aggregate data per month
        $aggregatedData = $collection->aggregate([
            ['$match' => [
                'entry_date' => [
                    '$gte' => $adjustedStartDate,
                    '$lte' => $adjustedEndDate
                ]
            ]],
            ['$group' => [
                '_id' => ['$dateToString' => [
                    'format' => '%Y-%m',
                    'date' => '$entry_date',
                    'timezone' => '+05:30' // Adjust to the local timezone
                ]],
                'distinct_acknowledgements' => ['$addToSet' => $groupField],
                'total_cases' => ['$sum' => 1] // Count total cases per month
            ]],
            ['$project' => [
                '_id' => 1,
                'cases' => ['$size' => '$distinct_acknowledgements'],
                'total_cases' => 1
            ]],
            ['$sort' => ['_id' => 1]]
        ])->toArray();

        // Step 2: Generate the list of all months between $startDate and $endDate
        $months = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $month = $current->format('Y-m');
            $months[$month] = [
                '_id' => $month,
                'cases' => 0,  // Initialize with 0 distinct cases
                'total_cases' => 0  // Initialize with 0 total cases
            ];
            $current->addMonth();
        }

        // Step 3: Merge the results, filling in missing months
        foreach ($aggregatedData as $data) {
            $months[$data['_id']] = $data;
        }

        // Step 4: Convert the associative array back to an indexed array
        return array_values($months);
    });




    // Cases per year grouped by acknowledgement_no within the date range
    $casesPerYearData = $collection->raw(function($collection) use ($startDate, $endDate, $source) {
        $groupField = ($source === 'NCRP') ? '$acknowledgement_no' : '$case_number';

        // Adjust the date range to cover the full years for the start and end dates
        $startYear = $startDate->copy()->startOfYear();
        $endYear = $endDate->copy()->endOfYear();

        // Aggregate data per year
        return $collection->aggregate([
            ['$match' => [
                'entry_date' => [
                    '$gte' => new MongoDB\BSON\UTCDateTime($startYear->timestamp * 1000),
                    '$lte' => new MongoDB\BSON\UTCDateTime($endYear->timestamp * 1000)
                ]
            ]],
            ['$group' => [
                '_id' => ['$dateToString' => ['format' => '%Y', 'date' => '$entry_date']],
                'distinct_acknowledgements' => ['$addToSet' => $groupField],
                'total_cases' => ['$sum' => 1] // Count total cases per year
            ]],
            ['$project' => [
                '_id' => 1,
                'cases' => ['$size' => '$distinct_acknowledgements'],
                'total_cases' => 1
            ]],
            ['$sort' => ['_id' => 1]]
        ])->toArray();
    });


    $casesPerDay = array_column($casesPerDayData, 'cases', '_id');
    $casesPerMonth = array_column($casesPerMonthData, 'cases', '_id');
    $casesPerYear = array_column($casesPerYearData, 'cases', '_id');

    return response()->json([
        'cases_per_day' => $casesPerDay,
        'cases_per_month' => $casesPerMonth,
        'cases_per_year' => $casesPerYear
    ]);
}








    ////////////////////////////////////////////////////////

}
