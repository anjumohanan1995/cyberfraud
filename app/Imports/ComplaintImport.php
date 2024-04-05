<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Patient;
use Auth;

class ComplaintImport implements ToCollection, WithStartRow
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
        $collection->transform(function ($row) {

            return [
                'Acknowledgement No'       => $row[1],
            ];
        });

       //dd($collection['Acknowledgement No']);
        // Validator::make($collection->toArray(), [
        //     '*.Patient name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
        //     '*.Mobile' => 'required',
        //     '*.Aadhar' => ['nullable', 'regex:/^([0-9]{4}[0-9]{4}[0-9]{4}$)|([0-9]{4}\s[0-9]{4}\s[0-9]{4}$)|([0-9]{4}-[0-9]{4}-[0-9]{4}$)/'],
        //     '*.Age' => 'required',
        // ])->validate();
        foreach ($collection as $row) {
            // Use the correct key to access the data
            $patient = new Complaint();
            $patient->acknowledgement_no = @$row['Acknowledgement No']; // Use 'Acknowledgement No' as key
            $patient->save();
        }
    }


}
