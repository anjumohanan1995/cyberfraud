<?php

namespace App\Http\Controllers;
use App\Models\SourceType;
use App\Models\EvidenceType;
use App\Models\Evidence;
use Carbon\Carbon;

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

        $items = Evidence::where('ack_no', $request->ackno)
                            ->orderBy('_id', 'desc')
                            ->orderBy($columnName, $columnSortOrder);

        $records = $items->skip($start)->take($rowperpage)->get();
        $totalRecord = Evidence::where('ack_no', $request->ackno)->orderBy('_id', 'desc');
        $totalRecords = $totalRecord->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $totalRecords;

        if($from_date){
            
            
           // dd($from_date);
        }
        
        $data_arr = array();
        $i=$start;

        foreach($records as $record){
            $i++;
            $id = $record->id;
            $acknowledgement_no = $record->ack_no;
            $evidence_type =  $record->evidence_type;
            $url  =  $record->url;
            $domain  =  $record->domain;
            $ip  =  $record->ip;
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
