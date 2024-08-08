<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintOthers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

class ComplaintStatController extends Controller
{
    public function getAvailableFilters()
    {
        // Fetch distinct years
        $years = Complaint::raw(function($collection) {
            return $collection->aggregate([
                ['$project' => ['year' => ['$year' => '$created_at']]],
                ['$group' => ['_id' => '$year']],
                ['$sort' => ['_id' => -1]]
            ]);
        })->pluck('_id')->toArray();

        // Fetch distinct months
        $months = Complaint::raw(function($collection) {
            return $collection->aggregate([
                ['$project' => ['month' => ['$month' => '$created_at']]],
                ['$group' => ['_id' => '$month']],
                ['$sort' => ['_id' => 1]]
            ]);
        })->pluck('_id')->toArray();

        // Fetch distinct days
        $days = Complaint::raw(function($collection) {
            return $collection->aggregate([
                ['$project' => ['day' => ['$dayOfMonth' => '$created_at']]],
                ['$group' => ['_id' => '$day']],
                ['$sort' => ['_id' => 1]]
            ]);
        })->pluck('_id')->toArray();

        return response()->json([
            'years' => $years,
            'months' => $months,
            'days' => $days
        ]);
    }


   // ComplaintStatController.php
// ComplaintStatController.php

public function getComplaintStats(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $source = $request->query('source');

    // Initialize query array
    $query = $source == 'NCRP' ? Complaint::query() : ComplaintOthers::query();

    if ($startDate && $endDate) {
        try {
            // Apply date range filter
            $query->whereBetween('status_changed', [$startDate, $endDate]);
        } catch (\Exception $e) {
            \Log::error('Date parsing error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid date format'], 400);
        }
    }

    // Fetch complaints with applied filters
    $complaints = $query->get();

    // Debug the complaints data
    \Log::info('Complaints:', $complaints->toArray());

    // Fetch all users
    $users = User::all()->keyBy('_id');

    // Initialize an empty array to store results
    $result = [];

    // Group complaints by acknowledgement_number or case_number based on source
    $groupByField = $source == 'NCRP' ? 'acknowledgement_no' : 'case_number';

    // Ensure the field exists in the complaints data
    if ($complaints->isEmpty() || !isset($complaints->first()[$groupByField])) {
        \Log::error("Field '{$groupByField}' does not exist in complaints data.");
        return response()->json([], 200);
    }

    $groupedComplaints = $complaints->groupBy($groupByField);

    // Debug grouped complaints
    \Log::info('Grouped Complaints:', $groupedComplaints->map->toArray()->toArray());

    // Process each group of complaints
    foreach ($groupedComplaints as $key => $complaintsGroup) {
        // Consider each group as a single case
        $complaint = $complaintsGroup->first();
        $userId = (string) $complaint->assigned_to;

        // Initialize user data if not already present
        if (!isset($result[$userId]) && $userId) {
            $result[$userId] = [
                'assigned_to' => $userId,
                'user_name' => isset($users[$userId]) ? $users[$userId]->name : 'Unknown User',
                'Started' => 0,
                'Ongoing' => 0,
                'Completed' => 0,
            ];
        }

        // Increment the count based on the case status
        switch ($complaint->case_status) {
            case 'Started':
                $result[$userId]['Started']++;
                break;
            case 'Ongoing':
                $result[$userId]['Ongoing']++;
                break;
            case 'Completed':
                $result[$userId]['Completed']++;
                break;
        }
    }

    // Log the processed results
    \Log::info('Processed Results:', $result);

    // Return the results as JSON
    return response()->json(array_values($result));
}










}
