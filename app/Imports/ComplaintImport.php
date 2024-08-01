<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


use App\Hospital;
use Auth;
use DateTime;

class ComplaintImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    protected $source_type;

    use Importable;


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

            // Convert the 'entry_date' field
            $entryDate = DateTime::createFromFormat('d-m-y H:i', $row[10]);
                     // If that fails, try to create DateTime object from the date-only format
           if (!$entryDate) {
            $entryDate = DateTime::createFromFormat('d-m-y', $row[10]);
        }

            $formattedEntryDate = $entryDate ? $entryDate->format('Y-m-d H:i:s') : null;

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
                'entry_date'                => $formattedEntryDate,
                'current_status'             => $row[11],
                'date_of_action'            => $row[12],
                'action_taken_by_name'         => "",
                'action_taken_by_designation'   => "",
                'action_taken_by_mobile'         => "",
                'action_taken_by_email'         => "",
                'action_taken_by_bank'         => "",
            ];
        });

        $validate = Validator::make($collection->toArray(), [
            '*.acknowledgement_no' => 'required',

        ])->validate();
            $c = $com = Complaint::all();
            // print_r($c)."<br>";
            // dd();
        foreach ($collection as $collect){
            // echo $date->format('Y-m-d');

                $complaint = Complaint::where('acknowledgement_no', (int)$collect['acknowledgement_no'])
                                        ->where('transaction_id',(string)$collect['transaction_id'])
                                        ->first();

            if($complaint){
                $complaint->source_type = $this->source_type;
                $complaint->acknowledgement_no = $collect['acknowledgement_no'];
                $complaint->district = $collect['district'];
                $complaint->police_station = $collect['police_station'];
                $complaint->complainant_name = $collect['complainant_name'];
                $complaint->complainant_mobile = $collect['complainant_mobile'];
                $complaint->transaction_id = $this->convertAcknoToString($collect['transaction_id']);
                $complaint->bank_name = $collect['bank_name'];
                $complaint->account_id = $collect['account_id'];
                $complaint->amount = $collect['amount'];
                // dd($collect['entry_date']);
                $complaint->entry_date = $collect['entry_date'];
                $complaint->current_status = $collect['current_status'];
                $complaint->date_of_action = $collect['date_of_action'];
                $complaint->action_taken_by_name = $collect['action_taken_by_name'];
                $complaint->action_taken_by_designation = $collect['action_taken_by_designation'];
                $complaint->action_taken_by_mobile = $collect['action_taken_by_mobile'];
                $complaint->action_taken_by_email = $collect['action_taken_by_email'];
                $complaint->action_taken_by_bank = $collect['action_taken_by_bank'];
                $complaint->com_status = 1;
                $complaint->update();
            }
            else{
                $complaint = new Complaint();
            $complaint->source_type = $this->source_type;
            $complaint->acknowledgement_no = $collect['acknowledgement_no'];
            $complaint->district = $collect['district'];
            $complaint->police_station = $collect['police_station'];
            $complaint->complainant_name = $collect['complainant_name'];
            $complaint->complainant_mobile = $collect['complainant_mobile'];
            $complaint->transaction_id = $this->convertAcknoToString($collect['transaction_id']);
            $complaint->bank_name = $collect['bank_name'];
            $complaint->account_id = $collect['account_id'];
            $complaint->amount = $collect['amount'];
            // dd($collect['entry_date']);
            $complaint->entry_date = $collect['entry_date'];
            $complaint->current_status = $collect['current_status'];
            $complaint->date_of_action = $collect['date_of_action'];
            $complaint->action_taken_by_name = $collect['action_taken_by_name'];
            $complaint->action_taken_by_designation = $collect['action_taken_by_designation'];
            $complaint->action_taken_by_mobile = $collect['action_taken_by_mobile'];
            $complaint->action_taken_by_email = $collect['action_taken_by_email'];
            $complaint->action_taken_by_bank = $collect['action_taken_by_bank'];
            $complaint->com_status = 1;
            $complaint->save();
            }




        }


    }

    protected function convertAcknoToString($transaction_id)
    {

        return is_numeric($transaction_id) ? (string) $transaction_id : $transaction_id;
    }


}
