<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Hospital;
use App\Models\ComplaintOthers;
use Auth;

class ComplaintImportOthers implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    protected $source_type;

    public function __construct($source_type,$caseNumber,$filename)
    {
        $this->source_type = $source_type;
        $this->caseNumber = $caseNumber;
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        // dd($collection);
        /*dd($collection);
         return new Patient([
            'name'     => @$collection[0],
            'age'    => @$collection[1],
            'user_id' =>Auth::user()->id


        ]);*/

        $collection->transform(function ($row){
            
            return [
                'url'        => $row[1],
                'domain'     => $row[2],
                'ip'=> $row[3],
                'registrar'=> $row[4],
                'registry_details'=> $row[5],
                'remarks'=> $row[6],
                'ticket_number'=> $row[7],
                'evidence_type' => $row[8],
                'source' => $row[9],
            ];
        });

        $validate = Validator::make($collection->toArray(),[
            '*.url' => 'required|max:150',
           
        ])->validate();


        foreach ($collection as $collect) {

            $complaint = new ComplaintOthers();
            $complaint->source_type = $this->source_type;
            $complaint->case_number = $this->caseNumber;
            $complaint->url = preg_replace('/\s+/', '', $collect['url']);
            $complaint->domain = $collect['domain'];
            $complaint->registry_details = $collect['registry_details'];
            $complaint->ip = $collect['ip'];
            $complaint->registrar = $collect['registrar'];
            $complaint->remarks = $collect['remarks'];
            $complaint->ticket_number = $collect['ticket_number'];
            $complaint->evidence_type = $collect['evidence_type'];
            $complaint->source = $collect['source'];
            $complaint->filename = $this->filename;                 
            $complaint->save();
              




        }
    }
}
