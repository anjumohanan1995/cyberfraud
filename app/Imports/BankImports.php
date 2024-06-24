<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Hospital;
use App\Models\BankCasedata;
use App\Models\OldBankCaseData;
use App\Models\OldCaseData;
use Auth;

class BankImports implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */

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

        $collection->transform(function ($values) {


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
                'transaction_date' => $values[14],
                'transaction_id_sec' => $values[15],
                'transaction_amount' => $values[16],
                'reference_no' => $values[17],
                'remarks' => $values[18],
                'date_of_action' => $values[19],
                 'action_taken_by_bank_sec' => $values[20],
                'action_taken_name' => $values[21],
                'action_taken_email' => $values[22],
                'branch_location' => $values[23],
                'branch_manager_details' => $values[24],
            ];
        });

        $validate = Validator::make($collection->toArray(), [
            '*.acknowledgement_no' => 'required|max:150',
            '*.transaction_id_or_utr_no' => 'required',
        ])->validate();

        // foreach ($collection as $collect) {
        //     BankCasedata::create($collect);
        // }

        foreach ($collection as $collect) {
            // Trim and apply case insensitivity to 'account_no_2' field
            if (isset($collect['account_no_2'])) {
                $collect['account_no_2'] = trim($collect['account_no_2']);
            }

            // Trim and apply case insensitivity to 'action_taken_by_bank' field
            if (isset($collect['action_taken_by_bank'])) {
                $collect['action_taken_by_bank'] = trim(strtolower($collect['action_taken_by_bank']));
            }

            // Create BankCasedata object
            BankCasedata::create($collect);
        }

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
