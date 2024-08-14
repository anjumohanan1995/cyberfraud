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
use SplFileObject;
use Illuminate\Support\MessageBag;

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

                        try {
                            Excel::import(new BankImports, $tempFile, null, \Maatwebsite\Excel\Excel::CSV);
                            // $filePath = 'imports/temp_file.csv';
                            // ImportBankActionJob::dispatch($filePath , $tempFile);
                            unlink($tempFile);
                            return redirect()->back()->with('success', 'File imported successfully!');
                            Log::info('File imported successfully.');
                        } catch (\Exception $e) { 
                            // Log::error('Error importing file: ' . $e->getMessage()); 
                           
                            // // Redirect back with error messages
                            // //return redirect()->back()->withErrors($errors)->withInput();
                            // return redirect()->back()->withErrors($e->getMessage())->withInput();
                            // //return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
                            if ($e instanceof \Illuminate\Validation\ValidationException) {
               
                                $errors = $e->validator->getMessageBag()->all();
                                 
                                return redirect()->back()->withErrors($errors)->withInput();
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
                // $filePath = $request->file('file')->store('imports');
                // ImportBankActionJob::dispatch($filePath , $file);
            
                return redirect()->back()->with('success', 'File imported successfully!');
            }  catch (\Exception $e){
                if ($e instanceof \Illuminate\Validation\ValidationException) {
               
                   $errors = $e->validator->getMessageBag()->all();
                    
                   return redirect()->back()->withErrors($errors)->withInput();
                } else {
                
                    return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                }

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
           
                    $errors = $e->validator->getMessageBag()->all();
                    
                    return redirect()->back()->withErrors($errors)->withInput();
                } else {
           
                    return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                }

                
            } 

        }  
            
            
        }
        else{
            return redirect()->back()->with('error', 'No file uploaded. Please select a file to import.');
        }
    }
}
