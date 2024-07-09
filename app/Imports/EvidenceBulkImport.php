<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

use App\Models\Evidence; 

class EvidenceBulkImport implements ToCollection , WithStartRow
{
    /**
    * @param Collection $collection
    */
    public function startRow():int
    {
        return 2;
    }
    public function collection(Collection $collection)
{
    $collection->transform(function ($row) {
        return [
            'evidence_type' => $row[0],
            'evidence_type_id' => '2', 
            'ticket' => $row[8],
            'data_disclosure' => $row[9],
            'preservation' => $row[10],
            'category' => $row[7],
            'remarks' => $row[6],
            'url' => $row[1],
            'domain' => $row[2],
            'registry_details' => $row[3],
            'ip' => $row[4],
            'registrar' => $row[5],
           
        ];
    });

    foreach ($collection as $collect){
        Evidence::create($collect);
    }

}

}
