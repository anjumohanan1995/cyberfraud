<?php

namespace App\Imports;

use App\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;
use App\Models\ComplaintOthers;
use App\Models\EvidenceType;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ComplaintImportOthers implements ToCollection, WithHeadingRow , WithValidation
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
        return 1;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
       foreach($rows as $row){
        $data = [
                'case_number' => $this->caseNumber,
                'source_type' => $this->source_type,
                'file_name'   => $this->filename,
                'url'        => $row['url'],
                'domain'     => $row['domain'],
                'ip'=> $row['ip'],
                'registrar'=> $row['registrar'],
                'registry_details'=> $row['registry_details'],
                'remarks'=> $row['remarks'],
                'ticket_number'=> $row['ticket_number'],
                'evidence_type' => strtolower($row['evidence_type']),
                'source' => $row['source'],
                'status' => 1
        ];
        ComplaintOthers::create($data);
       }
 
    }
        
    public function rules(): array
    {
        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->pluck('name')
        ->toArray();
        
        $uniqueItems = array_unique($evidenceTypes);
        
        return[
            'url' => 'required',
            'domain' => 'required',
            'evidence_type' => [
                'required',
                function ($attribute, $value, $fail) use ($uniqueItems) {
                    if (!in_array(strtolower($value), $uniqueItems)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],

        ];
    }

}
