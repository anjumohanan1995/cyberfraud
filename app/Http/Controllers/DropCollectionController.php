<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Jenssegers\Mongodb\Collection;

class DropCollectionController extends Controller
{
    public function dropCollection()
    {


        try {
            // Get the MongoDB collection instance
            $collection = Complaint::raw(); // Assuming User is your model

            // Drop the collection
            $collection->drop();

            return response()->json(['success' => true, 'message' => 'Collection dropped successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
