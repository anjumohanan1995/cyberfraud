<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\EvidenceType;
use App\Models\Evidence;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;


use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use MongoDB;

use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function againstEvidence(){

        // $source_types = SourceType::where('deleted_at',null)->get();
        // dd($source_types);
        $evidence_types = EvidenceType::where('deleted_at',null)->get();
        // $evidence = Evidence::where('deleted_at',null)->get();
        return view('notice.evidence',compact('evidence_types'));
    }

    public function evidenceListNotice(Request $request){


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

        $from_date="";$to_date="";
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $acknowledgement_no = $request->ackno;
        $source_type = $request->source_type;
        $evidence_type = $request->evidence_type;

        // $items = Evidence::where('ack_no', $request->ackno)
        //                     ->orderBy('_id', 'desc')
        //                     ->orderBy($columnName, $columnSortOrder);

        // $records = $items->skip($start)->take($rowperpage)->get();
        // $totalRecord = Evidence::where('ack_no', $request->ackno)->orderBy('_id', 'desc');
        // $totalRecords = $totalRecord->select('count(*) as allcount')->count();
        // $totalRecordswithFilter = $totalRecords;

        $evidences = Evidence::raw(function($collection) use ($start, $rowperpage,$acknowledgement_no,$source_type, $from_date , $to_date){

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
                        'ip' => ['$push' => '$ip'],

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



            if (isset($source_type)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'source_type' => $source_type
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
        $distinctEvidences = Evidence::raw(function($collection) use ($acknowledgement_no , $source_type ,$from_date , $to_date) {

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
            if (isset($source_type)){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'source_type' => $source_type
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

            if (isset($record->url) && !empty($record->url)) {
                foreach ($record->url as $item) {
                    $url .= $item . "<br>";

                }
            } else {
                $url = $record->mobile;
                // print_r($url);
            }
            // dd();
            foreach ($record->evidence_type as $item) {
             $evidence_type .= $item."<br>";
            }
            foreach ($record->domain as $item) {
                $domain .= $item."<br>";
            }
            foreach ($record->ip as $item) {
                $ip .= $item."<br>";
            }
            $edit = '<div style="margin-left: 10px;">
            <button class="btn btn-success" onclick="showPortalModal(\'' . $acknowledgement_no . '\', \'' . addslashes($evidence_type) . '\', \'' . addslashes($url) . '\')">
                <i class="fas fa-file-alt" data-toggle="tooltip" data-placement="top" title="Generate Notice"></i>
            </button>
        </div>';

        // PENDING NOTICE MANAGMENT ONCLICK ON ABOVE  <button class="btn btn-success" >


            $data_arr[] = array(
                    "id" => $i,
                "acknowledgement_no" => $acknowledgement_no,
                "evidence_type" => $evidence_type,
                "url" => $url,
                "domain"=>$domain,
                "ip"=>$ip,
                "edit" => $edit
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

    public function generateNotice(Request $request)
{
    $data = $request->input('data');
    // dd($data);
    $noticeType = $request->input('notice_id');
    $source_type = $request->input('source_type');


    // Prepare notice data based on notice type
    $noticeData = []; // Initialize an array to store notice data
    $groupedData = [];

    // Initialize an empty array to store counts of each evidence type
$evidenceTypeCounts = [];

    // Group data by ack_no
    foreach ($data as $item) {
        $ackNo = $source_type == "ncrp" ? $item['ack_no'] : $item['case_number'];
        // dd($ackNo);
        $evidenceType = $item['evidence_type'];


        // if (!$ackNo || !$evidenceType) {
        //     continue; // Skip items with missing data
        // }

        if ($evidenceType === 'website') {
            if (in_array($noticeType, [
                'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - website',
                'Notice U/sec 79(3)(b) of IT Act - NCRP - website',
                'Notice U/Sec.94 BNSS Act 2023 - NCRP - website',
                'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - website',
                'Notice U/sec 79(3)(b) of IT Act - Other - website',
                'Notice U/Sec.94 BNSS Act 2023 - Other - website'
            ])) {
            $noticeData[] = [
                'ack_no' => $ackNo,
                'urls' => $item['url'] ?? '',
                'domain_name' => $item['domain'] ?? '',
                'domain_id' => $item['registry_details'] ?? ''
            ];
        }
        } else if ($evidenceType !== "mobile" && $evidenceType !== "website") {
            if (in_array($noticeType, [
                'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - social media',
                'Notice U/sec 79(3)(b) of IT Act - NCRP - social media',
                'Notice U/Sec.94 BNSS Act 2023 - NCRP - social media',
                'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - social media',
                'Notice U/sec 79(3)(b) of IT Act - Other - social media',
                'Notice U/Sec.94 BNSS Act 2023 - Other - social media'
            ])) {
                // Group data by ack_no and evidence_type
                if (!isset($groupedData[$ackNo][$evidenceType])) {
                    $groupedData[$ackNo][$evidenceType] = [
                        'ack_no' => $ackNo,
                        'evidence_type' => $evidenceType,
                        'domains' => [],
                        'categories' => [],
                        'urls' => [],
                    ];
                }

                $groupedData[$ackNo][$evidenceType]['domains'][] = $item['domain'];
                $groupedData[$ackNo][$evidenceType]['categories'][] = $source_type == "ncrp" ? $item['category'] : $item['ip'];
                $groupedData[$ackNo][$evidenceType]['urls'][] = $item['url'];
                // dd($categories);
            }
        }
    }

        // Create notice data with chunked URLs
        foreach ($groupedData as $ackNoGroup) {
            foreach ($ackNoGroup as $evidenceTypeGroup) {
                $totalCount = count($evidenceTypeGroup['urls']);
                $chunks = array_chunk($evidenceTypeGroup['urls'], 5);
                $chunkCount = count($chunks);

                for ($i = 0; $i < $chunkCount; $i++) {
                    $noticeData[] = [
                        'ack_no' => $evidenceTypeGroup['ack_no'],
                        'evidence_type' => $evidenceTypeGroup['evidence_type'],
                        'urls' => array_slice($chunks[$i], 0, 5),
                        'domains' => array_slice($evidenceTypeGroup['domains'], $i * 5, 5),
                        'categories' => array_slice($evidenceTypeGroup['categories'], $i * 5, 5),
                    ];
                }
            }
    }
    // dd();
    // Generate HTML content for each notice
    foreach ($noticeData as $notice) {
        // print_r($notice);

        $combinedHtmlContent = '';

        switch ($noticeType) {
            case 'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - website':
                $combinedHtmlContent = View::make('notices.both_ncrp_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/sec 79(3)(b) of IT Act - NCRP - website':
                $combinedHtmlContent = View::make('notices.79_ncrp_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec.94 BNSS Act 2023 - NCRP - website':
                $combinedHtmlContent = View::make('notices.94_ncrp_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - NCRP - social media':
                $combinedHtmlContent = View::make('notices.both_ncrp_social_media', ['notice' => $notice])->render();
                break;

            case 'Notice U/sec 79(3)(b) of IT Act - NCRP - social media':
                $combinedHtmlContent = View::make('notices.79_ncrp_social_media', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec.94 BNSS Act 2023 - NCRP - social media':
                $combinedHtmlContent = View::make('notices.94_ncrp_social_media', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - website':
                $combinedHtmlContent = View::make('notices.both_other_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/sec 79(3)(b) of IT Act - Other - website':
                $combinedHtmlContent = View::make('notices.79_other_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec.94 BNSS Act 2023 - Other - website':
                $combinedHtmlContent = View::make('notices.94_other_website', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000 - Other - social media':
                $combinedHtmlContent = View::make('notices.both_other_social_media', ['notice' => $notice])->render();
                break;

            case 'Notice U/sec 79(3)(b) of IT Act - Other - social media':
                $combinedHtmlContent = View::make('notices.79_other_social_media', ['notice' => $notice])->render();
                break;

            case 'Notice U/Sec.94 BNSS Act 2023 - Other - social media':
                $combinedHtmlContent = View::make('notices.94_other_social_media', ['notice' => $notice])->render();
                break;

            default:
                $combinedHtmlContent = '';
                break;
        }

        // print_r($combinedHtmlContent);

        // Build the criteria array based on the source_type
        $criteria = [
            'user_id' => Auth::user()->id,
            'url' => $notice['urls'], // Store URLs as a comma-separated string
            'notice_type' => $noticeType,
        ];

        // Add the conditionally required field
        if ($source_type == "ncrp") {
            $criteria['ack_number'] = $notice['ack_no'];
        } else {
            $criteria['case_number'] = $notice['ack_no'];
        }
// dd($criteria);

        // Save the notice content to MongoDB
        Notice::updateOrCreate(
            $criteria,

            [
                'content' => $combinedHtmlContent,
                // Add additional fields if needed
            ]
        );
        // dd("sucess");
    }
    // dd();
    // dd("sucess");

    return redirect()->route('notices.index')->with('success', 'Notices generated and saved successfully.');
}



public function Notices()
{
    $currentUserId = Auth::user()->id; // Get the current authenticated user's ID
    // dd($currentUserId);

    // Fetch notices based on the presence of `assing_by_user_id` field
    $notices = Notice::where(function ($query) use ($currentUserId) {
        // Show notices where `assing_by_user_id` matches the current user's ID or where it is not present
        $query->where('assing_to_user_id', $currentUserId)
              ->orWhereNull('assing_to_user_id');
    })->get();
    // dd($notices);

    return view('notices.index', compact('notices')); // Pass the data to the view
}

    public function showNotice($id)
    {
        $notice = Notice::findOrFail($id); // Retrieve the notice by ID
        $currentUserId = auth()->id(); // Get the current authenticated user's ID
        // dd($currentUserId);
        $users = User::where('_id', '!=', $currentUserId)->get();
        // dd($users);
        return view('notices.show', ['notice' => $notice,'users' => $users]); // Pass the data to the view
    }

public function editNoticeView($id)
{
    $notice = Notice::findOrFail($id);
    return view('notices.edit', compact('notice'));
}

public function updateNotice(Request $request, $id)
{
    // Validate the request data
    $request->validate([
        'content' => 'required|string', // Validate the content field
    ]);

    // Find the notice by ID
    $notice = Notice::findOrFail($id);

    // Update the content field
    $notice->content = $request->input('content');
    $notice->save(); // Save the changes

    // Redirect back to the notice show page with a success message
    return redirect()->route('notices.show', $notice->id)->with('success', 'Notice updated successfully');
}

public function follow(Request $request, $id)
{
    // dd($request);
    $notice = Notice::findOrFail($id); // Find the notice by ID
    // dd($notice);

    // Validate and update the notice with the selected user ID
    $notice->assing_to_user_id = $request->input('user_id'); // Assuming 'followed_by_user_id' is the field to be updated

    $notice->assing_by_user_id = Auth::user()->id;
//   dd($notice);
    $notice->save();

    return response()->json(['success' => true]);
}

}
