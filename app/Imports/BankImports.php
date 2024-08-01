<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Validator;
use App\Hospital;
use App\Models\BankCasedata;
use App\Models\Complaint;
use App\Models\OldBankCaseData;
use App\Models\OldCaseData;
use Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;

class BankImports implements ToCollection, WithStartRow, WithChunkReading
{
    /**
     * @param Collection $collection
     */

    /**
     * @return int
     */
    public function startRow():int
    {
        return 2;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function chunkSize(): int
    {
        return 1000; // Number of rows to import per chunk
    }
    public function collection(Collection $collection)
    {
        $acknowledgementNos = Complaint::pluck('acknowledgement_no')->toArray();

        $collection->transform(function ($values) {
             // Convert the 'entry_date' field



            return [
                'acknowledgement_no' => $values[1],
                'transaction_id_or_utr_no' => $values[2],
                'Layer' => $values[3],
                'account_no_1' => $values[4],
                'action_taken_by_bank' => $values[5],
                'bank' => $values[6],
                'account_no_2' => $values[7],
                'ifsc_code' => $values[8],
                'cheque_no' => $values[9],
                'mid' => $values[10],
                'tid' => $values[11],
                'approval_code' => $values[12],
                'merchant_name' => $values[13],

               'transaction_date' => $this->parseDate(@$values[14]),
                'transaction_id_sec' => $values[15],
                'transaction_amount' => $values[16],
                'reference_no' => $values[17],
                'remarks' => $values[18],
                'date_of_action' => $this->parseDate(@$values[19]),
                 'action_taken_by_bank_sec' => $values[20],
                'action_taken_name' => $values[21],
                'action_taken_email' => $values[22],
                'branch_location' => $values[23],
                'branch_manager_details' => $values[24],
                'com_status'=>1,
            ];
        });

        $rules = [
            '*.acknowledgement_no' => 'required|in:' . implode(',', $acknowledgementNos),

        ];

        $validator = Validator::make($collection->toArray(), $rules);

        if ($validator->fails()) {
            // Handle validation failure
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        // $validate = Validator::make($collection->toArray(),[
        //     '*.acknowledgement_no' => 'required',

        // ])->validate();

        DB::connection('mongodb')->collection('bank_casedata')->where('acknowledgement_no',$collection[0]['acknowledgement_no'])->delete();

        foreach ($collection as $collect){

            // $bank_data = BankCasedata::where('acknowledgement_no', (int)$collect['acknowledgement_no'])->where('transaction_id_sec',(string)$collect['transaction_id_sec'])->first();



             $bank_data = BankCasedata::where('acknowledgement_no', (int)$collect['acknowledgement_no'])->where('transaction_id_sec',(string)$collect['transaction_id_sec'])->first();

                $bank_data = new BankCasedata();
                $bank_data->acknowledgement_no = $collect['acknowledgement_no'];
                $bank_data->transaction_id_or_utr_no = $this->convertAcknoToString($collect['transaction_id_or_utr_no']);
                $bank_data->Layer = $collect['Layer'];
                $bank_data->account_no_1 = $collect['account_no_1'];
                $bank_data->action_taken_by_bank = trim(strtolower($collect['action_taken_by_bank']));
                $bank_data->bank = $collect['bank'];
                $bank_data->account_no_2 = trim($collect['account_no_2']);
                $bank_data->ifsc_code = $collect['ifsc_code'];
                $bank_data->cheque_no = $collect['cheque_no'];
                $bank_data->mid = $collect['mid'];

                $bank_data->tid = $collect['tid'];
                $bank_data->approval_code = $collect['approval_code'];
                $bank_data->merchant_name = $collect['merchant_name'];
                $bank_data->transaction_date = $collect['transaction_date'];
                $bank_data->transaction_id_sec = $this->convertAcknoToString($collect['transaction_id_sec']);
                $bank_data->transaction_amount = $collect['transaction_amount'];
                $bank_data->reference_no = $collect['reference_no'];

                $bank_data->remarks = $collect['remarks'];
                $bank_data->date_of_action = $collect['date_of_action'];
                $bank_data->action_taken_by_bank_sec = $collect['action_taken_by_bank_sec'];
                $bank_data->action_taken_name = $collect['action_taken_name'];
                $bank_data->action_taken_email = $collect['action_taken_email'];
                $bank_data->branch_location = $collect['branch_location'];

                $bank_data->branch_manager_details = $collect['branch_manager_details'];

                $bank_data->com_status = $collect['com_status'];

                $bank_data->save();





        // BankCasedata::create($collect);

        // foreach ($collection as $collect) {

        //     // Check if there's an existing record with matching acknowledgement_no and transaction_id_or_utr_no.
        //     $existingRecord = BankCasedata::where('acknowledgement_no', $collect['acknowledgement_no'])
        //         ->where('transaction_id_or_utr_no', $collect['transaction_id_or_utr_no'])
        //         ->first();

        //     if ($existingRecord) {
        //         // checking if the existing data is already recorded before. if is recorded before then there
        //         // is no need to reupload it again . or duplecate it again.

        //         $existingRecordData = $existingRecord->toArray();
        //         unset($existingRecordData['updated_at']);
        //         unset($existingRecordData['created_at']);
        //         unset($existingRecordData['_id']);

        //         //unseting the data which is different from the old data.


        //         // Check if the existing record data is the same as the data in OldBankCaseData
        //         $oldRecord = OldBankCaseData::where($existingRecordData)->first();

        //         // If the old record doesn't exist, create it.
        //         // copying the orginal data to old data collection for backup or history.
        //         if (!$oldRecord) {
        //             $oldBankCaseData = new OldBankCaseData();
        //             $oldBankCaseData->fill($existingRecord->toArray());
        //             $oldBankCaseData->old_id = $existingRecord->_id;
        //             $oldBankCaseData->updated_date = now()->format('Y-m-d');
        //             $oldBankCaseData->save();
        //         }

        //         //updating if there is any change in the previous data.
        //         $existingRecord->update($collect);
        //     } else {

        //         //saving new data.
        //         BankCasedata::create($collect);
        //     }
        // }
    }
}

    protected function convertAcknoToString($acknowledgement_no)
    {

        return is_numeric($acknowledgement_no) ? (string) $acknowledgement_no : $acknowledgement_no;
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
