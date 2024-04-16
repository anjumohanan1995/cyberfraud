<?php

namespace App\Http\Controllers;

use App\Models\BankCasedata;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class BankCasedataController extends Controller
{



    public function index()
    {
        return view('dashboard.bank-case-data.index');
    }

    // public function store(Request $request)
    // {

    //     // Validate the uploaded file
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,csv,txt'
    //     ]);

    //     // Get the uploaded file
    //     $file = $request->file('file');

    //     // Check if a file was uploaded
    //     if ($file) {
    //         try {
    //             // Import data from the file
    //             $bank_case_data = (new FastExcel)->import($file, function ($line) {
    //                 // Import each row into the database
    //                 return BankCasedata::create([
    //                     'sl_no' => $line[1], // Assuming 'S No.' is the first column (index 0)
    //                     'acknowledgement_no' => $line[2], // Assuming 'Acknowledgement No.' is the second column (index 1)
    //                     'district' => $line[3]
    //                 ]);
    //             });


    //             // Provide feedback to the user
    //             return response()->json([
    //                 'message' => 'File imported successfully',
    //                 'imported_records' => count($bank_case_data)
    //             ]);
    //         } catch (\Exception $e) {
    //             // Handle exceptions gracefully
    //             return response()->json([
    //                 'error' => 'An error occurred during import',
    //                 'message' => $e->getMessage()
    //             ], 500);
    //         }
    //         dd('ho');
    //     } else {
    //         // No file uploaded
    //         return response()->json(['error' => 'No file uploaded'], 400);
    //     }
    // }


    // public function store(Request $request)
    // {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,csv,txt'
    //     ]);

    //     // Get the uploaded file
    //     $file = $request->file('file');

    //     // Check if a file was uploaded
    //     if ($file) {
    //         try {
    //             // Import data from the file
    //             $bank_case_data = (new FastExcel)->import($file, function ($line) {
    //                 try {
    //                     $values = array_values($line);


    //                     // Validation Rules
    //                     $rules = [
    //                         0 => 'required', // sl_no is required
    //                         1 => 'required', // acknowledgement_no is required
    //                         2 => 'required', // transaction_id_or_utr_no is required
    //                         3 => 'required', // Layer is required
    //                         23 => 'required', // Layer is required
    //                         // Add more validation rules as needed
    //                     ];

    //                     // Validate the data
    //                     $validator = Validator::make($values, $rules);

    //                     // Check if validation fails
    //                     if ($validator->fails()) {
    //                         // Log validation errors
    //                         Log::warning('Validation failed for row: ' . implode(', ', $values));
    //                         // Return false to skip importing this row
    //                         return false;
    //                     }

    //                     // Check if there's an existing record with matching acknowledgement_no or transaction_id_or_utr_no
    //                     $existingRecord = BankCasedata::where('acknowledgement_no', $values[1])->Where('transaction_id_or_utr_no', $values[2])->first();

    //                     if ($existingRecord) {
    //                         // dd('update');
    //                         // Update existing record
    //                         $existingRecord->update([
    //                             'sl_no' => $values[0],
    //                             'Layer' => $values[3],
    //                             'account_no_1' => $values[3],
    //                             'action_taken_by_bank' => $values[3],
    //                             'bank' => $values[3],
    //                             'account_no_2' => $values[3],
    //                             'ifsc_code' => $values[3],
    //                             'cheque_no' => $values[3],
    //                             'mid' => $values[3],
    //                             'tid' => $values[3],
    //                             'approval_code' => $values[3],
    //                             'merchant_name' => $values[3],
    //                             'transaction_date' => $values[3],
    //                             'transaction_amount' => $values[3],
    //                             'reference_no' => $values[3],
    //                             'remarks' => $values[3],
    //                             'date_of_action' => $values[6],
    //                             'action_taken_by_bank' => $values[7],
    //                             'action_taken_name' => $values[8],
    //                             'action_taken_email' => $values[9],
    //                             'branch_location' => $values[10],
    //                             'branch_manager_details' => $values[11],
    //                             // Add more fields as needed


    //                         ]);
    //                     } else {
    //                         // dd('create');
    //                         // Create new record
    //                         BankCasedata::create([
    //                             'sl_no' => $values[0],
    //                             'acknowledgement_no' => $values[1],
    //                             'transaction_id_or_utr_no' => $values[2],
    //                             'Layer' => $values[3],
    //                             // Add more fields as needed
    //                         ]);
    //                     }
    //                 } catch (Exception $e) {
    //                     // Handle any exceptions or errors during import
    //                     Log::error('Error importing data: ' . $e->getMessage());
    //                 }
    //             });


    //             // Provide feedback to the user
    //             return response()->json([
    //                 'message' => 'File imported successfully',
    //                 'imported_records' => count($bank_case_data)
    //             ]);
    //         } catch (\Exception $e) {
    //             // Handle exceptions gracefully
    //             return response()->json([
    //                 'error' => 'An error occurred during import',
    //                 'message' => $e->getMessage()
    //             ], 500);
    //         }
    //     } else {
    //         // No file uploaded
    //         return response()->json(['error' => 'No file uploaded'], 400);
    //     }
    // }




    public function store(Request $request)
    {
        // Validate the uploaded file.
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt'
        ]);

        // Get the uploaded file.
        $file = $request->file('file');

        // Check if a file was uploaded.
        if ($file) {
            try {
                // Import data from the file.
                $bank_case_data = (new FastExcel)->import($file, function ($line) {


                    Validator::make($line, [
                        'Email' => 'required',
                        'Name' => 'required',
                        'Password' => 'required',
                    ]);


                    try {
                        $values = array_values($line);

                        // Validation Rules.
                        $rules = [
                            0 => 'required', // sl_no is required
                            1 => 'required', // acknowledgement_no is required
                            2 => 'required', // transaction_id_or_utr_no is required
                            3 => 'required', // Layer is required.
                            10 => 'filled',
                            23 => 'filled',
                            // Add more validation rules as needed.
                        ];

                        // Validate the data.
                        $validator = Validator::make($values, $rules);

                        // Check if validation fails.
                        if ($validator->fails()) {
                            // Get the validation error messages.
                            $errors = $validator->errors()->all();

                            dd($errors);

                            // Return JSON response with validation errors.
                            return response()->json([
                                'error' => 'Validation failed',
                                'errors' => $errors
                            ], 422);
                        }

                        // Check if there's an existing record with matching acknowledgement_no or transaction_id_or_utr_no.
                        $existingRecord = BankCasedata::where('acknowledgement_no', $values[1])->orWhere('transaction_id_or_utr_no', $values[2])->first();

                        if ($existingRecord) {
                            // Update existing record.
                            $existingRecord->update([
                                'sl_no' => $values[0],
                                'Layer' => $values[3],
                                // Add more fields as needed.
                            ]);
                        } else {
                            // Create new record
                            BankCasedata::create([
                                'sl_no' => $values[0],
                                'acknowledgement_no' => $values[1],
                                'transaction_id_or_utr_no' => $values[2],
                                'Layer' => $values[3],
                                // Add more fields as needed.
                            ]);
                        }
                    } catch (Exception $e) {
                        // Handle any exceptions or errors during import.
                        Log::error('Error importing data: ' . $e->getMessage());
                    }
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



        } else {
            // No file uploaded
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }
}
