<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\User;

class ComplaintStatController extends Controller
{
    public function getComplaintStats()
    {
       // Fetch all complaints
       $complaints = Complaint::all();

       // Fetch all users
       $users = User::all()->keyBy('_id');

       // Initialize an empty array to store results
       $result = [];

       // Process each complaint
       foreach ($complaints as $complaint) {
           $userId = (string) $complaint->assigned_to;
//dd($userId);
           // Initialize user data if not already present
           if (!isset($result[$userId])) {
               $result[$userId] = [
                   'assigned_to' => $userId,
                   'user_name' => isset($users[$userId]) ? $users[$userId]->name : 'Unknown User',
                   'started' => 0,
                   'Ongoing' => 0,
                   'Completed' => 0,
               ];
           }
//dd($complaint->case_status);
           // Increment the count based on the case status
           switch ($complaint->case_status) {
               case 'started':
                   $result[$userId]['started']++;
                   break;
               case 'Ongoing':
                   $result[$userId]['Ongoing']++;
                   break;
               case 'Completed':
                   $result[$userId]['Completed']++;
                   break;
           }
           if($complaint->case_status == ''){
            $result[$userId]['started']++;
           }
       }
//dd($result);
       // Return the results as JSON
       return response()->json(array_values($result));
    }
}
