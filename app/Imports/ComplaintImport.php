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
       
        $errors = [];
       
        $collection->transform(function ($row) {
        
            return [
                'sl_no'                     => $row[0] ?? null ,
                'acknowledgement_no'        => $row[1] ?? null,
                'district'                  => $row[2] ?? null,
                'police_station'            => $row[3] ?? null,
                'complainant_name'          => $row[4] ?? null,
                'complainant_mobile'        => $row[5] ?? null,
                'transaction_id'            => $row[6] ?? null,
                'bank_name'                 => $row[7] ?? null,
                'account_id'                => $row[8] ?? null,
                'amount'                    => $row[9] ?? null,
                // 'entry_date'                => $this->parseDate(@$row[10]),
                'entry_date'                => $row[10] ?? null,
                'current_status'            =>$row[11] ?? null, 
                // 'date_of_action'            => $this->parseDate(@$row[12]),
                'date_of_action'            => $row[12] ?? null,
                'action_taken_by_name'         => $row[13] ?? null,
                'action_taken_by_designation'   => $row[14] ?? null,
                'action_taken_by_mobile'         => $row[15] ?? null,
                'action_taken_by_email'         => $row[16] ?? null,
                'action_taken_by_bank'         => $row[17] ?? null,
                'action_taken_by_name'         => $row[18] ?? null,
                'action_taken_by_designation'   => $row[19] ?? null,
                'action_taken_by_mobile'         => $row[20] ?? null,
                'action_taken_by_email'         => $row[21] ?? null,
                'action_taken_by_bank'         => $row[22] ?? null,
            ];
        });

        $filteredCollection = $collection->filter(function ($row) {
            return !empty($row['sl_no']);
        });
       
        $rows = $filteredCollection; 
       
        foreach ($rows as $index => $row){
            $rowIndex = $index + 2;
        
            $data = [
                'acknowledgement_no' => $row['acknowledgement_no'] ?? null,
                'district' => $row['district'] ?? null,
                'police_station' => $row['police_station'] ?? null,
                'complainant_name' => $row['complainant_name'] ?? null,
                'complainant_mobile' => $row['complainant_mobile'] ?? null,
                'transaction_id' => $row['transaction_id'] ?? null,
                'bank_name' => $row['bank_name'] ?? null,
                'amount' => $row['amount'] ?? null,
                'date_of_action' => $row['date_of_action'] ?? null,
                'entry_date' => $row['entry_date'] ?? null,
            ];

            $validator = Validator::make($data, [

                'acknowledgement_no' => 'required|numeric',
                'district' => 'nullable',
                'police_station' => 'nullable',
                'complainant_name' => 'nullable',
                'complainant_mobile' => 'nullable|numeric|digits:10',
                'transaction_id' => 'required',
                'amount' => 'required|numeric',
                'date_of_action' => 'required|valid_date_format',
                'bank_name' => 'required',
                'entry_date' => 'required|valid_date_format_entry_date',


            ], $this->validationMessages($rowIndex));

            if ($validator->fails()) {

                $errors[$rowIndex] = $validator->errors()->all();
            } 
            $rowIndex++;
            if (!empty($errors)) {
                // Create a validator with accumulated errors to throw ValidationException
                $dummyValidator = Validator::make([], []);
                foreach ($errors as $rowIndex => $messages) {
                    foreach ($messages as $message) {
                        $dummyValidator->errors()->add('row_' . $rowIndex, $message);
                    }
                }
               
            }
          
          
        }
        if($errors){
            throw new \Illuminate\Validation\ValidationException($dummyValidator);
        }
        
 

        
        foreach ($filteredCollection as $collect){
         
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
     
                $complaint->entry_date = $this->parseDate($collect['entry_date']);
                $complaint->current_status = $collect['current_status'];
                $complaint->date_of_action = $this->parseDate($collect['date_of_action']);
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
            $complaint->entry_date = $this->parseDate($collect['entry_date']);
            $complaint->current_status = $collect['current_status'];
            $complaint->date_of_action = $this->parseDate($collect['date_of_action']);
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

    protected function validationMessages($index)
{
    return [
        'acknowledgement_no.required' => 'Row ' . ($index) . ': Acknowledgement number is required.',
        'acknowledgement_no.numeric' => 'Row ' . ($index) . ': Acknowledgement number format invalid.',
        
        'complainant_mobile.digits' => 'Row ' . $index . ': Mobile number must be exactly 10 digits.',
        'complainant_mobile.numeric' => 'Row ' . $index . ': Mobile number must be numeric.',
        'transaction_id.required' => 'Row ' . $index . ': Transaction ID is required.',
       
        'bank_name.required' => 'Row ' . $index . ': Bank name field is required.',
        'amount.required' => 'Row ' . $index . ': Amount is required.',
        'amount.numeric' => 'Row ' . $index . ': Amount must be a valid number.',
        'date_of_action.required' => 'Row ' . $index . ': Date of action is required.',
        'date_of_action.valid_date_format' => 'Row ' . $index . ': Date of action is not in a valid format.',
        'entry_date.required' => 'Row ' . $index . ': Entry date is required.',
        'entry_date.valid_date_format_entry_date' => 'Row ' . $index . ': Entry date not in valid format.',

       
    ];
}

protected function formatErrorMessage($message, $index)
{
    // Replace "The" with "Row" and include row number
    return str_replace('The ', 'Row ' . ($index + 2) . ': ', $message);
}
    

    protected function convertAcknoToString($transaction_id)
    {

        return is_numeric($transaction_id) ? (string) $transaction_id : $transaction_id;
    }

    function parseDate($dateString) {
       
      
        if (is_numeric($dateString)) {
            return $this->excelSerialToDate($dateString);
        }
        else{
            return $dateString;
        }
        
    
    }
   
    function excelSerialToDate($serial){
      
        try {
            $baseDate = Carbon::create(1899, 12, 30); 
            $date = $baseDate->addDays((int)$serial);
    
            return $date->toDateTimeString(); 
        } catch (\Exception $e) {
            return null; 
        }
    }



    protected function validDateFormat($dateString)
{
    // Define acceptable date formats
    $formats = [
        'd/m/Y H:i:s A',
        'd-m-Y H:i:s A',
        'd/m/Y h:i:s A',
        'd-m-Y h:i:s A',
        'd/m/Y',
        'd-m-Y',
        'd/F/Y',
        'd-F-Y',
        'd/m/Y H:i',
        'd-m-Y H:i',
        'd/M/Y',
        'd-M-Y'
    ];

    // Try each format to see if it matches
    foreach ($formats as $format) {
        try {
            $date = Carbon::createFromFormat($format, $dateString);

            // Adjust year if it is a 2-digit year
            if (strlen($date->year) == 2) {
                $date->year = $date->year + ($date->year < 30 ? 2000 : 1900);
            }

            return true;
        } catch (\Exception $e) {
            // Continue to next format
        }
    }

    return false;
}


}
