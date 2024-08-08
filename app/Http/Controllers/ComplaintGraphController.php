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

    public function chartDataOLD(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $source = $request->input('source', 'NCRP'); // Default to 'NCRP'

        // Define the default collection to use
        $collection = ($source === 'NCRP') ? Complaint::query() : ComplaintOthers::query();

        // Cases per day
        $casesPerDayData = $collection->raw(function($collection) use ($year, $month, $day) {
            $startDate = $day ? "$year-$month-$day" : "$year-$month-01";
            $endDate = $day ? "$year-$month-$day +1 day" : "$year-$month-01 +1 month";

            return $collection->aggregate([
                ['$match' => [
                    'created_at' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                        '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                    ]
                ]],
                ['$group' => [
                    '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']],
                    'cases' => ['$sum' => 1]
                ]],
                ['$sort' => ['_id' => 1]]
            ])->toArray();
        });

        $casesPerDay = array_column($casesPerDayData, 'cases', '_id');

        // Cases per month
        $casesPerMonthData = $collection->raw(function($collection) use ($year) {
            return $collection->aggregate([
                ['$match' => [
                    'created_at' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime("$year-01-01") * 1000),
                        '$lt' => new MongoDB\BSON\UTCDateTime(strtotime("$year-12-31 +1 day") * 1000)
                    ]
                ]],
                ['$group' => [
                    '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$created_at']],
                    'cases' => ['$sum' => 1]
                ]],
                ['$sort' => ['_id' => 1]]
            ])->toArray();
        });

        $casesPerMonth = array_column($casesPerMonthData, 'cases', '_id');

        // Cases per year
        $casesPerYearData = $collection->raw(function($collection) {
            return $collection->aggregate([
                ['$group' => [
                    '_id' => ['$year' => '$created_at'],
                    'cases' => ['$sum' => 1]
                ]],
                ['$sort' => ['_id' => 1]]
            ])->toArray();
        });

        $casesPerYear = array_column($casesPerYearData, 'cases', '_id');

        // When no specific request is made, return current and previous month and year data
        if (!$request->filled('year') && !$request->filled('month') && !$request->filled('day')) {
            $previousMonth = date('Y-m', strtotime('first day of last month'));
            $currentMonth = date('Y-m');
            $previousYear = date('Y', strtotime('-1 year'));
            $currentYear = date('Y');

            $casesPerMonth = array_filter($casesPerMonth, function($key) use ($previousMonth, $currentMonth) {
                return $key == $previousMonth || $key == $currentMonth;
            }, ARRAY_FILTER_USE_KEY);

            $casesPerYear = array_filter($casesPerYear, function($key) use ($previousYear, $currentYear) {
                return $key == $previousYear || $key == $currentYear;
            }, ARRAY_FILTER_USE_KEY);
        }

        return response()->json([
            'cases_per_day' => $casesPerDay,
            'cases_per_month' => $casesPerMonth,
            'cases_per_year' => $casesPerYear
        ]);
    }

    ////////////////////////////////////////////////////////
    // public function chartData(Request $request)
    // {
    //     $day = $request->input('day');
    //     $month = $request->input('month', date('m'));
    //     $year = $request->input('year', date('Y'));
    //     $source = $request->input('source', 'NCRP'); // Default to 'NCRP'

    //     // Define the default collection to use
    //     $collection = ($source === 'NCRP') ? Complaint::query() : ComplaintOthers::query();

    //     // Cases per day grouped by acknowledgement_no
    //     $casesPerDayData = $collection->raw(function($collection) use ($year, $month, $day) {
    //         $startDate = $day ? "$year-$month-$day" : "$year-$month-01";
    //         $endDate = $day ? "$year-$month-$day +1 day" : "$year-$month-01 +1 month";

    //         return $collection->aggregate([
    //             ['$match' => [
    //                 'created_at' => [
    //                     '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
    //                     '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
    //                 ]
    //             ]],
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']],
    //                 'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     });

    //     $casesPerDay = array_column($casesPerDayData, 'cases', '_id');

    //     // Cases per month grouped by acknowledgement_no
    //     $casesPerMonthData = $collection->raw(function($collection) use ($year) {
    //         return $collection->aggregate([
    //             ['$match' => [
    //                 'created_at' => [
    //                     '$gte' => new MongoDB\BSON\UTCDateTime(strtotime("$year-01-01") * 1000),
    //                     '$lt' => new MongoDB\BSON\UTCDateTime(strtotime("$year-12-31 +1 day") * 1000)
    //                 ]
    //             ]],
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$created_at']],
    //                 'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     });

    //     $casesPerMonth = array_column($casesPerMonthData, 'cases', '_id');

    //     // Cases per year grouped by acknowledgement_no
    //     $casesPerYearData = $collection->raw(function($collection) {
    //         return $collection->aggregate([
    //             ['$group' => [
    //                 '_id' => ['$dateToString' => ['format' => '%Y', 'date' => '$created_at']],
    //                 'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
    //             ]],
    //             ['$project' => [
    //                 'cases' => ['$size' => '$distinct_acknowledgements']
    //             ]],
    //             ['$sort' => ['_id' => 1]]
    //         ])->toArray();
    //     });

    //     $casesPerYear = array_column($casesPerYearData, 'cases', '_id');

    //     return response()->json([
    //         'cases_per_day' => $casesPerDay,
    //         'cases_per_month' => $casesPerMonth,
    //         'cases_per_year' => $casesPerYear
    //     ]);
    // }


    public function chartData(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $source = $request->input('source', 'NCRP'); // Default to 'NCRP'

        // Define the default collection to use
        $collection = ($source === 'NCRP') ? Complaint::query() : ComplaintOthers::query();

        // Define date ranges
        $startDate = "$year-$month-01";
        $endDate = $day ? "$year-$month-$day +1 day" : "$year-$month-01 +1 month";
        $startOfYear = "$year-01-01";
        $endOfYear = "$year-12-31 +1 day";

        // Cases per day grouped by acknowledgement_no
        if($source == 'NCRP') {
            $casesPerDayData = $collection->raw(function($collection) use ($year, $month, $day, $startDate, $endDate) {
                $startDate = $day ? "$year-$month-$day" : "$year-$month-01";
                $endDate = $day ? "$year-$month-$day +1 day" : "$year-$month-01 +1 month";

                return $collection->aggregate([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                            '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                        ]
                    ]],
                    ['$group' => [
                        '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']],
                        'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
                    ]],
                    ['$project' => [
                        'cases' => ['$size' => '$distinct_acknowledgements']
                    ]],
                    ['$sort' => ['_id' => 1]]
                ])->toArray();
            });

        }else{
            $casesPerDayData = $collection->raw(function($collection) use ($year, $month, $day, $startDate, $endDate) {
                $startDate = $day ? "$year-$month-$day" : "$year-$month-01";
                $endDate = $day ? "$year-$month-$day +1 day" : "$year-$month-01 +1 month";

                return $collection->aggregate([
                    ['$match' => [                                                                                                                      
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                            '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                        ]
                    ]],
                    ['$group' => [
                        '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']],
                        'distinct_acknowledgements' => ['$addToSet' => '$case_number']
                    ]],
                    ['$project' => [
                        'cases' => ['$size' => '$distinct_acknowledgements']
                    ]],
                    ['$sort' => ['_id' => 1]]
                ])->toArray();
            });

        }

        $casesPerDay = array_column($casesPerDayData, 'cases', '_id');

        // Cases per month grouped by acknowledgement_no
        if ($month) {
            if($source == 'NCRP'){
                $casesPerMonthData = $collection->raw(function($collection) use ($year, $month, $startOfYear, $endOfYear) {
                $startDate = "$year-$month-01";
                $endDate = "$year-$month-01 +1 month";

                return $collection->aggregate([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                            '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                        ]
                    ]],
                    ['$group' => [
                        '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$created_at']],
                        'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
                    ]],
                    ['$project' => [
                        'cases' => ['$size' => '$distinct_acknowledgements']
                    ]],
                    ['$sort' => ['_id' => 1]]
                ])->toArray();
            });
            }else{
                $casesPerMonthData = $collection->raw(function($collection) use ($year, $month, $startOfYear, $endOfYear) {
                    $startDate = "$year-$month-01";
                    $endDate = "$year-$month-01 +1 month";

                    return $collection->aggregate([
                        ['$match' => [
                            'created_at' => [
                                '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000),
                                '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000)
                            ]
                        ]],
                        ['$group' => [
                            '_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$created_at']],
                            'distinct_acknowledgements' => ['$addToSet' => '$case_number']
                        ]],
                        ['$project' => [
                            'cases' => ['$size' => '$distinct_acknowledgements']
                        ]],
                        ['$sort' => ['_id' => 1]]
                    ])->toArray();
                });
            }


            $casesPerMonth = array_column($casesPerMonthData, 'cases', '_id');
        } else {
            $casesPerMonth = [];
        }

        // Cases per year grouped by acknowledgement_no
        if ($month) {
            if($source =='Others'){
                $casesPerYearData = $collection->raw(function($collection) use ($year, $startOfYear, $endOfYear) {
                    return $collection->aggregate([
                        ['$match' => [
                            'created_at' => [
                                '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startOfYear) * 1000),
                                '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endOfYear) * 1000)
                            ]
                        ]],
                        ['$group' => [
                            '_id' => ['$dateToString' => ['format' => '%Y', 'date' => '$created_at']],
                            'distinct_acknowledgements' => ['$addToSet' => '$case_number']
                        ]],
                        ['$project' => [
                            'cases' => ['$size' => '$distinct_acknowledgements']
                        ]],
                        ['$sort' => ['_id' => 1]]
                    ])->toArray();
                });
            }else{
                $casesPerYearData = $collection->raw(function($collection) use ($year, $startOfYear, $endOfYear) {
                    return $collection->aggregate([
                        ['$match' => [
                            'created_at' => [
                                '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startOfYear) * 1000),
                                '$lt' => new MongoDB\BSON\UTCDateTime(strtotime($endOfYear) * 1000)
                            ]
                        ]],
                        ['$group' => [
                            '_id' => ['$dateToString' => ['format' => '%Y', 'date' => '$created_at']],
                            'distinct_acknowledgements' => ['$addToSet' => '$acknowledgement_no']
                        ]],
                        ['$project' => [
                            'cases' => ['$size' => '$distinct_acknowledgements']
                        ]],
                        ['$sort' => ['_id' => 1]]
                    ])->toArray();
                });
            }

           // dd($casesPerYearData);


            $casesPerYear = array_column($casesPerYearData, 'cases', '_id');
        } else {
            $casesPerYear = [];
        }

        return response()->json([
            'cases_per_day' => $casesPerDay,
            'cases_per_month' => $casesPerMonth,
            'cases_per_year' => $casesPerYear
        ]);
    }



    ////////////////////////////////////////////////////////

}
