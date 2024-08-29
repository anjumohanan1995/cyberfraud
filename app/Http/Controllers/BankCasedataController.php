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
use App\Jobs\ImportBankActionJob;
use Illuminate\Support\Facades\Auth;
use SplFileObject;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class BankCasedataController extends Controller
{



    public function index()
    {
        return view('dashboard.bank-case-data.index');
    }


    public function store(Request $request)
    {
        $file = $request->file('file');
        if ($request->hasFile('file')){
            $originalExtension = $file->getClientOriginalExtension(); 
            // dd($originalExtension);
            if($originalExtension == 'csv'){
                if($file->getMimeType() == "application/octet-stream"){
                   
                    $filePath = $file->getRealPath();

                    try {
                        
                        $contents = file_get_contents($filePath);
                        $encoding = mb_detect_encoding($contents, mb_list_encodings(), true);
                        Log::info('Detected encoding: ' . $encoding); // Log detected encoding

                        $convertedContents = iconv($encoding, 'UTF-8//TRANSLIT', $contents);
                       
                        
                        $tempFile = tempnam(sys_get_temp_dir(), 'csv'); 
                        file_put_contents($tempFile, $convertedContents);
                        if (($handle = fopen($tempFile, 'r')) !== FALSE) {
                            $structuredData = [];
                        
                            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                // Initialize an array to hold the structured row
                                $structuredRow = [];
                                
                                // Process the 0th index
                                if (isset($row[0])) {
                                    $explodedData = explode(',', $row[0]);
                                    // Trim any extra spaces around the exploded values
                                    $explodedData = array_map('trim', $explodedData);
                                    // Merge exploded data with the subsequent row data
                                    $structuredRow = array_merge($explodedData, array_slice($row, 1));
                                } else {
                                    // If the 0th index is missing, use nulls or empty strings as appropriate
                                    $structuredRow = array_merge(array_fill(0, 5, null), array_slice($row, 1));
                                }
                                
                                // Ensure the length of the structured row matches your expected length
                                // Adjust the number (e.g., 25) based on the maximum length required
                                $expectedLength = 25;
                                if (count($structuredRow) < $expectedLength) {
                                    $structuredRow = array_pad($structuredRow, $expectedLength, null);
                                }
                        
                                // Append the structured row to the main array
                                $structuredData[] = $structuredRow;

                            }
                        
                            fclose($handle);

                            function arrayToCsv($array, $filePath){
                                $file = fopen($filePath, 'w');
                                foreach ($array as $row) {
                                    fputcsv($file, $row);
                                }
                                fclose($file);
                            }

                            $newCsvFile = tempnam(sys_get_temp_dir(), 'csv');
                            arrayToCsv($structuredData, $newCsvFile);
                            
                        } else {
                            // Handle error if the file cannot be opened
                            error_log('Unable to open the CSV file: ' . $tempFile);
                        }

                        try {
                            Excel::import(new BankImports, $newCsvFile, null, \Maatwebsite\Excel\Excel::CSV);
                            // $userId = Auth::user()->id;
                            // $uploadId = (string) Str::uuid(); 
                            // session()->forget('upload_id');
                            // session()->put('upload_id', $uploadId);
                            // $filePath = 'imports/temp_file.csv';
                            // ImportBankActionJob::dispatch($tempFile ,$userId , $uploadId);
                            // unlink($tempFile);
                            // return redirect()->back()->with('success', 'Uploading...')->with('redirected', true);
                            return redirect()->back()->with('success', 'Uploaded Successfully');
                            
                           
                        } catch (\Exception $e){ 
                            // Log::error('Error importing file: ' . $e->getMessage()); 
                           
                            // // Redirect back with error messages
                            // //return redirect()->back()->withErrors($errors)->withInput();
                            // return redirect()->back()->withErrors($e->getMessage())->withInput();
                            // //return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
                            if ($e instanceof \Illuminate\Validation\ValidationException) {
               
                                return redirect()->back()->withErrors($e->errors())->withInput();
                             } else {
                             
                                 return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                             }
                        }

                       
                    } 
                    catch (\Exception $e) {
                      
                        Log::error('Error importing file: ' . $e->getMessage());
                        return response()->json(['error' => 'Error reading CSV file'], 500);
                    }

           }
           else{ 
            try {
                
                Excel::import(new BankImports, $file);
                // $userId = Auth::user()->id;
                // $uploadId = (string) Str::uuid(); 
                // session()->forget('upload_id');
                // session()->put('upload_id', $uploadId);
        
                // $filePath = $request->file('file')->store('imports');
                // ImportBankActionJob::dispatch($file, $userId, $uploadId);
            
                // return redirect()->back()->with('success', 'Uploading...')->with('redirected', true);
                return redirect()->back()->with('success', 'Uploading...');
            }  catch(\Illuminate\Validation\ValidationException $e){

                return redirect()->back()->withErrors($e->errors())->withInput();
            } 
            catch (\Exception $e) {
             
              
                return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
            }

           }


        }
        else{
            $request->validate([

                'file' => 'required|file|mimes:xlsx,csv,txt,ods' 
            ]);
            try {

                Excel::import(new BankImports, $file);
        
                return redirect()->back()->with('success', 'File imported successfully!');
            }  catch (\Exception $e){
                if ($e instanceof \Illuminate\Validation\ValidationException) {
               
                    return redirect()->back()->withErrors($e->errors())->withInput();
                 }  else {
           
                    return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                }

                
            } 

        }  
            
            
        }
        else{
            return redirect()->back()->with('error', 'No file uploaded. Please select a file to import.');
        }
    }

    public function disputeAmountUpdate(Request $request){

        $objectId = $request->input('object_id');
        $disputeAmount = $request->input('dispute_amount');
       
    
        // Perform your update logic here, e.g., using MongoDB:
        $bankData = BankCasedata::find($objectId);
        $bankData->dispute_amount = $disputeAmount;
        $bankData->save();

        return redirect()->back()->with('success', 'Dispute Amount Updated Sucessfully.');

    }
}
