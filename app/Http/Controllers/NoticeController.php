<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\EvidenceType;
use App\Models\Evidence;
use App\Models\Notice;
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


//     public function generateNotice(Request $request)
// {
//     $data = $request->input('data');
//     $noticeType = $request->input('notice_id');

//     // Initialize an array to store notice data
//     $allNotices = [];

//     foreach ($data as $item) {
//         $noticeData = []; // Initialize $noticeData for each item

//         // Prepare notice data based on notice type
//         switch ($noticeType) {
//             case 'both_ncrp_website':
//                 $noticeData = [
//                     'sub' => "Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000",
//                     'main_content' => "We are writing to bring your immediate attention regarding a complaint that has been registered in National Cyber Crime Reporting Portal (Acknowledgement No: {$item['ack_no']}) against the below-mentioned website, which is involved in financial fraud.",
//                     'content_1' => "As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE and PRESERVE the below-mentioned website and domain, which is registered on your domain registrar service. Additionally, as per Section 94 of the Bharatiya Nagarik Suraksha Sanhita (BNSS), you are also directed to PROVIDE the details associated with the alleged website to this office within 48 hours.",
//                     'content_2' =>  "As an intermediary, if you fail to remove or disable the unlawful contents immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.",
//                     'url_head' => "Alleged Website: ",
//                     'url' => $item['url'],
//                     'domain_name' => $item['domain'],
//                     'domain_id' => $item['registry_details'],
//                     'details_head' => "Details Required: ",
//                     'details_content' => "1. Registration details of the aforementioned website.\n"
//                                         . "2. Primary / alternate e-mail IDs and contact numbers associated with the aforementioned website.\n"
//                                         . "3. Registration IP address at the time of creation and last login IP address.\n"
//                                         . "4. Mode of payment details for registration.\n"
//                                         . "5. Any other subdomains with the above registration email ID or mobile number.",
//                     'footer_content' => "Urgent action and confirmation is solicited by return.\nContact us on: cyberops-fsm.pol@kerala.gov.in"
//                 ];
//                 break;

//             case '79_ncrp_website':
//                 $noticeData = [
//                     'sub' => "Notice U/sec 79(3)(b) of IT Act",
//                     'content' => "On detailed investigation, it has been found that this website operates as a scam under the guise of obtaining confidential banking user credentials and engages in online financial fraud, causing illegal financial loss to the public. As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE the below-mentioned website within 24 hours and PRESERVE the details for further investigation. As an intermediary, if you fail to remove or disable the unlawful content immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.",
//                     'number' => $item['ack_no'],
//                     'url' => $item['url'],
//                     'domain_name' => $item['domain'],
//                     'domain_id' => $item['registry_details'],
//                 ];
//                 break;

//             case '94_ncrp_website':
//                 $noticeData = [
//                     'sub' => "Notice U/Sec.94 BNSS Act 2023",
//                     'content' => "As stipulated by U/s 94 Bharatiya Nagarik Suraksha Sanhita (BNSS) we direct you to PROVIDE the below mentioned details within 24 hrs for further investigation.",
//                     'number' => $item['ack_no'],
//                     'url' => $item['url'],
//                     'domain_name' => $item['domain'],
//                     'domain_id' => $item['registry_details'],
//                 ];
//                 break;

//             // Add more cases as needed
//         }

//         // Generate HTML content
//         $htmlContent = View::make('notices.notice', ['notices' => [$noticeData]])->render();

//         // Save the notice content to MongoDB
//         $allNotices[] = [
//             'content' => $htmlContent,
//             'user_id' => Auth::user()->id,
//         ];
//     }

//     // Save all notices
//     foreach ($allNotices as $notice) {
//         Notice::updateOrCreate(
//             [
//                 'user_id' => $notice['user_id'],
//                 // Add any additional unique fields if needed
//             ],
//             [
//                 'content' => $notice['content']
//             ]
//         );
//     }

//     return redirect()->route('notices.index')->with('success', 'Notices generated and saved successfully.');
// }


    public function generateNotice(Request $request)
    {
        $data = $request->input('data');
        $noticeType = $request->input('notice_id');
        // dd($data);

        // foreach ($data as $item) {
        //     print_r($item['ack_no'].'<br>');
        // }
        // dd();

              // Prepare notice data based on notice type
              foreach ($data as $item) {
                $noticeData = []; // Initialize $noticeData for each item

                // Handle 'For All Notice Type' case
                if ($noticeType == 'all_ncrp') {
                    // Add your notice data for this case here
                    $noticeData[] = [
                        // Your data here
                    ];
                    // Repeat as needed
                } else {
                    // Handle other notice types individually
                    switch ($noticeType) {
                        case 'both_ncrp_social_media':
                            // dd("on hold"); // Consider using logging instead
                            $noticeData[] = [
                                // Your data here
                            ];
                            break;

                            case 'both_ncrp_website':
                                $noticeData[] = [
                                    'sub' => $item['sub'] ?? "Notice U/Sec. 94 of BNSS & 79(3)(b) of IT Act 2000",
                                    'main_content' => "We are writing to bring your immediate attention regarding a complaint that has been registered in National Cyber Crime Reporting Portal (Acknowledgement No: {$item['ack_no']}) against the below-mentioned website, which is involved in financial fraud.",
                                    'content_1' => "As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE and PRESERVE the below-mentioned website and domain, which is registered on your domain registrar service. Additionally, as per Section 94 of the Bharatiya Nagarik Suraksha Sanhita (BNSS), you are also directed to PROVIDE the details associated with the alleged website to this office within 48 hours.",
                                    'content_2' =>  "As an intermediary, if you fail to remove or disable the unlawful contents immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.",
                                    'url_head' => "Alleged Website: ",
                                    'url' => $item['url'],
                                    'domain_name' => $item['domain'],
                                    'domain_id' => $item['registry_details'],
                                    'details_head' => "Details Required: ",
                                    'details_content' => "1. Registration details of the aforementioned website.\n"
                                                        . "2. Primary / alternate e-mail IDs and contact numbers associated with the aforementioned website.\n"
                                                        . "3. Registration IP address at the time of creation and last login IP address.\n"
                                                        . "4. Mode of payment details for registration.\n"
                                                        . "5. Any other subdomains with the above registration email ID or mobile number.",
                                    'footer_content' => "Urgent action and confirmation is solicited by return.\nContact us on: cyberops-fsm.pol@kerala.gov.in"
                                ];
                                break;

                        case '79_ncrp_social_media':
                            // dd("on hold"); // Consider using logging instead
                            $noticeData[] = [
                                // Your data here
                            ];
                            break;

                        case '79_ncrp_website':
                            $noticeData[] = [
                                'sub' => "Notice U/sec 79(3)(b) of IT Act",
                                'content' => "On detailed investigation, it has been found that this website operates as a scam under the guise of obtaining confidential banking user credentials and engages in online financial fraud, causing illegal financial loss to the public. As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE the below-mentioned website within 24 hours and PRESERVE the details for further investigation. As an intermediary, if you fail to remove or disable the unlawful content immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.",
                                'number' => $item['ack_no'],
                                'url' => $item['url'],
                                'domain_name' => $item['domain'],
                                'domain_id' => $item['registry_details'],
                            ];
                            break;

                        case '94_ncrp_social_media':
                            // dd("on hold"); // Consider using logging instead
                            $noticeData[] = [
                                // Your data here
                            ];
                            break;

                        case '94_ncrp_website':
                            $noticeData[] = [
                                'sub' => "Notice U/Sec.94 BNSS Act 2023",
                                'content' => "As stipulated by U/s 94 Bharatiya Nagarik Suraksha Sanhita (BNSS) we direct you to PROVIDE the below mentioned details within 24 hrs for further investigation.",
                                'number' => $item['ack_no'],
                                'url' => $item['url'],
                                'domain_name' => $item['domain'],
                                'domain_id' => $item['registry_details'],
                            ];
                            break;
                    }
                }
// dd($noticeData);
        // Generate HTML content for each notice
        foreach ($noticeData as $notice) {
            $htmlContent = View::make('notices.notice', ['notices' => [$notice]])->render();

            // Save the notice content to MongoDB
            Notice::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'ack_number' => $item['ack_no'],
                    'url' => $item['url'],
                    'sub' => $notice['sub'] ?? '', // Ensure 'sub' is available
                ],
                [
                    'content' => $htmlContent,
                    // 'main_content' => $notice['main_content'] ?? null,
                    // 'content_1' => $notice['content_1'] ?? null,
                    // 'content_2' => $notice['content_2'] ?? null,
                    // 'url_head' => $notice['url_head'] ?? null,
                    // 'domain_name' => $notice['domain_name'] ?? null,
                    // 'domain_id' => $notice['domain_id'] ?? null,
                    // 'details_head' => $notice['details_head'] ?? null,
                    // 'details_content' => $notice['details_content'] ?? null,
                    // 'footer_content' => $notice['footer_content'] ?? null,
                ]
            );
        }
    }

    return redirect()->route('notices.index')->with('success', 'Notices generated and saved successfully.');
}

    public function Notices()
    {
        $notices = Notice::all(); // Fetch all notices from MongoDB
        // dd($notices);
        return view('notices.index', compact('notices')); // Pass the data to the view
    }

    public function showNotice($id)
    {
        $notice = Notice::findOrFail($id); // Retrieve the notice by ID
        return view('notices.show', ['notice' => $notice]); // Pass the data to the view
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





}
