<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComplaintStatController extends Controller
{
    public function getComplaintStats(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');

        // Fetch complaints with optional filtering
        $query = Complaint::query();

        if ($year) {
            $query->whereYear('created_at', $year);
        }
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($day) {
            $query->whereDay('created_at', $day);
        }

        $complaints = $query->get();

        // Fetch all users
        $users = User::all()->keyBy('_id');

        // Initialize an empty array to store results
        $result = [];

        // Process each complaint
        foreach ($complaints as $complaint) {
            $userId = (string) $complaint->assigned_to;

            // Initialize user data if not already present
            if (!isset($result[$userId])) {
                $result[$userId] = [
                    'user_id' => $userId,
                    'user_name' => isset($users[$userId]) ? $users[$userId]->name : 'Unknown User',
                    'started' => 0,
                    'ongoing' => 0,
                    'completed' => 0,
                ];
            }

            // Increment the count based on the case status
            switch (strtolower($complaint->case_status)) {
                case 'started':
                    $result[$userId]['started']++;
                    break;
                case 'ongoing':
                    $result[$userId]['ongoing']++;
                    break;
                case 'completed':
                    $result[$userId]['completed']++;
                    break;
            }
        }

        // Return the results as JSON
        return response()->json(array_values($result));
    }

    public function getAvailableFilters()
    {
        $years = Complaint::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year');

        $months = Complaint::select(DB::raw('MONTH(created_at) as month'))
            ->distinct()
            ->pluck('month');

        $days = Complaint::select(DB::raw('DAY(created_at) as day'))
            ->distinct()
            ->pluck('day');

        return response()->json([
            'years' => $years,
            'months' => $months,
            'days' => $days
        ]);
    }
}
