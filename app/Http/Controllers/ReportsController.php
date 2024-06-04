<?php

namespace App\Http\Controllers;
use App\Models\SourceType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Complaint;
use App\Models\BankCasedata;
use App\Models\ComplaintOthers;
use App\Models\EvidenceType;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Crypt;
use MongoDB\Client;
use DateTime;

class ReportsController extends Controller
{
    public function index()
    {

        $evidenceTypes = EvidenceType::where('status', 'active')
                             ->whereNull('deleted_at')
                             ->get();
        return view("dashboard.reports.index", compact('evidenceTypes'));
    }

    public function getDatalistNcrp(Request $request)
    {
        // Get request parameters
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page.

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index.
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name.
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc.
        $searchValue = $search_arr['value']; // Search value.

        // Custom filters
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $current_date = $request->get('current_date');
        // dd($current_date);
        $bank_action_status = $request->get('bank_action_status');
        $normalizedBankActionStatus = strtolower(trim($bank_action_status));
        $normalizedBankActionStatus = preg_replace('/\s+/', '', $normalizedBankActionStatus);


                // Total records.
                $totalRecordQuery = Complaint::where('deleted_at', null);
                $totalRecord = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->orderBy('created_at', 'desc')->orderBy($columnName, $columnSortOrder);

                //$totalRecords = $totalRecord->select('count(*) as allcount')->count();
                $totalRecords = Complaint::groupBy('acknowledgement_no')->get()->count();

                $totalRecordswithFilte = Complaint::groupBy('acknowledgement_no')->where('deleted_at', null)->orderBy('created_at', 'desc');
                //$totalRecordswithFilter = $totalRecordswithFilte->select('count(*) as allcount')->count();
                $totalRecordswithFilter = Complaint::groupBy('acknowledgement_no')->get()->count();



                 //dd($com_status);
                 $items = Complaint::groupBy('acknowledgement_no')
                 ->where('deleted_at', null)
                 ->orderBy('_id', 'desc')
                 ->orderBy($columnName, $columnSortOrder);

                 $totalRecords  = Complaint::groupBy('acknowledgement_no')
                 ->where('deleted_at', null)
                 ->orderBy('created_at', 'desc')
                 ->orderBy($columnName, $columnSortOrder)->get()->count();
                 $totalRecordswithFilter = $totalRecords;

        // // Initial query to get total records
        // $totalRecordQuery = Complaint::where('deleted_at', null);

        // Build the query with the custom filters and search
        $query = Complaint::where('deleted_at', null);

        // Apply filter conditions
        if ($fromDate && $toDate) {
            // Parse and format dates using Carbon
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $fromDate . ' 00:00:00')->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d H:i:s', $toDate . ' 23:59:59')->endOfDay();

            // Convert Carbon objects to UTCDateTime
            $fromUTC = new UTCDateTime($from->getTimestamp() * 1000);
            $toUTC = new UTCDateTime($to->getTimestamp() * 1000);

          // Filter records based on the formatted dates
          $totalRecordQuery->whereBetween('entry_date', [$fromUTC, $toUTC]);
          $items->whereBetween('entry_date', [$fromUTC, $toUTC]);
          $totalRecords = Complaint::groupBy('acknowledgement_no')
                          ->whereBetween('entry_date', [$fromUTC, $toUTC])->get()->count();
          $totalRecordswithFilter = $totalRecords;
        }

        // New filter condition for current_date = "today"
if ($current_date === 'today') {
    $todayStart = Carbon::now()->startOfDay();
    $todayEnd = Carbon::now()->endOfDay();

    $todayStartUTC = new UTCDateTime($todayStart->getTimestamp() * 1000);
    $todayEndUTC = new UTCDateTime($todayEnd->getTimestamp() * 1000);

    $totalRecordQuery->whereBetween('entry_date', [$todayStartUTC, $todayEndUTC]);
    $items->whereBetween('entry_date', [$todayStartUTC, $todayEndUTC]);
    $totalRecords = Complaint::groupBy('acknowledgement_no')
                    ->whereBetween('entry_date', [$todayStartUTC, $todayEndUTC])->get()->count();
    $totalRecordswithFilter = $totalRecords;
}

       // Filter based on bank_action_status
if ($normalizedBankActionStatus) {

    $acknowledgementNos = BankCasedata::whereRaw([
        '$expr' => [
            '$regexMatch' => [
                'input' => ['$replaceAll' => ['input' => ['$toLower' => '$action_taken_by_bank'], 'find' => ' ', 'replacement' => '']],
                'regex' => $normalizedBankActionStatus,
                'options' => 'i'
            ]
        ]
    ])->pluck('acknowledgement_no');
    // Filter the Complaint collection using the retrieved acknowledgement_no
    $totalRecordQuery->whereIn('acknowledgement_no', $acknowledgementNos);
    $items->whereIn('acknowledgement_no', $acknowledgementNos);
    $totalRecords = Complaint::groupBy('acknowledgement_no')
                    ->whereIn('acknowledgement_no', $acknowledgementNos)->get()->count();
    $totalRecordswithFilter = $totalRecords;
}

// dd($acknowledgementNos);




        // Apply search filter
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('acknowledgement_no', 'like', '%' . $searchValue . '%')
                  ->orWhere('district', 'like', '%' . $searchValue . '%')
                  ->orWhere('complainant_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('transaction_id', 'like', '%' . $searchValue . '%')
                  ->orWhere('bank_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('account_id', 'like', '%' . $searchValue . '%')
                  ->orWhere('amount', 'like', '%' . $searchValue . '%')
                  ->orWhere('current_status', 'like', '%' . $searchValue . '%')
                  ->orWhere('date_of_action', 'like', '%' . $searchValue . '%')
                  ->orWhere('action_taken_by_name', 'like', '%' . $searchValue . '%');


            $totalRecords = Complaint::groupBy('acknowledgement_no')->where('acknowledgement_no', 'like', '%' . $searchValue . '%')->orWhere('district', 'like', '%' . $searchValue . '%')->orWhere('complainant_name', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->orWhere('police_station', 'like', '%' . $searchValue . '%')->orWhere('bank_name', 'like', '%' . $searchValue . '%')->where('deleted_at', null)->get()->count();

            $totalRecordswithFilter = $totalRecords;
            });
        }

        $records = $items->skip($start)->take($rowperpage)->get();

        $data_arr = array();
        $i = $start;

        foreach ($records as $record){
            $com = Complaint::where('acknowledgement_no',$record->acknowledgement_no)->take(10)->get();
            $i++;
            $id = $record->id;
            $source_type = $record->source_type;
            $acknowledgement_no = $record->acknowledgement_no;

            $transaction_id="";$amount="";$bank_name="";
            foreach($com as $com){
                $transaction_id .= $com->transaction_id."<br>";
                $amount .= '<span class="editable" data-ackno="'.$record->acknowledgement_no.'" data-transaction="'.$com->transaction_id.'" >'.$com->amount."</span><br>";
                $bank_name .= $com->bank_name."<br>";
                $complainant_name = $com->complainant_name;
                $complainant_mobile = $com->complainant_mobile;

                $district = $com->district;
                $police_station = $com->police_station;
                $account_id = $com->account_id;
                $entry_date = Carbon::parse($com->entry_date)->format('Y-m-d H:i:s');
                $current_status = $com->current_status;
                $date_of_action = $com->date_of_action;
                $action_taken_by_name = $com->action_taken_by_name;
                $action_taken_by_designation = $com->action_taken_by_designation;
                $action_taken_by_mobile = $com->action_taken_by_mobile;
                $action_taken_by_email = $com->action_taken_by_email;
                $action_taken_by_bank = $com->action_taken_by_bank;
            }

            $data_arr[] = array(
                "id" => $i,
                "acknowledgement_no" => $acknowledgement_no,
                "district" => $district."<br>".$police_station,
                "complainant_name" => $complainant_name."<br>".$complainant_mobile,
                "transaction_id" => $transaction_id,
                "bank_name" => $bank_name,
                "account_id" => $account_id,
                "amount" => $amount,
                "entry_date" => $entry_date,
                "current_status" => $current_status,
                "date_of_action" => $date_of_action,
                "action_taken_by_name" => $action_taken_by_name,
                "edit" => ''
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


    public function getDatalistOthersourcetype(Request $request)
    {
        // Get request parameters
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'] ?? 0; // Column index
        $columnName = $columnName_arr[$columnIndex]['data'] ?? '_id'; // Column name
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc'; // asc or desc
        $searchValue = $search_arr['value'] ?? ''; // Search value

        // Get all source types
        $source_types = SourceType::all();

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $current_value = $request->get('current_value');
        // dd($current_value);



        $complaints = ComplaintOthers::raw(function ($collection) use ($start, $rowperpage, $current_value, $fromDate, $toDate) {
            $pipeline = [

                [
                    '$group' => [
                        '_id' => '$case_number',
                        'source_type' => ['$addToSet' => '$source_type'],
                        'url' => ['$addToSet' => '$url'],
                        'domain' => ['$addToSet' => '$domain'],
                        'registry_details' => ['$addToSet' => '$registry_details'],
                        'ip' => ['$addToSet' => '$ip'],
                        'registrar' => ['$addToSet' => '$registrar'],
                        'remarks' => ['$addToSet' => '$remarks'],
                    ]
                ],
                [
                    '$sort' => [
                        '_id' => 1
                    ]
                ],
                [
                    '$sort' => [
                        'created_at' => -1
                    ]
                ],
                [
                    '$skip' => (int)$start
                ],
                [
                    '$limit' => (int)$rowperpage
                ],

            ];

            if ($current_value === 'today') {
                $today = new DateTime('today');
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'created_at' => ['$gte' => new UTCDateTime($today->getTimestamp() * 1000)]
                        ]
                    ]
                ], $pipeline);
            }

            if ($fromDate && $toDate) {
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'created_at' => ['$gte' => new UTCDateTime(strtotime($fromDate) * 1000), '$lte' => new UTCDateTime(strtotime($toDate) * 1000)]
                        ]
                    ]
                ], $pipeline);
            }


            return $collection->aggregate($pipeline);
        });

        $distinctCaseNumbers = ComplaintOthers::raw(function($collection) use ($current_value, $fromDate, $toDate) {
            $pipeline = [

                [
                    '$group' => [
                        '_id' => '$case_number'
                    ]
                ]
            ];

            if (isset($current_value)){
                $today = new DateTime('today');
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'created_at' => ['$gte' => new UTCDateTime($today->getTimestamp() * 1000)]
                        ]
                    ]
                ], $pipeline);
            }

            if ($fromDate && $toDate){
                $pipeline = array_merge([
                    [
                        '$match' => [
                            'created_at' => ['$gte' => new UTCDateTime(strtotime($fromDate) * 1000), '$lte' => new UTCDateTime(strtotime($toDate) * 1000)]
                        ]
                    ]
                ], $pipeline);
            }
            return $collection->aggregate($pipeline);
        });

        $totalRecords = count($distinctCaseNumbers->toArray());
        $data_arr = [];
        $i = $start;

        $totalRecordswithFilter = $totalRecords;

        foreach ($complaints as $record) {
            $i++;
            $url = ""; $domain = ""; $ip = ""; $registrar = ""; $remarks = ""; $source_type = "";

            $case_number = '<a href="' . route('other-case-details', ['id' => $record->_id]) . '">' . $record->_id . '</a>';

            foreach ($record->url as $item) {
                $url .= $item . "<br>";
            }
            foreach ($record->source_type as $item) {
                foreach ($source_types as $st) {
                    if ($st->_id == $item) {
                        $source_type .= $st->name . "<br>";
                    }
                }
            }
            foreach ($record->domain as $item) {
                $domain .= $item . "<br>";
            }
            foreach ($record->ip as $item) {
                $ip .= $item . "<br>";
            }
            foreach ($record->registrar as $item) {
                $registrar .= $item . "<br>";
            }
            foreach ($record->remarks as $item) {
                $remarks .= $item . "<br>";
            }

            $data_arr[] = [
                "id" => $i,
                "source_type" => $source_type,
                "case_number" => $case_number,
                "url" => $url,
                "domain" => $domain,
                "ip" => $ip,
                "registrar" => $registrar,
                "remarks" => $remarks,
            ];
        }

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }




    // public function  getDatalistOthersourcetype(Request $request){

    //             // dd($request);
    //             $draw = $request->get('draw');
    //             $start = $request->get("start");
    //             $rowperpage = $request->get("length"); // Rows display per page.

    //             $columnIndex_arr = $request->get('order');
    //             $columnName_arr = $request->get('columns');
    //             $order_arr = $request->get('order');
    //             $search_arr = $request->get('search');

    //             $columnIndex = $columnIndex_arr[0]['column']; // Column index.
    //             $columnName = $columnName_arr[$columnIndex]['data']; // Column name.
    //             $columnSortOrder = $order_arr[0]['dir']; // asc or desc.
    //             $searchValue = $search_arr['value']; // Search value.

    //             $source_types = SourceType::all();
    //             // $casenumber = $request->casenumber;
    //             // $url = $request->url;
    //             // dd($casenumber);

    //             $current_value = $request->get('current_value');
    //             // dd($current_value);

    //             // // Initialize $current_date variable
    //             $current_date = null;

    //             if ($current_value === 'today') {
    //                 // Set $current_date to today's date in 'Y-m-d' format
    //                 $current_date = date('Y-m-d');
    //             }

    //             // Convert current date to MongoDB UTCDateTime
    //             $currentDateTime = new UTCDateTime(strtotime($current_date) * 1000);

    //             //    dd($currentDateTime);

    //             $complaints = ComplaintOthers::raw(function($collection) use ($start, $rowperpage, $currentDateTime) {
    //                 $pipeline = [
    //                     [
    //                         '$group' => [
    //                             '_id' => '$case_number',
    //                             'source_type' => ['$addToSet' => '$source_type'],
    //                             'url' => ['$addToSet' => '$url'],
    //                             'domain' => ['$addToSet' => '$domain'],
    //                             'registry_details' => ['$addToSet' => '$registry_details'],
    //                             'ip' => ['$addToSet' => '$ip'],
    //                             'registrar' => ['$addToSet' => '$registrar'],
    //                             'remarks' => ['$addToSet' => '$remarks'],
    //                         ]
    //                     ],
    //                     [
    //                         '$sort' => [
    //                             '_id' => 1
    //                         ]
    //                     ],
    //                     [
    //                         '$sort' => [
    //                             'created_at' => -1
    //                         ]
    //                     ],
    //                     [
    //                         '$skip' => (int)$start
    //                     ],
    //                     [
    //                         '$limit' => (int)$rowperpage
    //                     ]
    //                 ];

    //                 if (isset($currentDateTime)){
    //                     $pipeline = array_merge([
    //                 [
    //                     '$match' => [
    //                         'created_at' => $currentDateTime
    //                     ]
    //                 ]
    //             ], $pipeline);
    //         }


    //                 return $collection->aggregate($pipeline);
    //             });

    //             $distinctCaseNumbers = ComplaintOthers::raw(function($collection){
    //             return $collection->aggregate([

    //                     [
    //                         '$group' => [
    //                             '_id' => '$case_number'
    //                         ]
    //                     ]
    //                 ]);
    //             });




    //             $totalRecords = count($distinctCaseNumbers);
    //             $data_arr = array();
    //             $i = $start;


    //             $totalRecordswithFilter =  $totalRecords;
    //             foreach($complaints as $record){

    //                 $i++;
    //                 $url = "";$domain="";$ip="";$registrar="";$remarks=""; $source_type="";

    //                 $case_number = '<a href="' . route('other-case-details', ['id' => $record->_id]) . '">'.$record->_id.'</a>';

    //                 foreach ($record->url as $item) {
    //                     $url .= $item."<br>";
    //                 }
    //                 foreach ($record->source_type as $item) {
    //                     foreach($source_types as $st){
    //                         if($st->_id == $item){
    //                             $source_type .= $st->name."<br>";
    //                         }
    //                     }
    //                 }
    //                 foreach ($record->domain as $item) {
    //                     $domain .= $item."<br>";
    //                 }
    //                 foreach ($record->ip as $item) {
    //                     $ip .= $item."<br>";
    //                 }
    //                 foreach ($record->registrar as $item) {
    //                     $registrar .= $item."<br>";
    //                 }
    //                 foreach ($record->remarks as $item) {
    //                     $remarks .= $item."<br>";
    //                 }

    //                 $data_arr[] = array(
    //                         "id" => $i,
    //                         "source_type" => $source_type,
    //                         "case_number" => $case_number,
    //                         "url" => $url,
    //                         "domain" => $domain,
    //                         "ip" => $ip,
    //                         "registrar"=>$registrar,
    //                         "remarks" => $remarks,
    //                         );

    //             }

    //             $response = array(
    //                 "draw" => intval($draw),
    //                 "iTotalRecords" => $totalRecords,
    //                 "iTotalDisplayRecords" => $totalRecordswithFilter,
    //                 "aaData" => $data_arr
    //             );

    //             return response()->json($response);



    // }


}
