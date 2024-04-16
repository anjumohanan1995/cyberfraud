<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ComplaintImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

       // dd($collection[1]);
        // Define how to create a model from the Excel row data
        $collection->transform(function ($row) {
            return [
                'Acknowledgement No'       => $row[1],
            ];
        });
        foreach ($collection as $row) {
           // dd($row['Acknowledgement No']);
            // Use the correct key to access the data
            // $patient = new Complaint();
            // $patient->acknowledgement_no = @$row['Acknowledgement No']; // Use 'Acknowledgement No' as key
            // $patient->save();

            Complaint::create([
                'acknowledgement_no' =>  @$row[0],
                // 'email' => 'superadmin@email.com',
                // 'password' => Hash::make('12345678'), // You should hash the password for security
                // 'role' => 'super admin', // Assuming 1 is the role ID for super admin
            ]);
        }
    }
}
