<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\ComplaintImport;
class ComplaintController extends Controller
{
    public function importComplaints()
    {
        return view("import_complaints");
    }
    public function complaintStore(Request $request)
    {
        $request->validate([
            'complaint_file' => 'required'
        ]);


        $file = $request->file('complaint_file');

        if ($file) {
            try {
                // Import data from the file
                $source_type = $request->source_type;

            // Import data from the file
            Excel::import(new ComplaintImport($source_type), $file);


                // Provide feedback to the user
                return redirect()->back()->with('success', 'Form submitted successfully!');

            } catch (\Exception $e) {
                // Handle exceptions gracefully
                return redirect()->back()->withErrors($e->getMessage())->withInput();
                // return response()->json([
                //     'error' => 'An error occurred during import',
                //     'message' => $e->getMessage()
                // ], 500);
            }
            dd('ho');
        } else {
            // No file uploaded
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }

}
