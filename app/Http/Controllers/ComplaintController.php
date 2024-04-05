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

       // dd($request->file);
        try{
            Excel::import(new ComplaintImport, $request->complaint_file);
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::alert($e->failures());
        }
       return redirect()->route('patients.index')
                        ->with('success','Patients Added successfully');
    }

}
