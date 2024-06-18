<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidence;
use App\Models\EvidenceType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\ComplaintOthers;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use MongoDB;
use Carbon\Carbon;

class EvidenceController extends Controller
{

    public function create($case_id)
    {

        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->get();
        // Loop through each EvidenceType and print the name field
// foreach ($evidenceTypes as $evidenceType) {
//     dd($evidenceType->name);
// }

        return view('dashboard.bank-case-data.evidence.create', compact('case_id','evidenceTypes'));
    }




    public function store(Request $request)
    {
        try {
            // Retrieve ACKNOWLEDGEMENT_NO from the URL
            $ack_no = $request->acknowledgement_number;
            $new_id = Crypt::encrypt($ack_no);
            $pdfPathsString = '';
            $screenshotPathsString = '';
            //dd($request->all());
             $validator = Validator::make($request->all(), [
        'evidence_type.*' => 'required',
        'evidence_type_id.*' => 'required',
        'url.*' => 'required|url',
        'domain.*' => 'nullable|string',
        'registry_details.*' => 'nullable|string',
        'ip.*' => 'nullable|ip',
        'registrar.*' => 'nullable|string',
        'pdf.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
        'screenshots.*' => 'nullable|file|mimes:jpeg,bmp,png|max:2048',
        'remarks.*' => 'nullable|string',
        'ticket.*' => 'nullable|string',
        'category.*' => 'nullable|string',
    ],[
        'evidence_type.*.required' => 'The evidence type field is required.',
        'url.*.required' => 'The URL field is required.',
        'url.*.url' => 'The URL must be a valid URL format.',
        'domain.*.nullable' => 'The domain field is optional.',
        'registry_details.*.nullable' => 'The registry details field is optional.',
        'ip.*.ip' => 'The IP address must be a valid IP format',
        'ip.*.nullable' => 'The IP address field is optional..',
        'registrar.*.nullable' => 'The registrar field is optional.',
        'pdf.*.nullable' => 'The Document field is optional.',
        'pdf.*.file' => 'The Document must be a file.',
        'pdf.*.mimes' => 'The Document must be a file of type: pdf, doc, docx, xls, xlsx, ppt, pptx.',
        'pdf.*.max' => 'The Document may not be greater than 2MB.',
        'screenshots.*.nullable' => 'The Screenshots field is optional.',
        'screenshots.*.file' => 'The screenshots must be a file.',
        'screenshots.*.mimes' => 'The screenshots must be a file of type: jpeg, bmp, png.',
        'screenshots.*.max' => 'The screenshots may not be greater than 2MB.',
        'remarks.*.nullable' => 'The remarks field is optional.',
        'ticket.*.nullable' => 'The ticket field is optional.',
        'category.*.nullable' => 'The category field is optional.',
    ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            // dd($request);
            foreach ($request->evidence_type as $key => $type) {
                $evidence = new Evidence();
                $evidence->evidence_type = $type;
                // dd($request->evidence_type_id[$key]);
                $evidence->evidence_type_id = $request->evidence_type_id[$key];


                if ($request->hasFile('pdf') && $request->file('pdf')[$key]->isValid()) {
                    $pdfFile = $request->file('pdf')[$key];

                    // Generate a unique file name
                    $uniqueFileName = uniqid() . '_' . $pdfFile->getClientOriginalName();

                    // Store the file with the unique name
                    $filePath = $pdfFile->storeAs('public/pdf', $uniqueFileName);

                    // Uncomment this line if you need to debug the file path
                    // dd($filePath);

                    $evidence->pdf = $filePath;
                    // dd($evidence);
                }

                if ($request->hasFile('screenshots') && $request->file('screenshots')[$key]->isValid()) {
                    $screenshotFile = $request->file('screenshots')[$key];

                    // Generate a unique file name
                    $uniqueFileName = uniqid() . '_' . $screenshotFile->getClientOriginalName();

                    // Store the file with the unique name
                    $filePath = $screenshotFile->storeAs('public/screenshots', $uniqueFileName);

                    // Uncomment this line if you need to debug the file path
                    // dd($filePath);

                    $evidence->screenshots = $filePath;
                    // dd($evidence);
                }

                // Assign other data
                $evidence->ack_no = $ack_no;
                $evidence->ticket = $request->ticket[$key];
                $evidence->category = $request->category[$key];
                switch ($type) {
                    case 'website':
                        // dd($evidence);
                        $evidence->url = $request->url[$key];
                        $evidence->domain = $request->domain[$key];
                        $evidence->registry_details = $request->registry_details[$key];
                        $evidence->ip = $request->ip[$key];
                        $evidence->registrar = $request->registrar[$key];
                        break;
                    default:
                        $evidence->url = $request->url[$key];
                        break;
                }
                $evidence->remarks = $request->remarks[$key];
                // dd($evidence);

                // Save evidence
                $evidence->save();



            }
            return redirect()->route('evidence.index', ['acknowledgement_no' => $new_id])->with('success', 'Evidence added successfully!');
        } catch (\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function index($ack_no)
    {

        $new_id = Crypt::decrypt($ack_no);
        $numeric_id = preg_replace('/[^0-9]/', '', $new_id);
        $evidences = Evidence::where('ack_no', $numeric_id)->get();
//dd($numeric_id);
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

    public function evidenceManagement(){
        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->get();

   
       
        return view('evidence-management.list',compact('evidenceTypes'));
    }

    public function evidenceNcrp(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); 
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; 
        $columnName = $columnName_arr[$columnIndex]['data']; 
        $columnSortOrder = $order_arr[0]['dir']; 
        $searchValue = $search_arr['value']; 
        $ack_no="";
        $acknowledgement_no = $request->acknowledgement_no;
        $url = $request->url;
        $domain = $request->domain;
        $evidence_type = $request->evidence_type;
        $evidence_type_text = $request->evidence_type_text;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $current_date = $request->current_date;
        if($current_date){
            $from_date = Carbon::today('Asia/Kolkata')->toDateString();
            $to_date = $from_date;
        }

        $evidences = Evidence::raw(function($collection) use ($start, $rowperpage,$acknowledgement_no,$url,$domain ,$evidence_type , $evidence_type_text, $from_date , $to_date){

            if ($from_date && $to_date) {
                $startOfDay = Carbon::createFromFormat('Y-m-d', $from_date, 'Asia/Kolkata')->startOfDay();
                $endOfDay = Carbon::createFromFormat('Y-m-d', $to_date, 'Asia/Kolkata')->endOfDay();
    
                $utcStartDate = $startOfDay->copy()->setTimezone('UTC');
                $utcEndDate = $endOfDay->copy()->setTimezone('UTC');
            }

            $pipeline = [
                
                [
                    '$group' => [
                        '_id' => '$ack_no',
                        'evidence_type' => ['$push' => '$evidence_type'],
                        'url' => ['$push' => '$url'],
                        'domain' => ['$push' => '$domain'],
                        'registry_details' => ['$push' => '$registry_details'],
                        'ip' => ['$push' => '$ip'],
                        'registrar' => ['$push' => '$registrar'],
                        
                    ]
                ],
                [
                    '$sort' => [
                        '_id' => 1,
                ]
                ],
                [
                    '$skip' => (int)$start
                ],
                [
                    '$limit' => (int)$rowperpage
                ],
                
            ];

            if (isset($acknowledgement_no)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ack_no' => $acknowledgement_no
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($evidence_type)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'evidence_type' => $evidence_type_text
                        ]
                    ]
                ], $pipeline);
            }
            if ($from_date && $to_date){
                $pipeline = array_merge([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new MongoDB\BSON\UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }
                  
            return $collection->aggregate($pipeline);
        });
        
        $distinctEvidences = Evidence::raw(function($collection) use ($acknowledgement_no ,$url , $domain ,$evidence_type , $evidence_type_text ,$from_date , $to_date) {

            if ($from_date && $to_date) {
                $startOfDay = Carbon::createFromFormat('Y-m-d', $from_date, 'Asia/Kolkata')->startOfDay();
                $endOfDay = Carbon::createFromFormat('Y-m-d', $to_date, 'Asia/Kolkata')->endOfDay();
    
                $utcStartDate = $startOfDay->copy()->setTimezone('UTC');
                $utcEndDate = $endOfDay->copy()->setTimezone('UTC');
            }

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$ack_no'
                    ]
                ]
            ];

            if (isset($acknowledgement_no)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ack_no' => $acknowledgement_no
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($evidence_type)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'evidence_type' => $evidence_type_text
                        ]
                    ]
                ], $pipeline);
            }

            if ($from_date && $to_date){
                $pipeline = array_merge([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new MongoDB\BSON\UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }
            
            return $collection->aggregate($pipeline);
        });

        $totalRecords = count($distinctEvidences);
        $data_arr = array();
        $i = $start;


        $totalRecordswithFilter =  $totalRecords;
      
        foreach($evidences as $record){

            $i++;
            $url = "";$domain="";$ip="";$registrar="";$remarks=""; $evidence_type="";$registry_details="";

            $acknowledgement_no = $record->_id;

            foreach ($record->url as $item) {
                $url .= $item."<br>";
            }
            foreach ($record->evidence_type as $item) {
             $evidence_type .= $item."<br>";
            }
            foreach ($record->domain as $item) {
                $domain .= $item."<br>";
            }
            foreach ($record->ip as $item) {
                $ip .= $item."<br>";
            }
            foreach ($record->registrar as $item) {
                $registrar .= $item."<br>";
            }
            foreach ($record->registry_details as $item) {
                $registry_details .= $item."<br>";
            }

            $data_arr[] = array(
                    "id" => $i,
                    "acknowledgement_no" => $acknowledgement_no,
                    "evidence_type" => $evidence_type,
                    "url" => $url,
                    "domain" => $domain,
                    "ip" => $ip,
                    "registrar"=>$registrar,
                    "registry_details" => $registry_details,
                    "edit" => '',
                    );

        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);

    }

    public function evidenceOthers(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); 
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; 
        $columnName = $columnName_arr[$columnIndex]['data']; 
        $columnSortOrder = $order_arr[0]['dir']; 
        $searchValue = $search_arr['value']; 
        $ack_no="";
        $case_number = $request->case_number;
        $url = $request->url;
        $domain = $request->domain;
        $evidence_type = $request->evidence_type;
        $evidence_type_text = $request->evidence_type_text;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $current_date = $request->current_date;
        if($current_date){
            $from_date = Carbon::today('Asia/Kolkata')->toDateString();
            $to_date = $from_date;
        }
      
        $evidences = ComplaintOthers::raw(function($collection) use ($start, $rowperpage, $case_number, $url, $domain, $evidence_type, $evidence_type_text, $from_date, $to_date) {


            if ($from_date && $to_date) {
                $startOfDay = Carbon::createFromFormat('Y-m-d', $from_date, 'Asia/Kolkata')->startOfDay();
                $endOfDay = Carbon::createFromFormat('Y-m-d', $to_date, 'Asia/Kolkata')->endOfDay();
    
                $utcStartDate = $startOfDay->copy()->setTimezone('UTC');
                $utcEndDate = $endOfDay->copy()->setTimezone('UTC');
            }

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$case_number',
                        'evidence_type' => ['$push' => '$evidence_type'],
                        'url' => ['$push' => '$url'],
                        'domain' => ['$push' => '$domain'],
                        'registry_details' => ['$push' => '$registry_details'],
                        'ip' => ['$push' => '$ip'],
                        'registrar' => ['$push' => '$registrar'],
                    ]
                ],
                [
                    '$sort' => [
                        '_id' => 1,
                    ]
                ],
                [
                    '$skip' => (int)$start
                ],
                [
                    '$limit' => (int)$rowperpage
                ],
            ];
          
        
            if (isset($case_number)) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'case_number' => $case_number
                        ]
                    ]
                ], $pipeline);
                
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
                
            }
            if (isset($evidence_type)) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                        'evidence_type' => $evidence_type
                        ]
                    ]
                ], $pipeline);
               
            }
            if ($from_date && $to_date){
                $pipeline = array_merge([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new MongoDB\BSON\UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }
        
            return $collection->aggregate($pipeline);
        });
        
        $distinctEvidences = ComplaintOthers::raw(function($collection) use ($case_number ,$url , $domain ,$evidence_type , $evidence_type_text , $from_date , $to_date ) {
            
            if ($from_date && $to_date) {
                $startOfDay = Carbon::createFromFormat('Y-m-d', $from_date, 'Asia/Kolkata')->startOfDay();
                $endOfDay = Carbon::createFromFormat('Y-m-d', $to_date, 'Asia/Kolkata')->endOfDay();
    
                $utcStartDate = $startOfDay->copy()->setTimezone('UTC');
                $utcEndDate = $endOfDay->copy()->setTimezone('UTC');
            }

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$case_number'
                    ]
                ]
            ];

            if (isset($case_number)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'case_number' => $case_number
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($url)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'url' => $url
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($domain)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'domain' => $domain
                        ]
                    ]
                ], $pipeline);
            }
            if (isset($evidence_type)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'evidence_type' => $evidence_type_text
                        ]
                    ]
                ], $pipeline);
            }
            if ($from_date && $to_date){
                $pipeline = array_merge([
                    ['$match' => [
                        'created_at' => [
                            '$gte' => new MongoDB\BSON\UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new MongoDB\BSON\UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }


            return $collection->aggregate($pipeline);
        });
       
        $totalRecords = count($distinctEvidences); 
        $data_arr = array();
        $i = $start;


        $totalRecordswithFilter =  $totalRecords;
      
        foreach($evidences as $record){

            $i++;
            $url = "";$domain="";$ip="";$registrar="";$remarks=""; $evidence_type="";$registry_details="";

            $case_number = $record->_id;

            foreach ($record->url as $item) {
                $url .= $item."<br>";
            }
            foreach ($record->evidence_type as $item) {
             $evidence_type .= $item."<br>";
            }
            foreach ($record->domain as $item) {
                $domain .= $item."<br>";
            }
            foreach ($record->ip as $item) {
                $ip .= $item."<br>";
            }
            foreach ($record->registrar as $item) {
                $registrar .= $item."<br>";
            }
            foreach ($record->registry_details as $item) {
                $registry_details .= $item."<br>";
            }

            $data_arr[] = array(
                    "id" => $i,
                    "case_number" => $case_number,
                    "evidence_type" => $evidence_type,
                    "url" => $url,
                    "domain" => $domain,
                    "ip" => $ip,
                    "registrar"=>$registrar,
                    "registry_details" => $registry_details,
                    "edit" => '',
                    );

        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }


}
