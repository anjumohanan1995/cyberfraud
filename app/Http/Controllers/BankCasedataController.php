<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankImport;
use App\Imports\BankImports;
use Exception;
use App\Jobs\ImportBankAction;

class BankCasedataController extends Controller
{



    public function index()
    {
        return view('dashboard.bank-case-data.index');
    }


    public function store(Request $request)
    {
       
        // Validate the uploaded file.
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt,ods'
        ],[
    'file.mimes' => 'The uploaded file format is not supported. Please upload a file with one of the following formats: xlsx, csv, txt, ods.'
]);

        // Get the uploaded file.
        $file = $request->file('file');
        //dd($file->getMimeType()); // Check the MIME type

        // Check if a file was uploaded.
        if ($file) {

            try {

                // dd($file);

                // Import data from the file.
                Excel::import(new BankImports, $file);
              // ImportBankAction::dispatch($file);
            
           
                // Provide feedback to the user.
                return redirect()->back()->with('success', 'File imported successfully!');
            }  catch (\Exception $e){
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
        } 
         else {
            // No file uploaded.
            return redirect()->back()->with('error', 'No file uploaded. Please select a file to import.');
        }
    }
}
