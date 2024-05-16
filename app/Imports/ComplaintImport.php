<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Hospital;
use Auth;

class ComplaintImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    protected $source_type;

    public function __construct($source_type)
    {
        $this->source_type = $source_type;
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

        $collection->transform(function ($row) {

            return [
                'acknowledgement_no'        => $row[1],
                'district'                  => $row[2],
                'police_station'            => $row[3],
                'complainant_name'           => $row[4],
                'complainant_mobile'         => $row[5],
                'transaction_id'             => $row[6],
                'bank_name'                  => $row[7],
                'account_id'                  => $row[8],
                'amount'                    => $row[9],
                'entry_date'                => $row[10],
                'current_status'             => $row[11],
                'date_of_action'            => $row[12],
                'action_taken_by_name'         => $row[13],
                'action_taken_by_designation'   => $row[14],
                'action_taken_by_mobile'         => $row[15],
                'action_taken_by_email'         => $row[16],
                'action_taken_by_bank'         => $row[17],
            ];
        });

        $validate = Validator::make($collection->toArray(), [
            '*.acknowledgement_no' => 'required|max:150',
            
        ])->validate();


        foreach ($collection as $collect) {
            
            $complaint = new Complaint();
            $complaint->source_type = $this->source_type;
            $complaint->acknowledgement_no = $collect['acknowledgement_no'];
            $complaint->district = $collect['district'];
            $complaint->police_station = $collect['police_station'];
            $complaint->complainant_name = $collect['complainant_name'];
            $complaint->complainant_mobile = $collect['complainant_mobile'];
            $complaint->transaction_id = $collect['transaction_id'];
            $complaint->bank_name = $collect['bank_name'];
            $complaint->account_id = $collect['account_id'];
            $complaint->amount = $collect['amount'];
            $complaint->entry_date = $collect['entry_date'];
            $complaint->current_status = $collect['current_status'];
            $complaint->date_of_action = $collect['date_of_action'];
            $complaint->action_taken_by_name = $collect['action_taken_by_name'];
            $complaint->action_taken_by_designation = $collect['action_taken_by_designation'];
            $complaint->action_taken_by_mobile = $collect['action_taken_by_mobile'];
            $complaint->action_taken_by_email = $collect['action_taken_by_email'];
            $complaint->action_taken_by_bank = $collect['action_taken_by_bank'];

            $ack="";
            $ack = complaint::where('acknowledgement_no',$collect['acknowledgement_no']);
            if($ack){
                $transn = complaint::where('acknowledgement_no',$collect['acknowledgement_no'])->where('transaction_id',$collect['transaction_id'])->first();
                if($transn){
                    $complaint->update();
                }
                else{
                    $complaint->save();
                }
                
            }


            
        }
    }
}
