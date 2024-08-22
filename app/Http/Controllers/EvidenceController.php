<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidence;
use App\Models\EvidenceType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\ComplaintOthers;
use App\Models\CountryCode;
use App\Models\Complaint;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use MongoDB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EvidenceBulkImport;
use App\exports\SampleExport;

class EvidenceController extends Controller
{

    public function create($case_id)
    {

        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->get();
        $countries = CountryCode::all();

        // foreach ($countries as $country) {
        //     // Access the 'country' field of each CountryCode object
        //     dd($country->country);
        // }
// dd($countries);
        // Loop through each EvidenceType and print the name field
// foreach ($evidenceTypes as $evidenceType) {
//     dd($evidenceType->name);
// }

        return view('dashboard.bank-case-data.evidence.create', compact('case_id','evidenceTypes','countries'));
    }




    public function store(Request $request)
    {
        // dd($request);
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
                'url.*' => 'nullable|url',
                'domain.*' => 'nullable|string',
                'registry_details.*' => 'nullable|string',
                'ip.*' => 'nullable|ip',
                'registrar.*' => 'nullable|string',
                'pdf.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
                'screenshots.*' => 'required|file|mimes:jpeg,bmp,png|max:2048',
                'remarks.*' => 'required|string',
                'ticket.*' => 'required|string',
                'data_disclosure.*' => 'required|string',
                'preservation.*' => 'required|string',
                'category.*' => 'required|string|in:phishing,malware,fraud,other',
                'mobile.*' => 'required_with:country_code.*', // Requires 'country_code' when 'mobile' is present
                'country_code.*' => 'required_with:mobile.*', // Requires 'mobile' when 'country_code' is present

            ], [
                'evidence_type.*.required' => 'The evidence type field is required.',
                'evidence_type_id.*.required' => 'The evidence type ID field is required.',
                'url.*.url' => 'The URL must be a valid URL format.',
                'domain.*.string' => 'The domain must be a string.',
                'registry_details.*.string' => 'The registry details must be a string.',
                'ip.*.ip' => 'The IP address must be a valid IP format.',
                'registrar.*.string' => 'The registrar must be a string.',
                'pdf.*.file' => 'The document must be a file.',
                'pdf.*.mimes' => 'The document must be a file of type: pdf, doc, docx, xls, xlsx, ppt, pptx.',
                'pdf.*.max' => 'The document may not be greater than 2MB.',
                'screenshots.*.file' => 'The screenshots must be a file.',
                'screenshots.*.mimes' => 'The screenshots must be a file of type: jpeg, bmp, png.',
                'screenshots.*.max' => 'The screenshots may not be greater than 2MB.',
                'remarks.*.string' => 'The remarks must be a string.',
                'ticket.*.string' => 'The ticket must be a string.',
                'data_disclosure.*.string' => 'The data disclosure must be a string.',
                'preservation.*.string' => 'The preservation must be a string.',
                'category.*.string' => 'The category must be a string.',
                'category.*.in' => 'The selected category is invalid.',
                'mobile.*.required_with' => 'The mobile field is required when country code is present.',
                'country_code.*.required_with' => 'The country code field is required when mobile is present.',
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
                $evidence->data_disclosure = $request->data_disclosure[$key];
                $evidence->preservation = $request->preservation[$key];
                $evidence->category = $request->category[$key];
                $evidence->remarks = $request->remarks[$key];
                $evidence->reported_status = "active";
                switch ($type) {
                    case 'website':
                        // dd($evidence);
                        $evidence->url = $request->url[$key];
                        $evidence->domain = $request->domain[$key];
                        $evidence->registry_details = $request->registry_details[$key];
                        $evidence->ip = $request->ip[$key];
                        $evidence->registrar = $request->registrar[$key];
                        break;
                        case 'mobile':
                        case 'whatsapp':
                            $evidence->url = $request->mobile[$key];
                            $evidence->country_code = $request->country_code[$key];
                            break;
                    default:
                        $evidence->url = $request->url[$key];
                        $evidence->domain = $request->domain[$key];
                        break;
                }

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
        $ip = $request->ip;
        $domain = $request->domain;
        $evidence_type = $request->evidence_type;
        $evidence_name = $request->evidence_name;
        // dd($evidence_name);
        $evidence_type_text = $request->evidence_type_text;
        // dd($evidence_type_text);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $current_date = $request->current_date;

        // // Convert fromDate and toDate to start and end of the day
        // $fromDateStart = $fromDate ? Carbon::parse($from_date)->startOfDay() : null;
        // $toDateEnd = $toDate ? Carbon::parse($to_date)->endOfDay() : null;
        $fromDateStart = Carbon::parse($from_date)->startOfDay();
        $toDateEnd = Carbon::parse($to_date)->endOfDay();


        if($current_date){
            $today = Carbon::today('Asia/Kolkata');
            $fromDateStart = $today->copy()->startOfDay();
            $toDateEnd = $today->copy()->endOfDay();
        }

        // First, get the acknowledgement numbers from the Complaints model based on date filters
        $filteredAckNumbers = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
            ->pluck('acknowledgement_no')
            ->toArray();
            // dd($filteredAckNumbers);

            $sortableFields = [
                'acknowledgement_no' => '_id',
                'evidence_type' => 'evidence_type',
                'url' => 'url',
                'domain' => 'domain',
                'ip' => 'ip',
                'registrar' => 'registrar',
                'registry_details' => 'registry_details'
            ];

        $evidences = Evidence::raw(function($collection) use ($start, $rowperpage,$acknowledgement_no,$url,$ip,$domain ,$evidence_type , $evidence_type_text, $filteredAckNumbers, $searchValue, $columnName, $columnSortOrder, $sortableFields){

            $pipeline = [

                [
                    '$group' => [
                        '_id' => '$ack_no',
                        'evidence_type_ids' => [
                            '$push' => [
                                'evidence_type' => '$evidence_type',
                                'evidence_type_id' => '$evidence_type_id'
                            ]
                        ],
                        'reported_status' => ['$first' => '$reported_status'],
                        // 'evidence_type' => ['$push' => '$evidence_type'],
                        'url' => ['$push' => '$url'],
                        'domain' => ['$push' => '$domain'],
                        'registry_details' => ['$push' => '$registry_details'],
                        'ip' => ['$push' => '$ip'],
                        'registrar' => ['$push' => '$registrar'],

                    ]
                ]

            ];

        // Apply filters
        $matchConditions = [];

        if (!empty($filteredAckNumbers)) {
            $stringFilteredAckNumbers = array_map('strval', $filteredAckNumbers);
            $matchConditions['ack_no'] = ['$in' => $stringFilteredAckNumbers];
        }

        if (isset($acknowledgement_no)) {
            $matchConditions['ack_no'] = $acknowledgement_no;
        }

        if (isset($url)) {
            $matchConditions['url'] = $url;
        }

        if (isset($domain)) {
            $matchConditions['domain'] = $domain;
        }

        if (isset($ip)) {
            $matchConditions['ip'] = $ip;
        }

        if (isset($evidence_type)) {
            $matchConditions['evidence_type'] = $evidence_type_text;
        }

        if (!empty($searchValue)) {
            $matchConditions['$or'] = [
                ['ack_no' => ['$regex' => $searchValue, '$options' => 'i']],
                ['evidence_type' => ['$regex' => $searchValue, '$options' => 'i']],
                ['url' => ['$regex' => $searchValue, '$options' => 'i']],
                ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
                ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
                ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
                ['registry_details' => ['$regex' => $searchValue, '$options' => 'i']]
            ];
        }

        if (!empty($matchConditions)) {
            array_unshift($pipeline, ['$match' => $matchConditions]);
        }

        // Add dynamic sort stage
        if (isset($sortableFields[$columnName])) {
            $sortField = $sortableFields[$columnName];
            $sortDirection = $columnSortOrder === 'asc' ? 1 : -1;
            $pipeline[] = [
                '$sort' => [
                    $sortField => $sortDirection
                ]
            ];
        }

        // Add skip and limit stages
        $pipeline[] = ['$skip' => (int)$start];
        $pipeline[] = ['$limit' => (int)$rowperpage];

        return $collection->aggregate($pipeline);
        });

        $distinctEvidences = Evidence::raw(function($collection) use ($acknowledgement_no ,$url ,$ip, $domain ,$evidence_type , $evidence_type_text ,$filteredAckNumbers, $searchValue) {

            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$ack_no'

                    ]
                ]
            ];

                    // Apply the same filters as in the main query
        $matchConditions = [];

        if (!empty($filteredAckNumbers)) {
            $stringFilteredAckNumbers = array_map('strval', $filteredAckNumbers);
            $matchConditions['ack_no'] = ['$in' => $stringFilteredAckNumbers];
        }

        if (isset($acknowledgement_no)) {
            $matchConditions['ack_no'] = $acknowledgement_no;
        }

        if (isset($url)) {
            $matchConditions['url'] = $url;
        }

        if (isset($domain)) {
            $matchConditions['domain'] = $domain;
        }

        if (isset($ip)) {
            $matchConditions['ip'] = $ip;
        }

        if (isset($evidence_type)) {
            $matchConditions['evidence_type'] = $evidence_type_text;
        }

        if (!empty($searchValue)) {
            $matchConditions['$or'] = [
                ['ack_no' => ['$regex' => $searchValue, '$options' => 'i']],
                ['evidence_type' => ['$regex' => $searchValue, '$options' => 'i']],
                ['url' => ['$regex' => $searchValue, '$options' => 'i']],
                ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
                ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
                ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
                ['registry_details' => ['$regex' => $searchValue, '$options' => 'i']]
            ];
        }
            if (!empty($matchConditions)) {
                array_unshift($pipeline, ['$match' => $matchConditions]);
            }

            return $collection->aggregate($pipeline);
        });

        $totalRecords = count($distinctEvidences);
        $data_arr = array();
        $i = $start;


        $totalRecordswithFilter =  $totalRecords;


        foreach($evidences as $record){

            $i++;
            $url = "";$domain="";$ip="";$registrar="";$remarks=""; $evidence_type="";$registry_details="";$mobile="";

            $acknowledgement_no = $record->_id;
            // $website_id = '';

            foreach ($record->url as $item) {
                $url .= '<a href="#" data-url="' . $item . '" data-type="ncrp" class="check-status">'.$item."</a><br>";
            }

            // $getEvidence = Evidence::get(['evidence_type', 'url', 'mobile']);
            // // Iterate over the collection to access each record
            // foreach ($getEvidence as $getevidence) {
            //     if (isset($getevidence->mobile)) {
            //         $mobile .= $getevidence->mobile."<br>";
            //     }
            // }



            foreach ($record->evidence_type_ids as $item){

                $evidence_type .= $item['evidence_type'] . "<br>";

                // if ($item['evidence_type'] === "website") {

                //     $website_id = $item['evidence_type_id'];
                // }
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



        //     $editButton = '';
        //     if ($website_id) {
        //     $status = '
        //     <div>
        //         <a class="btn btn-primary" href="' . route('get-mailmerge-list', ['id' => $website_id,'ack_no' => $record->_id ]) . '"><small>Mail Merge</small></a>
        //     </div>';
        // }


        $ack_no = '
        <div>
            <a class="btn btn-primary" href="' . route('get-mailmerge-list', ['ack_no' => $record->_id ]) . '"><small>' . $record->_id . '</small></a>
        </div>';



        // $editButton = '';
        // if ($website_id) {
        //     $editButton = '
        //     <div>
        //         <a class="btn btn-primary" href="' . route('get-mailmerge-list', ['id' => $website_id,'ack_no' => $record->_id ]) . '"><small>Mail Merge</small></a>
        //     </div>';
        // }


    $data_arr[] = array(
        "id" => $i,
        "acknowledgement_no" => $ack_no,
        "evidence_type" => $evidence_type,
        "url" => $url,
        // "mobile" => $mobile,
        "domain" => $domain,
        "ip" => $ip,
        "registrar" => $registrar,
        "registry_details" => $registry_details,
        // "edit" => $editButton,
        // "status" => $status,
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

    public function updateReportedStatus($id, Request $request)
    {
        // dd($id);
        $status = $request->input('status');
        // dd($status);

        // Find the evidence by _id
        $evidence = Evidence::where('_id', $id)->first();

        // Check if evidence exists
        if (!$evidence) {
            return response()->json(['error' => 'No document found for the given _id'], 404);
        }

        // Update reported_status based on the status parameter
        $evidence->reported_status = $status;
        $evidence->save();

        // Respond with a success message
        return response()->json(['message' => 'Status updated successfully']);
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
        $ip = $request->ip;
        $evidence_type = $request->evidence_type;
        $evidence_type_text = $request->evidence_type_text;
        // dd($evidence_type_text);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $current_date = $request->current_date;
        if($current_date){
            $from_date = Carbon::today('Asia/Kolkata')->toDateString();
            $to_date = $from_date;
        }

        $sortableFields = [
            'case_number' => '_id',
            'evidence_type' => 'evidence_type',
            'url' => 'url',
            'domain' => 'domain',
            'ip' => 'ip',
            'registrar' => 'registrar',
            'registry_details' => 'registry_details'
        ];

        $evidences = ComplaintOthers::raw(function($collection) use ($start, $rowperpage, $case_number, $url, $domain,$ip, $evidence_type, $evidence_type_text, $from_date, $to_date, $searchValue, $columnName, $columnSortOrder, $sortableFields) {


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
                        'reported_status' => ['$first' => '$reported_status'],
                        'evidence_type' => ['$push' => '$evidence_type'],
                        'url' => ['$push' => '$url'],
                        'domain' => ['$push' => '$domain'],
                        'registry_details' => ['$push' => '$registry_details'],
                        'ip' => ['$push' => '$ip'],
                        'registrar' => ['$push' => '$registrar'],
                    ]
                ]
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
            if (isset($ip)) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ip' => $ip
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
                            '$gte' => new UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }

            if (!empty($searchValue)) {
                $matchStage['$or'] = [
                    ['case_number' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['evidence_type' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['url' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['registry_details' => ['$regex' => $searchValue, '$options' => 'i']]  // Search by source name
                ];
                $pipeline = array_merge([['$match' => $matchStage]], $pipeline);
            }

            // Add dynamic sort stage
            if (isset($sortableFields[$columnName])) {
                $sortField = $sortableFields[$columnName];
                $sortDirection = $columnSortOrder === 'asc' ? 1 : -1;
                $pipeline[] = [
                    '$sort' => [
                        $sortField => $sortDirection
                    ]
                ];
            } else {
                // Default sort if column is not sortable
                $pipeline[] = [
                    '$sort' => [
                        '_id' => 1
                    ]
                ];
            }

            // Add skip and limit stages
            $pipeline[] = ['$skip' => (int)$start];
            $pipeline[] = ['$limit' => (int)$rowperpage];

            return $collection->aggregate($pipeline);
        });

        $distinctEvidences = ComplaintOthers::raw(function($collection) use ($case_number ,$url , $domain ,$ip, $evidence_type , $evidence_type_text , $from_date , $to_date,$searchValue ) {

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
            if (isset($ip)) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'ip' => $ip
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
                            '$gte' => new UTCDateTime($utcStartDate->timestamp * 1000),
                            '$lte' => new UTCDateTime($utcEndDate->timestamp * 1000)
                        ]
                    ]]
                ], $pipeline);
            }
            if (!empty($searchValue)) {
                $matchStage['$or'] = [
                    ['case_number' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['evidence_type' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['url' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['domain' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['registrar' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['ip' => ['$regex' => $searchValue, '$options' => 'i']],
                    ['registry_details' => ['$regex' => $searchValue, '$options' => 'i']]  // Search by source name
                ];
                $pipeline = array_merge([['$match' => $matchStage]], $pipeline);
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

            // $case_number = $record->_id;
            // $website_name = '';

            foreach ($record->url as $item) {
                $url .= '<a href="#" data-url="' . $item . '" data-type="others" class="check-status">'.$item."</a><br>";
            }
            foreach ($record->evidence_type as $item) {
                $evidence_type .= $item . "<br>";
                if ($item == "website") {
                    $website_name = "website";
                    // dd($website_name);
                }
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

            $case_number = '
            <div>
                <a class="btn btn-primary" href="' . route('get-mailmerge-listother', ['case_number' => $record->_id ]) . '"><small>' . $record->_id . '</small></a>
            </div>';
            $data_arr[] = array(
                    "id" => $i,
                    "case_number" => $case_number,
                    "evidence_type" => $evidence_type,
                    "url" => $url,
                    "domain" => $domain,
                    "ip" => $ip,
                    "registrar"=>$registrar,
                    "registry_details" => $registry_details,
                    // "edit" => $editButton,
                    // "status" => $status,
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

    public function statusRecheck(Request $request){
        $ackno = $request->ackno;

        if($request->type==='ncrp'){

            $urls = Evidence::where('ack_no',$ackno )->pluck('url');
        }
        else{

            $urls = ComplaintOthers::where('case_number',$ackno )->pluck('url');
        }

        if($urls){
            if($request->type==='ncrp'){
                foreach($urls as $url){
                    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                        // Handle invalid URL
                        $status_code = 400;
                        $status_text = 'Bad Request';
                        $reported_status = 'inactive';
                        continue;
                    }

                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 10,
                        ],
                    ]);
                    //$headers = @get_headers($url);
                    $headers = @get_headers($url, 0, $context);

                   // dd($headers);
                    if($headers === false){
                        $status_code = 400;
                        $status_text = 'Bad Request';
                        $reported_status = 'inactive';

                    }
                    else{
                        $statusLine = $headers[0];
                        preg_match('/\d{3}/', $statusLine, $matches);
                        $status_code = isset($matches[0]) ? $matches[0] : null;
                        $parts = explode(' ', $statusLine, 3);
                        if (count($parts) >= 2) {
                            $status_text = $parts[2];

                        } else {
                            $status_text = "Failed to determine status text.";

                        }
                        $reported_status = $status_code === '200' ? 'reported' : 'inactive';

                    }

                        Evidence::where('ack_no',$ackno)->where('url', $url)
                                ->where('reported_status','reported')
                                ->update(['url_status' => $status_code,
                                  'url_status_text'=> $status_text,
                                  'reported_status' => $reported_status
                       ]);



                }
            }
            else{

                foreach($urls as $url){

                    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                        // Handle invalid URL
                        $status_code = 400;
                        $status_text = 'Bad Request';
                        $reported_status = 'inactive';
                        continue;
                    }

                    $context = stream_context_create([
                        'http' => [
                            'timeout' => 10,
                        ],
                    ]);
                    //$headers = @get_headers($url);
                    $headers = @get_headers($url, 0, $context);

                    if($headers === false){
                        $status_code = 400;
                        $status_text = 'Bad Request';
                        $reported_status = 'inactive';

                    }
                    else{

                        $statusLine = $headers[0];
                        preg_match('/\d{3}/', $statusLine, $matches);
                        $status_code = isset($matches[0]) ? $matches[0] : null;
                        $parts = explode(' ', $statusLine, 3);
                        if (count($parts) >= 2) {
                            $status_text = $parts[2];

                        } else {
                            $status_text = "Failed to determine status text.";

                        }
                        $reported_status = $status_code === '200' ? 'reported' : 'inactive';

                    }


                        ComplaintOthers::where('case_number',$ackno)->where('url', $url)
                                   ->where('reported_status','reported')
                                   ->update(['url_status' => $status_code,
                                  'url_status_text'=> $status_text,
                                  'reported_status' => $reported_status

                       ]);



                }
            }

            return response()->json(['success'=>true]);
        }
        else{
            return response()->json(['success'=>false]);
        }

    }

    public function urlStatus(Request $request){

        $type = $request->type;
        if($type=='ncrp'){
            $status = Evidence::where('url', $request->url)->first();
        }
        else{
            $status = ComplaintOthers::where('url', $request->url)->first();
        }

        if($status){
            $responseData = [
                'statuscode' => $status->url_status,
                'statustext' => $status->url_status_text,
                'url' => $status->url
            ];
            return response()->json($responseData);
        }
        else{
            return response()->json(['error' => 'Status not found for the given URL.'], 404);
        }
    }

    public function updateReportedStatusOther($id, Request $request)
    {
        // dd($id);
        $status = $request->input('status');
        // dd($status);

        // Find the evidence by _id
        $evidence = ComplaintOthers::where('_id', $id)->first();

        // Check if evidence exists
        if (!$evidence) {
            return response()->json(['error' => 'No document found for the given _id'], 404);
        }

        // Update reported_status based on the status parameter
        $evidence->reported_status = $status;
        $evidence->save();

        // Respond with a success message
        return response()->json(['message' => 'Status updated successfully']);
    }

    // public function evidenceBulkUpload(Request $request){

    //     // return view('dashboard.bank-case-data.evidence.bulkUpload',['ackno'=>$ackno]);
    //     return view('dashboard.bank-case-data.evidence.bulkUpload');
    // }

    public function evidenceBulkImport(Request $request){

        // return view('dashboard.bank-case-data.evidence.bulkUpload',['ackno'=>$ackno]);
        return view('dashboard.bank-case-data.evidence.bulkUpload');
    }

    public function evidenceBulkUploadFile(Request $request)
    {
        // Validate the file input
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls,ods'
        ]);

        // Check if a file is present in the request
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $import = new EvidenceBulkImport;

            // Run the import process
            Excel::import($import, $file);

            // Check if there are any custom errors from the import process
            if (!empty($import->getErrors())) {
                // Redirect with errors using withErrors
                return redirect()->route('evidence.bulk.import', $request->ackno)
                    ->withErrors($import->getErrors()) // Ensure this is an array of error messages
                    ->withInput();
            }

            // Redirect on successful import
            return redirect()->route('evidence.bulk.import', $request->ackno)
                ->with('success', 'File uploaded successfully.');
        }

        // Redirect back with validation errors
        return redirect()->back()->withErrors(['file' => 'File upload failed.'])->withInput();
    }


    // public function evidenceBulkUploadFile(Request $request){

    //     $request->validate([
    //         'file' => 'required|mimes:csv,xlsx,xls,ods'
    //     ]);

    //     if ($request->hasFile('file')){
    //         $file = $request->file('file');
    //         Excel::import(new EvidenceBulkImport, $file);
    //         return redirect()->route('evidence.bulk.import',$request->ackno)->with('success', 'File uploaded successfully.');
    //     }
    //     return redirect('evidence.bulk.import',$request->ackno)->with('error', 'File upload failed.');


    // }

    public function storeEvidence(Request $request)
    {

            // Define validation rules
    $request->validate([
        'source_type' => 'required|string|in:ncrp,other',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);
        $errorMessages = [];

        $source_type = $request->input('source_type');
        $from_date_input = $request->input('from_date');
        $to_date_input = $request->input('to_date');
        $ack_no = $request->input('ack_no');
        $case_no = $request->input('case_no');
        $evidence_type_ncrp = $request->input('evidence_type_ncrp');
        $evidence_type_ncrp_name = Evidence::whereNull('deleted_at')
                                            ->where('evidence_type_id', $evidence_type_ncrp)
                                            ->pluck('evidence_type')
                                            ->unique()
                                            ->first();
        $evidence_type_other = $request->input('evidence_type_other');
        $status = $request->input('status');

        // Convert fromDate and toDate to start and end of the day
        $fromDateStart = Carbon::parse($from_date_input)->startOfDay();
        $toDateEnd = Carbon::parse($to_date_input)->endOfDay();

                // Convert dates to Carbon instances and then to MongoDB compatible date format
        $from_date = new UTCDateTime(Carbon::parse($from_date_input)->startOfDay());
        $to_date = new UTCDateTime(Carbon::parse($to_date_input)->endOfDay());

        // Retrieve acknowledgement_no values from complaints within the specified date range
        $acknowledgementNos = Complaint::whereBetween('entry_date', [$fromDateStart,  $toDateEnd])
                                        ->pluck('acknowledgement_no')
                                        ->toArray();

        // Convert the integers to strings
        $acknowledgementNo = array_map('strval', $acknowledgementNos);

        // Initialize query based on $source_type
        if ($source_type == "ncrp") {
            $query = Evidence::whereNull('deleted_at');
        } elseif ($source_type == "other") {
            $query = ComplaintOthers::whereNull('deleted_at');
        } else {
            $errorMessages[] = 'Invalid source type.';
            return response()->json([
                'message' => 'Invalid request',
                'error_messages' => $errorMessages
            ], 400);
        }

        // Apply date range filter
        if ($from_date_input && $to_date_input && $source_type == "ncrp") {
            $query->whereIn('ack_no', $acknowledgementNo);
        } elseif ($from_date_input && $to_date_input) {
            $query->whereBetween('created_at', [$from_date, $to_date]);
        }elseif ($from_date_input || $to_date_input) {
            $errorMessages[] = 'Both from date and to date must be provided for date range filtering.';
        }

        // Apply date range filter
        // if ($from_date_input && $to_date_input && $source_type == 'other') {
        //     $query->whereBetween('created_at', [$from_date, $to_date]);
        // } elseif ($from_date_input || $to_date_input) {
        //     $errorMessages[] = 'Both from date and to date must be provided for date range filtering.';
        // }


        // Apply additional filters step-by-step and check data after each step
        if ($ack_no) {
            $query->where('ack_no', $ack_no);
            $data = $query->get();
            if ($data->isEmpty()) {
                $errorMessages[] = 'No records found for the provided acknowledgement number.';
                return response()->json([
                    'message' => 'Data received successfully',
                    'data' => $data,
                    'error_messages' => $errorMessages,
                    'source_type' => $source_type,
                    'from_date' => $from_date_input,
                    'to_date' => $to_date_input,
                    'ack_no' => $ack_no,
                    'case_no' => $case_no,
                    'evidence_type_ncrp' => $evidence_type_ncrp,
                    'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
                    'evidence_type_other' => $evidence_type_other,
                    'status' => $status
                ]);
            }
        }

        if ($case_no) {
            $query->where('case_number', $case_no);
            $data = $query->get();
            if ($data->isEmpty()) {
                $errorMessages[] = 'No records found for the provided case number.';
                return response()->json([
                    'message' => 'Data received successfully',
                    'data' => $data,
                    'error_messages' => $errorMessages,
                    'source_type' => $source_type,
                    'from_date' => $from_date_input,
                    'to_date' => $to_date_input,
                    'ack_no' => $ack_no,
                    'case_no' => $case_no,
                    'evidence_type_ncrp' => $evidence_type_ncrp,
                    'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
                    'evidence_type_other' => $evidence_type_other,
                    'status' => $status
                ]);
            }
        }

        if ($evidence_type_ncrp) {
            $query->where('evidence_type_id', $evidence_type_ncrp);
            $data = $query->get();
            if ($data->isEmpty()) {
                $errorMessages[] = 'No records found for the provided evidence type (NCRP).';
                return response()->json([
                    'message' => 'Data received successfully',
                    'data' => $data,
                    'error_messages' => $errorMessages,
                    'source_type' => $source_type,
                    'from_date' => $from_date_input,
                    'to_date' => $to_date_input,
                    'ack_no' => $ack_no,
                    'case_no' => $case_no,
                    'evidence_type_ncrp' => $evidence_type_ncrp,
                    'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
                    'evidence_type_other' => $evidence_type_other,
                    'status' => $status
                ]);
            }
        }

        if ($evidence_type_other) {
            $query->where('evidence_type', $evidence_type_other);
            $data = $query->get();
            if ($data->isEmpty()) {
                $errorMessages[] = 'No records found for the provided evidence type (other).';
                return response()->json([
                    'message' => 'Data received successfully',
                    'data' => $data,
                    'error_messages' => $errorMessages,
                    'source_type' => $source_type,
                    'from_date' => $from_date_input,
                    'to_date' => $to_date_input,
                    'ack_no' => $ack_no,
                    'case_no' => $case_no,
                    'evidence_type_ncrp' => $evidence_type_ncrp,
                    'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
                    'evidence_type_other' => $evidence_type_other,
                    'status' => $status
                ]);
            }
        }

        if ($status) {
            $query->where('reported_status', $status);
            $data = $query->get();
            if ($data->isEmpty()) {
                $errorMessages[] = 'No records found for the provided status.';
                return response()->json([
                    'message' => 'Data received successfully',
                    'data' => $data,
                    'error_messages' => $errorMessages,
                    'source_type' => $source_type,
                    'from_date' => $from_date_input,
                    'to_date' => $to_date_input,
                    'ack_no' => $ack_no,
                    'case_no' => $case_no,
                    'evidence_type_ncrp' => $evidence_type_ncrp,
                    'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
                    'evidence_type_other' => $evidence_type_other,
                    'status' => $status
                ]);
            }
        }

        // Execute query and get final data
        $data = $query->get();

        // dd($data, $errorMessages);
        // dd($data);
// dd($errorMessages);
        // Check if final data is empty and return error message
        if ($data->isEmpty()) {
            $errorMessages[] = 'No records found for the provided criteria.';
        }

        // Return data to the view
        return response()->json([
            'message' => 'Data received successfully',
            'data' => $data,
            'error_messages' => $errorMessages,
            'source_type' => $source_type,
            'from_date' => $from_date_input,
            'to_date' => $to_date_input,
            'ack_no' => $ack_no,
            'case_no' => $case_no,
            'evidence_type_ncrp' => $evidence_type_ncrp,
            'evidence_type_ncrp_name' => $evidence_type_ncrp_name,
            'evidence_type_other' => $evidence_type_other,
            'status' => $status
        ]);
    }

    public function createEvidenceDownloadTemplate()
    {

        $excelData = [];
        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->pluck('name')
        ->toArray();

        $uniqueItems = array_unique($evidenceTypes);
        $commaSeparatedString = implode(',', $uniqueItems);

        $firstRow = ['The evidence types should be the following :  ' . $commaSeparatedString];

        $additionalRowsData = [
            [ 'Acknowledgement No','URL/Mobile', 'Domain/Post/Profile','IP/Modus Keyword','Registrar','Registry Details','Remarks','Content Removal Ticket','Data Disclosure Ticket','Preservation Ticket','Evidence Type','Category' ],
            ['1212120', 'https://forum.com', 'forum.com','192.0.2.16','GoDaddy','klkl','Site maintenance','TK0016','TK0017','TK0018','Instagram','Phishing'],
            ['1215212', 'https://abcd.com', 'abcd.com','192.2.2.16','sdsdds','rtrt','Site ghghg','TK0023','TK0024','TK0025','Website','Malware'],
            ['1216212', 'https://dfdf.com', 'dfdf.com','192.3.2.16','bnnn','ghgh','ghgh gg','TK0052','TK0053','TK0054','Facebook','Fraud'],

        ];
        return Excel::download(new SampleExport($firstRow,$additionalRowsData), 'template.xlsx');
        // return Excel::download(new SampleExport($additionalRowsData), 'template.xlsx');

    }



}
