<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\EvidenceType;
use App\Models\Evidence;

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

        $source_types = SourceType::where('deleted_at',null)->get();
        $evidence_types = EvidenceType::where('deleted_at',null)->get();
        return view('notice.evidence',compact('source_types','evidence_types'));
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
            $edit = '<button class="btn btn-danger delete-btn">Generate Notice</button>';

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




}
