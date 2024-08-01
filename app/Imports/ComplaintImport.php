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
use Carbon\Carbon;


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

            // if (strpos($row[10], ' ') === false) {

            //     $entryDate = $row[10].' 00:00:00';
            //     $entryDate = DateTime::createFromFormat('d-m-Y H:i:s', $entryDate);
            //     if($entryDate == false){
            //         $entryDate = $row[10].' 00:00:00';
            //         $entryDate = DateTime::createFromFormat('d/m/Y H:i:s', $entryDate);
            //     }
            //  }
            //  else{
            //     $entryDate = DateTime::createFromFormat('d-m-Y H:i:s', $row[10]);
            //     if($entryDate == false){
            //         $entryDate = DateTime::createFromFormat('d/m/Y H:i:s', $entryDate);
            //     }
            //  }
            //  if($row[10]===null){
            //    $entryDate=null;

            // }

            // If that fails, try to create DateTime object from the date-only format

            // $formattedEntryDate = $entryDate ? $entryDate->format('Y-m-d H:i:s') : null;

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
                'entry_date'                => $this->parseDate(@$row[10]),
                'current_status'             => $this->parseDate(@$row[11]),
                'date_of_action'            => $row[12],
                'action_taken_by_name'         => "",
                'action_taken_by_designation'   => "",
                'action_taken_by_mobile'         => "",
                'action_taken_by_email'         => "",
                'action_taken_by_bank'         => "",
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

    function parseDate($dateString) {
        // Define possible date formats with placeholders for two-digit years
        if (is_numeric($dateString)) {
            return $this->excelSerialToDate($dateString);
        }


        $formats = [
            'd/m/Y H:i:s',
            'd-m-Y H:i:s',
            'd/m/Y',
            'd-m-Y',
            'd-F-Y',
            'd-F-y',
            'd/m/Y H:i',
            'd-m-Y H:i',
            'd/M/Y',
            'd-M-Y'
        ];

        // Try each format until one succeeds
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);

                // Check if year is two-digit
                if (strlen($date->year) == 2) {
                    // Assuming years 00-29 are 2000-2029, and 30-99 are 1930-1999
                    $date->year = $date->year + ($date->year < 30 ? 2000 : 1900);
                }

                return $date->toDateTimeString(); // Return in 'Y-m-d H:i:s' format
            } catch (\Exception $e) {
                // Continue to the next format
            }
        }

        // Return null or handle error if no format matches
        return null;
    }

    function excelSerialToDate($serial) {
        // Convert Excel serial date to a Carbon date
        try {
            $baseDate = Carbon::create(1899, 12, 30); // Excel starts from Dec 30, 1899
            $date = $baseDate->addDays((int)$serial);

            return $date->toDateTimeString(); // Return in 'Y-m-d H:i:s' format
        } catch (\Exception $e) {
            return null; // Return null if conversion fails
        }
    }


}
