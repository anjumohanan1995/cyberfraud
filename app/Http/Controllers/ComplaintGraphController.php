<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComplaintGraphController extends Controller
{
    public function chartData(Request $request)
    {
        $specifiedDate = $request->input('specified_date');
        $date = Carbon::parse($specifiedDate);
$Data = Complaint::whereDate('created_at', $date)->count();
dd($Data);
        // Fetch data for cases per day
        $casesPerDayQuery = Complaint::whereDate('created_at', $date)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date');

        $casesPerDay = $casesPerDayQuery->pluck('count', 'date')->toArray();

        // Debug output
        logger()->debug('Cases per Day Query:', ['query' => $casesPerDayQuery->toSql()]);
        logger()->debug('Cases per Day Bindings:', ['bindings' => $casesPerDayQuery->getBindings()]);
        logger()->debug('Cases per Day Result:', ['result' => $casesPerDay]);

        // Fetch data for cases per month
        $casesPerMonthQuery = Complaint::whereYear('created_at', $date->year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month');

        $casesPerMonth = $casesPerMonthQuery->pluck('count', 'month')->toArray();

        // Format month names
        $formattedCasesPerMonth = [];
        foreach ($casesPerMonth as $month => $count) {
            $formattedCasesPerMonth[Carbon::createFromFormat('!m', $month)->format('F')] = $count;
        }

        // Debug output
        logger()->debug('Cases per Month Query:', ['query' => $casesPerMonthQuery->toSql()]);
        logger()->debug('Cases per Month Bindings:', ['bindings' => $casesPerMonthQuery->getBindings()]);
        logger()->debug('Cases per Month Result:', ['result' => $formattedCasesPerMonth]);

        // Fetch data for cases per year
        $casesPerYearQuery = Complaint::select(DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as count'))
            ->groupBy('year')
            ->orderBy('year');

        $casesPerYear = $casesPerYearQuery->pluck('count', 'year')->toArray();

        // Debug output
        logger()->debug('Cases per Year Query:', ['query' => $casesPerYearQuery->toSql()]);
        logger()->debug('Cases per Year Bindings:', ['bindings' => $casesPerYearQuery->getBindings()]);
        logger()->debug('Cases per Year Result:', ['result' => $casesPerYear]);

        return response()->json([
            'casesPerDay' => $casesPerDay,
            'casesPerMonth' => $formattedCasesPerMonth,
            'casesPerYear' => $casesPerYear,
        ]);
    }
}
