<?php

namespace App\Http\Controllers;

use App\Models\SourceType;
use App\Models\EvidenceType;
use App\Models\Evidence;
use App\Models\Notice;
use App\Models\User;
use App\Models\Bank;
use App\Models\Wallet;
use App\Models\Merchant;
use App\Models\Insurance;
use App\Models\BankCasedata;
use App\Models\Complaint;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use MongoDB;
use MongoDB\BSON\Regex;

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
                'domain_id' => $item['registry_details'] ?? '',
                'ip' => $item['ip'] ?? '',
                'evidence_type' => $evidenceType,
            ];
        }
        } else if ($evidenceType !== "mobile" && $evidenceType !== "website" && $evidenceType !== "whatsapp") {
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
        'url' => is_array($notice['urls']) ? implode(', ', $notice['urls']) : $notice['urls'],
        'notice_type' => $noticeType,
        'source_type' => $source_type,
        'type' => 'Evidence',
    ];

    // dd($notice['evidence_type']);

    // Add evidence_type if it exists in the notice
    if (isset($notice['evidence_type'])) {
        $criteria['evidence_type'] = $notice['evidence_type'];
    }

    // Add domain if it exists in the notice
    if (isset($notice['domain_name'])) {
        $criteria['domain'] = $notice['domain_name'];
    } elseif (isset($notice['domains'])) {
        $criteria['domain'] = implode(', ', $notice['domains']);
    }

    if ($notice['evidence_type'] == "website") {
        if (isset($notice['ip'])) {
            $criteria['ip'] = $notice['ip'];
        }
    } else {
        // Add IP or category based on source_type
        if ($source_type == "ncrp" || $source_type != "ncrp") {
            if (isset($notice['categories'])) {
                $criteria['ip'] = implode(', ', $notice['categories']);
            }
        }
    }


    // Add the conditionally required field
    if ($source_type == "ncrp") {
        $criteria['ack_number'] = $notice['ack_no'];
    } else {
        $criteria['case_number'] = $notice['ack_no'];
    }
// dd($evidenceType);


    $evidenceType = $notice['evidence_type'];
    // Check if a similar notice already exists
    $existingNotice = Notice::where('notice_type', $noticeType)
        ->where(function($query) use ($criteria, $evidenceType, $noticeType) {
            if (strpos($noticeType, 'website') !== false && $evidenceType == "website") {
                // For website notices, check if all three (url, domain, ip) match
                $query->where([
                    ['url', $criteria['url']],
                    ['domain', $criteria['domain']],
                    ['ip', $criteria['ip']]
                ]);
            } elseif (strpos($noticeType, 'social media') !== false) {
                // For social media notices, use OR condition
                if (isset($criteria['url'])) {
                    $query->orWhere('url', $criteria['url']);
                }
                if (isset($criteria['domain'])) {
                    $query->orWhere('domain', $criteria['domain']);
                }
                if (isset($criteria['ip'])) {
                    $query->orWhere('ip', $criteria['ip']);
                }
            } else {
                // For other types, use exact match on all available criteria
                foreach (['url', 'domain', 'ip'] as $field) {
                    if (isset($criteria[$field])) {
                        $query->where($field, $criteria[$field]);
                    }
                }
            }
        })->exists();
    // Save the notice content to MongoDB
    // Notice::updateOrCreate(
        // $criteria,

    if (!$existingNotice) {
        Notice::create(array_merge($criteria, [
            'content' => $combinedHtmlContent,
            // Add additional fields if needed
        ]));
        return response()->json(['success' => true, 'message' => 'Notice generated successfully']);
    } else {
        return response()->json(['success' => false, 'message' => 'Notice already generated'], 409);
    }
}
// dd();
// dd("sucess");

return redirect()->route('notices.index')->with('success', 'Notices generated and saved successfully.');
}



        // In your controller or service
        public function Notices(Request $request)
        {
            $currentUser = Auth::user();
            $currentUserId = $currentUser->id;
            $isSuperAdmin = $currentUser->role === 'Super Admin';
            $evidence=EvidenceType::get();

            // Get the filters from the request
            $accountNoFilter = $request->input('account_no');
            $ackNoFilter = $request->input('ack_no');
            $evidenceTypeFilter = $request->input('evidence_type');

            // Ensure filters are valid strings, default to null if empty
            $accountNoFilter = !empty($accountNoFilter) ? $accountNoFilter : null;
            $ackNoFilter = !empty($ackNoFilter) ? $ackNoFilter : null;
            $evidenceTypeFilter = !empty($evidenceTypeFilter) ? $evidenceTypeFilter : null;

            // Construct the query
            $query = Notice::where(function ($query) use ($currentUserId, $isSuperAdmin) {
                if (!$isSuperAdmin) {
                    $query->where(function ($query) use ($currentUserId) {
                        $query->where('assing_to_user_id', $currentUserId)
                              ->orWhereNull('assing_to_user_id');
                    });
                }
            });

            // Apply account_no filter if valid
            if ($accountNoFilter) {
                $query->where(function ($query) use ($accountNoFilter) {
                    $query->where('account_no', $accountNoFilter) // Exact match
                          ->orWhere(function ($query) use ($accountNoFilter) {
                              $query->where('account_no', 'like', "%$accountNoFilter%"); // Check within comma-separated list
                          });
                });
            }

            // Apply ack_no filter if valid
            if ($ackNoFilter) {
                $query->where(function ($query) use ($ackNoFilter) {
                    $query->where('ack_number', $ackNoFilter) // Exact match
                          ->orWhere(function ($query) use ($ackNoFilter) {
                              $query->where('ack_number', 'like', "%$ackNoFilter%"); // Check within comma-separated list
                          });
                });
            }

            if ($evidenceTypeFilter) {
                $query->where(function ($query) use ($evidenceTypeFilter) {
                    $query->where('evidence_type', $evidenceTypeFilter) // Exact match
                          ->orWhere(function ($query) use ($evidenceTypeFilter) {
                              $query->where('evidence_type', 'like', "%$evidenceTypeFilter%"); // Check within comma-separated list
                          });
                });
            }

            // Execute the query
            $notices = $query->orderBy('created_at', 'desc')->get();

            return view('notices.index', compact('notices','evidence'));
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
    $currentUserId = auth()->id(); // Get the current authenticated user's ID
    $users = User::where('_id', '!=', $currentUserId)->get();
    $user_sign= User::where('_id', $currentUserId)->get();
    $user = Auth::user();
    $role = $user->role;
    // dd($user_sign);
    return view('notices.edit', compact('notice','users','user_sign', 'role'));
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


    public function againstMuleAccount()
    {
        $banks = Bank::all(); // Get all banks with their IDs
        $wallets = Wallet::all(); // Get all wallets with their IDs
        $insurances = Insurance::all(); // Get all insurances with their IDs
        $merchants = Merchant::all(); // Get all merchants with their IDs

        $acknowledgementNos = Complaint::pluck('acknowledgement_no')->toArray();
        $documents = BankCasedata::whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->get();

        // Count occurrences of account_no_2 with different acknowledgment numbers
        $accountCounts = [];
        foreach ($documents as $doc) {
            preg_match('/(\d+)/', $doc->account_no_2, $matches);
            if (!empty($matches[1])) {
                $number = $matches[1];
                if (!isset($accountCounts[$number])) {
                    $accountCounts[$number] = [];
                }
                $accountCounts[$number][] = $doc->acknowledgement_no;
            }
        }

        // Filter account_no_2 that repeat more than twice with different acknowledgment numbers
        $frequentAccountNumbers = array_filter($accountCounts, function ($acknos) {
            return count(array_unique($acknos)) > 2;
        });

        $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);
        $layer1Cases = BankCasedata::where('Layer', 1)
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();

        $accountNumberPatterns = array_map(function ($number) {
            return new Regex("^$number\\b", ''); // Match the start of the string
        }, $frequentAccountNumbersKeys);

        $otherLayerCases = BankCasedata::where('Layer', '!=', 1)
            ->where(function ($query) use ($accountNumberPatterns) {
                foreach ($accountNumberPatterns as $pattern) {
                    $query->orWhere('account_no_2', 'regexp', $pattern);
                }
            })
            ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();

        $withdrawalCases = BankCasedata::where('Layer', '!=', 1)
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->whereIn('action_taken_by_bank', ['withdrawal through atm', 'cash withdrawal through cheque'])
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();

        // Filter and group cases
        $filterDuplicates = function ($cases) {
            return $cases->unique(function ($case) {
                return $case->acknowledgement_no . '-' . $case->account_no_2;
            });
        };

        $layer1Cases = $filterDuplicates($layer1Cases);
        $otherLayerCases = $filterDuplicates($otherLayerCases);
        $withdrawalCases = $filterDuplicates($withdrawalCases);

        $groupedOtherLayerCases = $otherLayerCases->groupBy(function ($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });

        $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
            return $group->pluck('acknowledgement_no')->unique()->count() >= 1;
        });

        $merge = $layer1Cases->merge($withdrawalCases);
        $allCases = $merge->merge($validOtherLayerCases->flatten(1));

        $groupedCases = $allCases->groupBy(function ($case) {
            return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        });

        $uniqueCases = $groupedCases->map(function ($group) {
            return $group->first();
        })->values();

        $filteredCases = $uniqueCases->map(function ($item) {
            $item->account_no_2 = preg_replace('/\[ Reported \d+ times \]/', '', $item->account_no_2);
            return $item;
        });
        $data_arr = [];
        foreach ($filteredCases as $key => $record) {
            $data_arr[] = [
                'bank' => $record->bank,        // All data is in the 'bank' field
            ];
        }
        $data_arr = collect($data_arr)->unique('bank')->values()->all();
        // Initialize matched data array
        $matchedData = [
            'bank' => [],
            'wallet' => [],
            'insurance' => [],
            'merchant' => [],
        ];

        // Check against models
        foreach ($data_arr as $item) {
            $bankValue = $item['bank'];

            if (!empty($bankValue)) {
                $matchedBank = $banks->firstWhere('bank', $bankValue); // Assuming 'name' field is used for matching
                if ($matchedBank) {
                    $matchedData['bank'][] = $matchedBank;
                }

                $matchedWallet = $wallets->firstWhere('wallet', $bankValue); // Assuming 'name' field is used for matching
                if ($matchedWallet) {
                    $matchedData['wallet'][] = $matchedWallet;
                }

                $matchedInsurance = $insurances->firstWhere('insurance', $bankValue); // Assuming 'name' field is used for matching
                if ($matchedInsurance) {
                    $matchedData['insurance'][] = $matchedInsurance;
                }

                $matchedMerchant = $merchants->firstWhere('merchant', $bankValue); // Assuming 'name' field is used for matching
                if ($matchedMerchant) {
                    $matchedData['merchant'][] = $matchedMerchant;
                }
            }
        }

        // Remove null values from matched data
        $matchedData = array_map(function ($items) {
            return array_filter($items);
        }, $matchedData);

        // dd($matchedData); // For debugging purposes

        return view('notice.muleaccount', compact('matchedData'));
    }

    public function generateMuleNotice(Request $request)
    {
    try {
        // dd("hi");
        $sourceType = $request->input('source_type');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $entityId = $request->input('entity_id');
        $entityType = $request->input('entity_type');

        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'entity_type' => 'required|in:bank,wallet,insurance,merchant',
            'entity_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        Log::info('Generate Mule Notice Request Data', [
            'source_type' => $sourceType,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'entity_id' => $entityId,
            'entity_type' => $entityType,
        ]);

        $fromDateStart = Carbon::parse($fromDate)->startOfDay();
        $toDateEnd = Carbon::parse($toDate)->endOfDay();

        switch ($entityType) {
            case 'bank':
                $entity = Bank::find($entityId);
                break;
            case 'wallet':
                $entity = Wallet::find($entityId);
                break;
            case 'insurance':
                $entity = Insurance::find($entityId);
                break;
            case 'merchant':
                $entity = Merchant::find($entityId);
                break;
            default:
                return response()->json(['success' => false, 'message' => "Entity not found for type: $entityType"], 400);
        }
        if (!$entity) {
            return response()->json(['success' => false, 'message' => "Entity not found for type: $entityType"], 400);
        }

        Log::info('Entity Details', ['entity' => $entity]);

        $acknowledgementNos = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
                                        ->pluck('acknowledgement_no')->toArray();
            // dd($acknowledgementNos);
        $documents = BankCasedata::whereNotNull('account_no_2')
        ->where('account_no_2', '!=', '')
        ->get();
        // dd($documents);

        // Count occurrences of account_no_2 with different acknowledgment numbers
        $accountCounts = [];
        foreach ($documents as $doc) {
        preg_match('/(\d+)/', $doc->account_no_2, $matches);
        if (!empty($matches[1])) {
        $number = $matches[1];
        if (!isset($accountCounts[$number])) {
        $accountCounts[$number] = [];
        }
        $accountCounts[$number][] = $doc->acknowledgement_no;
        }
        }
        // dd($accountCounts);

        // Filter account_no_2 that repeat more than twice with different acknowledgment numbers
        $frequentAccountNumbers = array_filter($accountCounts, function($acknos) {
        return count(array_unique($acknos)) > 2;
        });
        // dd($frequentAccountNumbers);

        $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);
        // dd($frequentAccountNumbersKeys);

        $frequentAccountNumbers = array_filter($frequentAccountNumbersKeys, function($count) {
            return $count > 2;
        });

        // dd($frequentAccountNumbers);

        $layer1Cases = BankCasedata::where('Layer', 1)
            ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();
            // dd($layer1Cases);

        $layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();
        // dd($layer1AcknowledgementNos);

                $accountNumberPatterns = array_map(function($number) {
            return new Regex("^$number\\b", ''); // Match the start of the string
        }, $frequentAccountNumbersKeys);

        // dd($accountNumberPatterns);

        $otherLayerCases = BankCasedata::where('Layer', '!=', 1)
        ->whereNotNull('account_no_2')
        ->where('account_no_2', '!=', '')
        ->where(function($query) use ($accountNumberPatterns) {
                foreach ($accountNumberPatterns as $pattern) {
                    $query->orWhere('account_no_2', 'regexp', $pattern);
                    }
                })
            ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();

        // dd($otherLayerCases);
        $withdrawalCases = BankCasedata::where('Layer','!=', 1)
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->whereIn('action_taken_by_bank', ['withdrawal through atm', 'cash withdrawal through cheque'])
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->get();


        // Function to filter duplicates based on acknowledgment number and account number
        // Apply entity filter
            $entityBank = $entity->bank ?? $entity->wallet ?? $entity->insurance ?? $entity->merchant;

            // Filter Layer 1 cases
            $layer1Cases = $layer1Cases->filter(function ($case) use ($entityBank) {
                return $case->bank === $entityBank;
            });

            // Filter other layer cases
            $otherLayerCases = $otherLayerCases->filter(function ($case) use ($entityBank) {
                return $case->bank === $entityBank;
            });


            $withdrawalCases = $withdrawalCases->filter(function ($case) use ($entityBank) {
                return $case->bank === $entityBank;
            });

            // Remove duplicates based on account_no_2 and acknowledgment_no
            $filterDuplicates = function ($cases) {
                return $cases->unique(function ($case) {
                    return $case->acknowledgement_no . '-' . $case->account_no_2;
                });
            };
                        // dd($filterDuplicates);

            $layer1Cases = $filterDuplicates($layer1Cases);
            $otherLayerCases = $filterDuplicates($otherLayerCases);
            $withdrawalCases = $filterDuplicates($withdrawalCases);
                        // dd($layer1Cases);

            // Group other layer cases by account_no_2
            $groupedLayerOneCases = $layer1Cases->groupBy(function ($case) {
                return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
            });
                        // dd($groupedOtherLayerCases);

            // Filter valid other layer cases
            $validLayerOneCases = $groupedLayerOneCases->filter(function ($group) {
                return $group->pluck('acknowledgement_no')->unique()->count() >=1;
            });
            // dd($validOtherLayerCases);

            $groupedOtherLayerCases = $otherLayerCases->groupBy(function ($case) {
                return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
            });
                        // dd($groupedOtherLayerCases);

            // Filter valid other layer cases
            $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
                return $group->pluck('acknowledgement_no')->unique()->count() >=1;
            });


            // Merge Layer 1 and valid other layer cases
            $merge=$layer1Cases->merge($withdrawalCases);
            $allCases = $merge->merge($validOtherLayerCases->flatten(1));
            // dd($allCases);

            // Group by account_no_2 and remove duplicates
            $groupedCases = $allCases->groupBy(function ($case) {
                return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
            });
            // dd($groupedCases);

            // Ensure each group is unique by account_no_2
            $uniqueCases = $groupedCases->map(function ($group) {
                return $group->first();
            });
            // dd($uniqueCases);


            // Flatten the cases for notice data
            $flattenedCases = $groupedCases->flatMap(function ($group) {
                return $group->map(function ($case) {
                    return [
                        // Clean account_no_2 by removing any text in square brackets
                        'account_no_2' => preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2)),
                        'acknowledgement_no' => $case->acknowledgement_no,
                        'bank' => $case->bank,
                        'Layer' => $case->Layer,
                        'date' => now()->format('Y-m-d'),
                        'action_taken_by_bank' => $case->action_taken_by_bank
                    ];
                });
            });

            // dd($flattenedCases);

            Log::info('Flattened Cases', ['flattenedCases' => $flattenedCases]);

            if ($flattenedCases->isEmpty()) {
                return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
            }

            $anotice = Notice::get();

            // Extract account numbers from the notices
            $accountNos = $anotice->pluck('account_no')->flatten()->unique()->toArray();

            // Split each comma-separated string into individual account numbers
            $individualAccountNos = collect($accountNos)->flatMap(function($item) {
                return explode(',', $item); // Split by comma
            })->map(function($item) {
                return trim($item); // Trim any whitespace
            })->unique()->toArray(); // Remove duplicates

            // Log the extracted individual account numbers
            Log::info('Individual Account Numbers', ['individualAccountNos' => $individualAccountNos]);

            // Filter flattened cases where cleaned account_no_2 is not in the individual account numbers
            $repeatNotice = $flattenedCases->whereNotIn('account_no_2', $individualAccountNos);

            // dd($repeatNotice);

            // Log the filtered repeat notices
            Log::info('Repeat Notices', ['repeatNotice' => $repeatNotice]);

            if ($repeatNotice->isEmpty()) {
                Log::warning('No cases found where account_no_2 does not match any account number from notices.');
            }


            // Step 2: Get all unique acknowledgement numbers from $repeatNotice
            $acknowledgementNos = $repeatNotice->pluck('acknowledgement_no')->unique();

            // Step 3: Retrieve complaints that have matching acknowledgement numbers
            $complaints = Complaint::whereIn('acknowledgement_no', $acknowledgementNos)->get();

            // Step 4: Filter the $repeatNotice further to ensure account_no_2 is not the same as account_id in the Complaint model
            $filteredRepeatNotice = $repeatNotice->filter(function ($notice) use ($complaints) {
                // Find complaints with the same acknowledgement_no
                $matchingComplaints = $complaints->where('acknowledgement_no', $notice['acknowledgement_no']);

                // Check if account_no_2 is not in the list of account_id from the matching complaints
                foreach ($matchingComplaints as $complaint) {
                    // Ensure both values are of the same type
                    if ((string) $complaint->account_id === (string) $notice['account_no_2']) {
                        return false; // Exclude this notice as it matches a complaint's account_id
                    }
                }

                return true; // Include this notice as no matching complaint's account_id was found
            });

            // Optional: Reindex the collection if necessary
            $filteredRepeatNotice = $filteredRepeatNotice->values();

            // dd($filteredRepeatNotice);
            if ($filteredRepeatNotice->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Notice has already been generated for this account.'], 400);
            }

            Log::info('Filtered Repeat Notices', ['filteredRepeatNotice' => $filteredRepeatNotice]);


            // Map the flattened cases to the notice data format
            $noticeData = $filteredRepeatNotice->map(function ($case) {
                return [
                    'account_no_2' => preg_replace('/\[\s*Reported\s*\d+\s*times\s*\]/', '', trim($case['account_no_2'])),
                    'acknowledgement_no' => $case['acknowledgement_no'],
                    'bank' => $case['bank'],
                    'state' => 'kerala',
                    'Layer' => $case['Layer'],
                    'date' => $case['date'],
                    'action_taken_by_bank' => $case['action_taken_by_bank']
                ];
            })->toArray();

            // Reindex the array to ensure sequential numeric keys starting from 0
            $noticeData = array_values($noticeData);

            Log::info('Notice Data', ['noticeData' => $noticeData]);

            if (empty($noticeData)) {
                return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
            }

            $htmlContent = View::make('notices.muleaccount', ['notice' => $noticeData])->render();
            // dd($htmlContent);

            // Notice::create([
            //     'user_id' => Auth::user()->id,
            //     'ack_number' => $noticeData[0]['acknowledgement_no'],
            //     'notice_type' => 'NOTICE U/s 168 of BHARATIYA NAGARIK SURAKSHA SANHITA (BNSS)-2023',
            //     'type' => 'Mule',
            //     'content' => $htmlContent,
            //     'type' => 'Mule',
            // ]);

            // Extract and process ack_number, ensuring it's a comma-separated string
            $ack_numbers = [];
            $account_nos = [];

            // Loop through the notice data to collect ack_number and account_no_2
            foreach ($noticeData as $data) {
                // Trim whitespace and add to arrays
                $ack_numbers[] = $data['acknowledgement_no'];
                $account_nos[] = trim($data['account_no_2']);
            }


            // Convert arrays to comma-separated strings
            $ack_number = implode(',', $ack_numbers);
            $account_no = implode(',', $account_nos);
            // dd($account_no);

                $notice = new Notice();
                $notice->ack_number = $ack_number;
                $notice->account_no = $account_no;
                $notice->user_id = Auth::user()->id;
                $notice->notice_type = 'NOTICE U/s 168 of BHARATIYA NAGARIK SURAKSHA SANHITA (BNSS)-2023';
                $notice->type = 'Mule';
                $notice->content = $htmlContent;
                $notice->bank = $noticeData[0]['bank'];

                $notice->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error generating notice: ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => 'An error occurred while generating the notice.'], 500);
        }
    }

    public function againstBankAccount()
    {
        $bank = Bank::get();
        $wallet= Wallet::get();
        $insurance=Insurance::get();
        $merchant=Merchant::get();
        return view('notice.bank',compact('bank','wallet','insurance','merchant'));
    }

        public function generateBankAccNotice(Request $request)
    {
        try {
            // Extract input values
            $sourceType = $request->input('source_type');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $entityId = $request->input('entity_id');
            $entityType = $request->input('entity_type');
            $ackNo = $request->input('acknowledgement_no');

            // Custom validation rule to check that either entity_id or acknowledgement_no is required
            $validator = Validator::make($request->all(), [
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'entity_type' => 'required|in:bank,wallet,insurance,merchant',
                'entity_id' => 'required_without:acknowledgement_no',
                'acknowledgement_no' => 'required_without:entity_id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            // Log request data
            Log::info('Generate Bank Acc Notice Request Data', [
                'source_type' => $sourceType,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'acknowledgement_no' => $ackNo,
            ]);

            $fromDateStart = Carbon::parse($fromDate)->startOfDay();
            $toDateEnd = Carbon::parse($toDate)->endOfDay();

            // Fetch entity if entity_id is provided
            $entity = null;
            if ($entityId) {
                switch ($entityType) {
                    case 'bank':
                        $entity = Bank::find($entityId);
                        break;
                    case 'wallet':
                        $entity = Wallet::find($entityId);
                        break;
                    case 'insurance':
                        $entity = Insurance::find($entityId);
                        break;
                    case 'merchant':
                        $entity = Merchant::find($entityId);
                        break;
                }

                if (!$entity) {
                    return response()->json(['success' => false, 'message' => "Entity not found for type: $entityType"], 400);
                }
            }

            Log::info('Entity Details', ['entity' => $entity]);

            // Fetch acknowledgements based on either entity_id or ack_no
            $acknowledgementNos = [];
            if ($ackNo) {
                $acknowledgementNos = Complaint::where('acknowledgement_no', $ackNo)
                    ->whereBetween('entry_date', [$fromDateStart, $toDateEnd])
                    ->pluck('acknowledgement_no')
                    ->toArray();
            } else {
                $acknowledgementNos = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
                    ->pluck('acknowledgement_no')
                    ->toArray();
            }

            // Fetch documents and extract account numbers
            $documents = BankCasedata::where('account_no_2', '!=', null)->get();
            $accountNumbers = $documents->pluck('account_no_2')->map(function ($accountNo) {
                return preg_replace('/\s*\[.*\]$/', '', trim($accountNo)); // Clean account number
            })->unique()->toArray();

            $accountNumberPatterns = array_map(function($number) {
                return '/'.preg_quote($number, '/').'$/'; // Correct escaping for MongoDB regex
            }, $accountNumbers);

            Log::info('Account Number Patterns', ['patterns' => $accountNumberPatterns]);

            // Fetch filtered cases
            $otherLayerCases = BankCasedata::where(function($query) use ($entity) {
                if ($entity) {
                    $query->where('bank', $entity->bank ?? $entity->wallet ?? $entity->insurance ?? $entity->merchant);
                }
            })
            ->whereIn('acknowledgement_no', $acknowledgementNos)
            ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
            ->where(function($query) use ($accountNumberPatterns) {
                foreach ($accountNumberPatterns as $pattern) {
                    $query->orWhere('account_no_2', 'regexp', $pattern); // Use regexp for MongoDB
                }
            })
            ->whereNotNull('account_no_2')
            ->where('account_no_2', '!=', '')
            ->get();

            Log::info('Other Layer Cases', ['cases' => $otherLayerCases]);

            // Function to filter duplicates based on acknowledgment number and account number
            $filterDuplicates = function($cases) {
                return $cases->unique(function($case) {
                    return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2)) . '-' . $case->acknowledgement_no;
                });
            };

            // Filter duplicates
            $otherLayerCases = $filterDuplicates($otherLayerCases);

            // Group cases by account number
            $groupedOtherLayerCases = $otherLayerCases->groupBy(function($case) {
                return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
            });

            Log::info('Grouped Other Layer Cases', ['groupedCases' => $groupedOtherLayerCases]);

            // Filter the grouped cases to ensure valid cases have unique acknowledgment numbers greater than one
            $allCases = $groupedOtherLayerCases->filter(function ($group) {
                return $group->pluck('acknowledgement_no')->unique()->count();
            });

            Log::info('Filtered Cases', ['allCases' => $allCases]);

            // Ensure $allCases is a collection
            if (! $allCases instanceof \Illuminate\Support\Collection) {
                $allCases = collect($allCases);
            }

            // Debug $allCases
            Log::info('Filtered Cases Account Numbers', ['accountNumbers' => $allCases->flatMap(function ($cases) {
                return $cases->pluck('account_no_2');
            })->toArray()]);

            // Group by account number again
            $groupedCases = $allCases->flatMap(function ($cases) {
                return $cases->groupBy(function($case) {
                    return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
                });
            });

            Log::info('Grouped Cases', ['groupedCases' => $groupedCases]);

            // Flatten cases for notice data
            $flattenedCases = $groupedCases->flatMap(function ($group) {
                return $group->map(function ($case) {
                    return [
                        'account_no_2' => preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2)),
                        'acknowledgement_no' => $case->acknowledgement_no,
                        'bank' => $case->bank,
                        'Layer' => $case->Layer,
                        'date' => now()->format('Y-m-d'),
                        'action_taken_by_bank' => $case->action_taken_by_bank
                    ];
                });
            });

            Log::info('Flattened Cases', ['flattenedCases' => $flattenedCases]);

            if ($flattenedCases->isEmpty()) {
                return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
            }

            $anotice = Notice::get();

            // Extract account numbers from the notices
            $accountNos = $anotice->pluck('account_no')->flatten()->unique()->toArray();

            // Split each comma-separated string into individual account numbers
            $individualAccountNos = collect($accountNos)->flatMap(function($item) {
                return explode(',', $item); // Split by comma
            })->map(function($item) {
                return trim($item); // Trim any whitespace
            })->unique()->toArray(); // Remove duplicates

            // Log the extracted individual account numbers
            Log::info('Individual Account Numbers', ['individualAccountNos' => $individualAccountNos]);

            // Filter flattened cases where cleaned account_no_2 is not in the individual account numbers
            $repeatNotice = $flattenedCases->whereNotIn('account_no_2', $individualAccountNos);

            // dd($repeatNotice);

            // Log the filtered repeat notices
            Log::info('Repeat Notices', ['repeatNotice' => $repeatNotice]);

            if ($repeatNotice->isEmpty()) {
                Log::warning('No cases found where account_no_2 does not match any account number from notices.');
            }

            if ($repeatNotice->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Notice has already been generated for this account.'], 400);
            }

            $noticeData = $repeatNotice->map(function ($case) {
                return [
                    'account_no_2' => preg_replace('/\[\s*Reported\s*\d+\s*times\s*\]/', '', trim($case['account_no_2'])),
                    'acknowledgement_no' => $case['acknowledgement_no'],
                    'bank' => $case['bank'],
                    'state' => 'kerala',
                    'Layer' => $case['Layer'],
                    'date' => $case['date'],
                    'action_taken_by_bank' => $case['action_taken_by_bank'],

                ];
            })->toArray();

            $noticeData = array_values($noticeData);

            Log::info('Notice Data', ['noticeData' => $noticeData]);

            if (empty($noticeData)) {
                return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
            }
                // dd($noticeData);
            $htmlContent = View::make('notices.againstBank', ['notice' => $noticeData, 'to_date'=>$toDateEnd , 'from_date'=>$fromDateStart ])->render();

                // Notice::create([
                //     'user_id' => Auth::user()->id,
                //     'ack_number' => $noticeData[0]['acknowledgement_no'],
                //     'notice_type' => 'Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)',
                //     'type'=>'Bank',
                //     'content' => $htmlContent,
                //     'type' => 'Bank'
                // ]);

            $ack_numbers = [];
            $account_nos = [];

            // Loop through the notice data to collect ack_number and account_no_2
            foreach ($noticeData as $data) {
                // Trim whitespace and add to arrays
                $ack_numbers[] = $data['acknowledgement_no'];
                $account_nos[] = trim($data['account_no_2']);
            }

            // Convert arrays to comma-separated strings
            $ack_number = implode(',', $ack_numbers);
            $account_no = implode(',', $account_nos);

            // Save the data to the database
            // Assuming a model named Notice or similar
            $notice = new Notice();
            $notice->ack_number = $ack_number;
            $notice->account_no = $account_no;
            $notice->user_id = Auth::user()->id;
            $notice->notice_type = 'Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)';
            $notice->type='Bank';
            $notice->content = $htmlContent;
            $notice->bank= $noticeData[0]['bank'];

            $notice->save();

            return response()->json(['success' => true, 'message' => 'Notice generated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error generating Bank Acc Notice', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'No valid case data found to generate notices.'], 500);
        }
    }

        // public function generateBankAckNotice(Request $request)
        // {
        // try {
        //     // dd("hi");
        //     $sourceType = $request->input('source_type');
        //     $fromDate = $request->input('from_date');
        //     $toDate = $request->input('to_date');
        //     $entityId = $request->input('entity_id');
        //     $entityType = $request->input('entity_type');
        //     $ackNo = $request->input('acknowledgement_no');


        //     $validator = Validator::make($request->all(), [
        //         'from_date' => 'nullable|date|before_or_equal:to_date', // Nullable, but should be a valid date if provided
        //         'to_date' => 'nullable|date|after_or_equal:from_date',  // Nullable, but should be a valid date if provided
        //         'acknowledgement_no' => 'nullable|string', // Nullable and a string if provided
        //         'entity_type' => 'required|in:bank,wallet,insurance,merchant', // Required
        //         'entity_id' => 'required', // Required
        //     ]);

        //     // Custom rule to ensure at least one of the three fields is filled
        //     $validator->after(function ($validator) use ($request) {
        //         if (!$request->filled('from_date') && !$request->filled('to_date') && !$request->filled('acknowledgement_no')) {
        //             $validator->errors()->add('from_date', 'At least one of from_date, to_date, or acknowledgement_no is required.');
        //         }
        //     });

        //     if ($validator->fails()) {
        //         return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        //     }
        //     Log::info('Generate Mule Notice Request Data', [
        //         'source_type' => $sourceType,
        //         'from_date' => $fromDate,
        //         'to_date' => $toDate,
        //         'entity_id' => $entityId,
        //         'entity_type' => $entityType,
        //     ]);

        //     $fromDateStart = Carbon::parse($fromDate)->startOfDay();
        //     $toDateEnd = Carbon::parse($toDate)->endOfDay();

        //     switch ($entityType) {
        //         case 'bank':
        //             $entity = Bank::find($entityId);
        //             break;
        //         case 'wallet':
        //             $entity = Wallet::find($entityId);
        //             break;
        //         case 'insurance':
        //             $entity = Insurance::find($entityId);
        //             break;
        //         case 'merchant':
        //             $entity = Merchant::find($entityId);
        //             break;
        //         default:
        //             return response()->json(['success' => false, 'message' => "Entity not found for type: $entityType"], 400);
        //     }
        //     if (!$entity) {
        //         return response()->json(['success' => false, 'message' => "Entity not found for type: $entityType"], 400);
        //     }

        //     Log::info('Entity Details', ['entity' => $entity]);

        //     // $acknowledgementNos = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
        //     //                                 ->pluck('acknowledgement_no')->toArray();

        //     $acknowledgementNos = [];
        //             if ($ackNo) {
        //                 $acknowledgementNos = Complaint::where('acknowledgement_no', $ackNo)
        //                     ->whereBetween('entry_date', [$fromDateStart, $toDateEnd])
        //                     ->pluck('acknowledgement_no')
        //                     ->toArray();
        //             } else {
        //                 $acknowledgementNos = Complaint::whereBetween('entry_date', [$fromDateStart, $toDateEnd])
        //                     ->pluck('acknowledgement_no')
        //                     ->toArray();
        //             }
        //         // dd($acknowledgementNos);
        //     $documents = BankCasedata::where('account_no_2', '!=', null)->get()->toArray();

        //         // Process documents in PHP
        //     $accountNumbers = [];
        //     foreach ($documents as $doc) {
        //             if (isset($doc['account_no_2'])) {
        //                 // Extract numeric part
        //                 preg_match('/(\d+)/', $doc['account_no_2'], $matches);
        //                 if (!empty($matches[1])) {
        //                     $number = $matches[1];
        //                     if (!isset($accountNumbers[$number])) {
        //                         $accountNumbers[$number] = 0;
        //                     }
        //                     $accountNumbers[$number]++;
        //                 }
        //             }
        //         }

        //         // Filter account numbers that repeat more than twice
        //         $frequentAccountNumbers = array_filter($accountNumbers, function($count) {
        //             return $count > 2;
        //         });
        //         // dd($documents);
        //         // Get the keys (account numbers) that have more than two occurrences
        //         $frequentAccountNumbersKeys = array_keys($frequentAccountNumbers);
        //         $layer1Cases = BankCasedata::where(function($query) use ($entity) {
        //             $query->where('bank', $entity->bank ?? $entity->wallet ?? $entity->insurance ?? $entity->merchant);
        //         })
        //         ->whereIn('acknowledgement_no', $acknowledgementNos)
        //         ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
        //         ->where('Layer', 1)
        //         ->whereNotNull('account_no_2')
        //         ->where('account_no_2', '!=', '')
        //         ->get();
        //     // dd($layer1Cases);


        //     $layer1AcknowledgementNos = $layer1Cases->pluck('acknowledgement_no')->toArray();

        //     // Prepare a list of regular expressions to match account numbers
        //     $accountNumberPatterns = array_map(function($number) {
        //         return new Regex("^$number\\b", ''); // Match the start of the string
        //     }, $frequentAccountNumbersKeys);


        //     $otherLayerCases = BankCasedata::where(function($query) use ($entity) {
        //         $query->where('bank', $entity->bank ?? $entity->wallet ?? $entity->insurance ?? $entity->merchant);
        //     })
        //     ->whereNotIn('acknowledgement_no', $layer1AcknowledgementNos) // Exclude those from layer1
        //     ->whereIn('acknowledgement_no', $acknowledgementNos) // Consider these acknowledgement numbers
        //     ->whereNotIn('action_taken_by_bank', ['other', 'wrong transaction'])
        //     ->where('Layer', '!=', 1)
        //     ->where(function($query) use ($accountNumberPatterns) {
        //         foreach ($accountNumberPatterns as $pattern) {
        //             $query->orWhere('account_no_2', 'regexp', $pattern);
        //         }
        //     })
        //     ->whereNotNull('account_no_2')
        //     ->where('account_no_2', '!=', '')
        //     ->get();
        //     // dd($otherLayerCases);

        //     // Function to filter duplicates based on acknowledgment number and account number
        //     $filterDuplicates = function($cases) {
        //         return $cases->unique(function($case) {
        //             return $case->acknowledgement_no . '-' . $case->account_no_2;
        //         });
        //     };

        //     // Filter duplicates from both sets of cases
        //     $layer1Cases = $filterDuplicates($layer1Cases);
        //     $otherLayerCases = $filterDuplicates($otherLayerCases);
        //     // dd($otherLayerCases);

        //     // Group otherLayerCases by account number without extra information
        //     $groupedOtherLayerCases = $otherLayerCases->groupBy(function($case) {
        //         return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        //     });
        //     // dd($groupedOtherLayerCases);

        //     // Filter the grouped cases to ensure valid cases have unique acknowledgment numbers greater than one
        //     $validOtherLayerCases = $groupedOtherLayerCases->filter(function ($group) {
        //         return $group->pluck('acknowledgement_no')->unique()->count() > 1;
        //     });
        //     // dd($validOtherLayerCases);

        //     // Merge layer1Cases and validOtherLayerCases
        //     $allCases = $layer1Cases->merge($validOtherLayerCases->flatten(1));

        //     // Debugging output
        //     // dd($allCases);

        //         // Group cases by trimmed account_no_2
        //         $groupedCases = $allCases->groupBy(function($case) {
        //             return preg_replace('/\s*\[.*\]$/', '', trim($case->account_no_2));
        //         });
        //         // dd($groupedCases);

        //         // Filter out duplicate acknowledgement_no values within each group
        //         $groupedCases->transform(function ($group) {
        //             // Ensure each group has unique acknowledgement_no values
        //             return $group->unique('acknowledgement_no');
        //         });
        //         // dd($validCases);

        //         // Flatten the cases for notice data
        //         $flattenedCases = $groupedCases->flatMap(function ($group) {
        //             return $group->map(function ($case) {
        //                 return [
        //                     'account_no_2' => $case->account_no_2,
        //                     'acknowledgement_no' => $case->acknowledgement_no,
        //                     'bank' => $case->bank,
        //                     'Layer' => $case->Layer,
        //                     'date' => now()->format('Y-m-d'),
        //                     'action_taken_by_bank' => $case->action_taken_by_bank
        //                 ];
        //             });
        //         });
        //         // dd($flattenedCases);

        //         Log::info('Flattened Cases', ['flattenedCases' => $flattenedCases]);

        //         if ($flattenedCases->isEmpty()) {
        //             return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
        //         }

        //         // Map the flattened cases to the notice data format
        //         $noticeData = $flattenedCases->map(function ($case) {
        //             return [
        //                 'account_no_2' => preg_replace('/\[\s*Reported\s*\d+\s*times\s*\]/', '', trim($case['account_no_2'])),
        //                 'acknowledgement_no' => $case['acknowledgement_no'],
        //                 'bank' => $case['bank'],
        //                 'state' => 'kerala',
        //                 'Layer' => $case['Layer'],
        //                 'date' => $case['date'],
        //                 'action_taken_by_bank' => $case['action_taken_by_bank']
        //             ];
        //         })->toArray();

        //         Log::info('Notice Data', ['noticeData' => $noticeData]);

        //         if (empty($noticeData)) {
        //             return response()->json(['success' => false, 'message' => "No valid case data found to generate notices."], 400);
        //         }

        //         $htmlContent = View::make('notices.againstBankImmediate', ['notice' => $noticeData])->render();

        //         Notice::create([
        //             'user_id' => Auth::user()->id,
        //             'ack_number' => $noticeData[0]['acknowledgement_no'],
        //             'notice_type' => ' Notice for immediate intervention to prevent cyber fraud ',
        //             'type'=>'Bank',
        //             'content' => $htmlContent,
        //             'type' => 'Bank',

        //         ]);

        //         return response()->json(['success' => true]);
        //     } catch (\Exception $e) {
        //         Log::error('Error generating notice: ' . $e->getMessage());

        //         return response()->json(['success' => false, 'error' => 'An error occurred while generating the notice.'], 500);
        //     }
        // }



    public function approve(Request $request, $id)
    {
        // Find the notice by ID
        $notice = Notice::find($id);

        if (!$notice) {
            return response()->json(['success' => false, 'message' => 'Notice not found.']);
        }

        // Check if already approved
        if ($notice->approved) {
            return response()->json(['success' => false, 'message' => 'Already approved.']);
        }

        // Update the content and approval information
        $notice->content = $request->input('content');
        // $notice->approved = true; // Mark as approved
        $notice->approve_id = auth()->id(); // Track the user who approved
        $notice->save(); // Save the changes

        return response()->json(['success' => true]);
    }

}
