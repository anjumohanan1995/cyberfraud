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
use App\Jobs\ImportComplaintsJob;
use App\Models\UploadErrors;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Maatwebsite\Excel\Fakes\ExcelFake;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function importComplaints()
    {   
        // Fetch only active source types from the database
        $sourceTypes = SourceType::where('status', 'active')->get();
        $upload_id = session('upload_id');
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
                try {
                    $importer = new ComplaintImportOthers($source_type, $request->case_number, $fileName);
                    FacadesExcel::import($importer, $request->complaint_file);

                    return redirect()->back()->with('success', 'Form submitted successfully!');
                } catch (\Exception $e) {
                    $errors = json_decode($e->getMessage(), true);
                    return redirect()->back()->with('import_errors', $errors)->withInput();
                }

            }

            else{

                $request->validate([
                    'complaint_file' => 'required|file|mimes:xlsx,csv,txt,ods|max:100000'
                ]);

                if ($file){
                    try {

                        // FacadesExcel::import(new ComplaintImport($source_type), $file);
                        $userId = Auth::user()->id;
                        $uploadId = (string) Str::uuid();
                        session()->forget('upload_id');
                        session()->put('upload_id', $uploadId);
                        $filePath = $request->file('complaint_file')->store('imports');
                        ImportComplaintsJob::dispatch($filePath, $source_type , $userId , $uploadId);
                        // return redirect()->back()->with('success', 'Uploading...');
                        return redirect()->back()->with('redirected', true);
                        
                        UploadErrors::where('upload_id', $uploadId)->delete();
                        
                    } catch (\Illuminate\Validation\ValidationException $e){
                        // Show all validation errors

                        return redirect()->back()->withErrors($e->errors())->withInput();
                    } 
                    catch (\Exception $e){
                        Log::error($e->getMessage());

                        return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
                    }
                } else{
                    // No file uploaded
                    return response()->json(['error' => 'No file uploaded'], 400);
                }
            }
        }




}

    public function showUploadErrors($uploadId){

        $errors = UploadErrors::where('user_id', auth()->id())->where('upload_id', $uploadId)->get();

        return response()->json(['errors' => $errors]);
    }

    public function clearSessionErrors(){
        session()->forget('upload_id');
        return response()->json(['status' => 'success']);
    }

}
