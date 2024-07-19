<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;
use MongoDB\BSON\ObjectId;

class ComplaintStatController extends Controller
{
    public function getComplaintStats()
    {
        $client = new Mongo();
        $complaintsCollection = $client->cyber->complaints;
        $usersCollection = $client->cyber->users;

        // Fetch all users
        $users = $usersCollection->find();
        $data = [];

        foreach ($users as $user) {
            $userId = $user['_id'];
            $userIdString = (string) $userId;

            // Validate ObjectId
            try {
                $objectId = new ObjectId($userIdString);
            } catch (\Exception $e) {
                \Log::error('Invalid ObjectId: ' . $userIdString);
                continue; // Skip invalid ObjectId
            }
dd($objectId);
            // Debug: Log the user ID and the type
            \Log::info('Processing User ID: ' . $userIdString);

            // Count started cases
            $startedCount = $complaintsCollection->countDocuments([
                'assigned_to' => $objectId,
                '$or' => [
                    ['case_status' => 'Started'],
                    ['case_status' => ['$exists' => false]]
                ]
            ]);

            // Count ongoing cases
            $ongoingCount = $complaintsCollection->countDocuments([
                'assigned_to' => $objectId,
                'case_status' => 'Ongoing'
            ]);

            // Count completed cases
            $completedCount = $complaintsCollection->countDocuments([
                'assigned_to' => $objectId,
                'case_status' => 'Completed'
            ]);

            // Log counts
            \Log::info('Started Count: ' . $startedCount);
            \Log::info('Ongoing Count: ' . $ongoingCount);
            \Log::info('Completed Count: ' . $completedCount);

            // Collect data
            $data[] = [
                'user' => $user['name'], // Adjust if necessary
                'started' => $startedCount,
                'ongoing' => $ongoingCount,
                'completed' => $completedCount,
            ];
        }

        // Log final data
        \Log::info('Complaint Stats Data: ' . json_encode($data));

        return response()->json($data);
    }
}

