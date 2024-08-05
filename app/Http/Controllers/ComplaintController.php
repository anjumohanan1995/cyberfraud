<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintOthers;
use App\Models\SourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Imports\ComplaintImport;
use App\Imports\ComplaintImportOthers;
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

        $file = $request->file('complaint_file');
        $source_type = $request->input('source_type');
        if($source_type){
            if($source_type !== 'NCRP'){

                $request->validate([
                    'case_number' => 'required',
                    'letter' =>      'required|mimes:pdf',
                    'complaint_file' => 'required',

                ]);
                $case_number_check = ComplaintOthers::where('case_number',$request->case_number)->get()->count();

                if($case_number_check > 0){
                    return redirect()->back()->with('error', 'Case number exists!!');
                }
                if ($request->hasFile('letter')){
                   $file = $request->file('letter');
                   $extension = $file->getClientOriginalExtension();
                   $fileName = 'cyb-'. date('Y-m-d_H-i-s').'.'.$extension;
                   $path = $file->storeAs('uploads/complaints/others/', $fileName);

                }

            Excel::import(new ComplaintImportOthers($source_type,$request->case_number,$fileName), $request->complaint_file);
            return redirect()->back()->with('success', 'Form submitted successfully!');
            }
            else{

                $request->validate([
                    'complaint_file' => 'required'
                ]);

                if ($file){
                    try {
                        // Import data from the file
                        $source_type = $request->source_type;

                    // Import data from the file
                    Excel::import(new ComplaintImport($source_type), $file);
                    

                        // Provide feedback to the user
                        return redirect()->back()->with('success', 'Form submitted successfully!');

                    } 
                    catch (\Exception $e){
                        if ($e instanceof \Illuminate\Validation\ValidationException) {
                            // Retrieve the validation errors
                            $errors = $e->validator->getMessageBag()->all();
                    
                            // Redirect back with validation errors and input data
                            return redirect()->back()->withErrors($errors)->withInput();
                        } else {
                            // Handle other exceptions
                            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                        }

                        // return response()->json([
                        //     'error' => 'An error occurred during import',
                        //     'message' => $e->getMessage()
                        // ], 500);
                    }
                    //dd('ho');
                } else {
                    // No file uploaded
                    return response()->json(['error' => 'No file uploaded'], 400);
                }
            }
        }




}

}
