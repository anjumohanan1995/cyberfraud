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

        // Process the Excel file
        Excel::import(new ComplaintImport, $file);

        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }

}
