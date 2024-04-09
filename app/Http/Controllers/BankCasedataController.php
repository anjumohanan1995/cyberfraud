<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class BankCasedataController extends Controller
{



    public function index()
    {



        return view('dashboard.bank-case-data.index');
    }

    public function store(Request $request)
    {

        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt'
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Check if a file was uploaded
        if ($file) {
            try {
                // Import data from the file
                $bank_case_data = (new FastExcel)->import($file, function ($line) {
                  //  dd($line);
                    // Import each row into the database
                    return BankCasedata::create([
                        'sl_no' => $line['S No.'],
                        'acknowledgement_no' => $line['Acknowledgement No.'],
                        'district' => $line['District ']
                    ]);
                });


                // Provide feedback to the user
                return response()->json([
                    'message' => 'File imported successfully',
                    'imported_records' => count($bank_case_data)
                ]);
            } catch (\Exception $e) {
                // Handle exceptions gracefully
                return response()->json([
                    'error' => 'An error occurred during import',
                    'message' => $e->getMessage()
                ], 500);
            }
            dd('ho');
        } else {
            // No file uploaded
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }
}
