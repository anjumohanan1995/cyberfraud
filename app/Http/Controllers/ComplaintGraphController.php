<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintOther;
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

    public function chartData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');
        $source = $request->input('source');

        if ($source == 'NCRP') {
            $model = Complaint::class;
        } else {
            $model = ComplaintOther::class;
        }

        $years = $model::selectRaw('YEAR(created_at) as year')
                       ->distinct()
                       ->pluck('year')
                       ->toArray();

        $months = $model::selectRaw('MONTH(created_at) as month')
                        ->distinct()
                        ->pluck('month')
                        ->toArray();

        $days = $model::whereYear('created_at', $year)
                      ->whereMonth('created_at', $month)
                      ->selectRaw('DAY(created_at) as day')
                      ->distinct()
                      ->pluck('day')
                      ->toArray();

        // Cases per day
        $casesPerDay = $model::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->whereDay('created_at', $day)
                            ->selectRaw('DATE(created_at) as date, count(*) as cases')
                            ->groupBy('date')
                            ->get();

        // Cases per month
        $casesPerMonth = $model::whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->count();

        // Cases per year
        $casesPerYear = $model::whereYear('created_at', $year)
                               ->count();

        return response()->json([
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'cases_per_day' => $casesPerDay,
            'cases_per_month' => $casesPerMonth,
            'cases_per_year' => $casesPerYear
        ]);
    }
}
