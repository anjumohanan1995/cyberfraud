<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\SourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\ComplaintImport;
class ComplaintController extends Controller
{
    public function importComplaints()
    {
        // Fetch only active source types from the database
        $sourceTypes = SourceType::where('status', 'active')->get();

        // Pass the fetched data to the view
        return view("import_complaints", compact('sourceTypes'));
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
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    // Retrieve the validation errors
                    $errors = $e->validator->getMessageBag()->all();

                    // Redirect back with validation errors and input data
                    return redirect()->back()->withErrors($errors)->withInput();
                }

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
