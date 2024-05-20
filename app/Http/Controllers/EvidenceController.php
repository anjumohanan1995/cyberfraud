<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidence;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{

    public function create($case_id)
    {

        return view('dashboard.bank-case-data.evidence.create', compact('case_id'));
    }




    public function store(Request $request)
    {
        try {
            // Retrieve ACKNOWLEDGEMENT_NO from the URL
            $ack_no = $request->acknowledgement_number;
            $pdfPathsString = '';
            $screenshotPathsString = '';
             $validator = Validator::make($request->all(), [
        'evidence_type.*' => 'required',
        'url.*' => 'required|url',
        'domain.*' => 'nullable|string',
        'registry_details.*' => 'nullable|string',
        'ip.*' => 'nullable|ip',
        'registrar.*' => 'nullable|string',
        'pdf.*' => 'nullable|file|mimes:pdf|max:2048',
        'screenshots.*' => 'nullable|file|mimes:jpeg,bmp,png|max:2048',
        'remarks.*' => 'nullable|string',
    ],[
        'evidence_type.*.required' => 'The evidence type field is required.',
        'url.*.required' => 'The URL field is required.',
        'url.*.url' => 'The URL must be a valid URL format.',
        'domain.*.nullable' => 'The domain field is optional.',
        'registry_details.*.nullable' => 'The registry details field is optional.',
        'ip.*.ip' => 'The IP address must be a valid IP format',
        'ip.*.nullable' => 'The IP address field is optional..',
        'registrar.*.nullable' => 'The registrar field is optional.',
        'pdf.*.nullable' => 'The PDF field is optional.',
        'pdf.*.file' => 'The PDF must be a file.',
        'pdf.*.mimes' => 'The PDF must be a file of type: pdf.',
        'pdf.*.max' => 'The PDF may not be greater than 2MB.',
        'screenshots.*.nullable' => 'The Screenshots field is optional.',
        'screenshots.*.file' => 'The screenshots must be a file.',
        'screenshots.*.mimes' => 'The screenshots must be a file of type: jpeg, bmp, png.',
        'screenshots.*.max' => 'The screenshots may not be greater than 2MB.',
        'remarks.*.nullable' => 'The remarks field is optional.',
    ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            foreach ($request->evidence_type as $key => $type) {
                $evidence = new Evidence();
                $evidence->evidence_type = $type;
                if($request->hasFile('pdf'))
                {
                $pdf = $request->file('pdf');
                foreach($pdf as $pd){
                    foreach ($request->pdf as $keyss => $pdfs) {
                        $pdfName = $pdfs->getClientOriginalName();
                        $pdfCollection[$keyss] = $pdfs->storeAs('public/pdf', $pdfName);
                    }
                }
                }
            if (!empty($pdfCollection)) {
                $pdfPathsString = implode(',', $pdfCollection);
            }

            if($request->hasFile('screenshots'))
                {
                $screenshots = $request->file('screenshots');
                foreach($screenshots as $screenshot){
                    foreach ($request->screenshots as $keys => $screensho) {
                        $screenshotName = $screensho->getClientOriginalName();
                        $screenshotCollection[$keys] = $screensho->storeAs('public/pdf', $screenshotName);
                    }
                  //  echo "Upload Successfully";

                }
                }
            if (!empty($screenshotCollection)) {
                $screenshotPathsString = implode(',', $screenshotCollection);
            }
                $evidence->pdf = $pdfPathsString;;
                $evidence->screenshots = $screenshotPathsString;
                $evidence->ack_no = $ack_no;
                switch ($type) {
                    case 'website':
                        $evidence->url = $request->url[$key];
                        $evidence->domain = $request->domain[$key];
                        $evidence->registry_details = $request->registry_details[$key];
                        $evidence->ip = $request->ip[$key];
                        $evidence->registrar = $request->registrar[$key];
                        break;
                    case 'instagram':
                    case 'telegram':
                        $evidence->url = $request->url[$key];
                        break;
                }

                $evidence->remarks = $request->remarks[$key];

                $evidence->save();
            }
        return redirect()->back()->with('success', 'Evidence added successfully!');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function index($ack_no)
    {
        $evidences = Evidence::where('ack_no', $ack_no)->get();

        return view('dashboard.bank-case-data.evidence.index', compact('evidences'));
    }


    public function destroy($id)
    {
        try {
            $evidence = Evidence::findOrFail($id);

            if ($evidence->pdf) {
                $pdfPaths = explode(',', $evidence->pdf);
                foreach ($pdfPaths as $path) {
                    Storage::delete($path);
                }
            }

            if ($evidence->screenshots) {
                $screenshotPaths = explode(',', $evidence->screenshots);
                foreach ($screenshotPaths as $path) {
                    Storage::delete($path);
                }
            }

            $evidence->delete();
            return redirect()->back()->with('success', 'Evidence deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


}
