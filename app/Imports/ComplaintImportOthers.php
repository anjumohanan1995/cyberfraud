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
    protected $caseNumber;
    protected $filename;

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
     * Handle the collection of rows from the Excel file.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {


            // Convert URL to string
            $url = $this->convertUrlToString($row['url']);


            // Check for duplicates
            $exists = ComplaintOthers::where('url', $url)
                ->where('domain', $row['domain'])
                ->where('ip', $row['ip'])
                ->exists();

            if (!$exists) {
                $data = [
                    'case_number' => $this->caseNumber,
                    'source_type' => $this->source_type,
                    'file_name'   => $this->filename,
                    'url'         => $url,
                    'domain'      => $row['domain'],
                    'ip'          => $row['ip'],
                    'registrar'   => $row['registrar'],
                    'registry_details' => $row['registry_details'],
                    'remarks'     => $row['remarks'],
                    'content_removal_ticket' => $row['content_removal_ticket'],
                    'data_disclosure_ticket' => $row['data_disclosure_ticket'],
                    'preservation_ticket' => $row['preservation_ticket'],
                    'evidence_type' => strtolower($row['evidence_type']),
                    'source'       => $row['source'],
                    'status'       => 1
                ];



                ComplaintOthers::create($data);
            }
        }

    }

        /**
     * Convert URL or mobile to string.
     *
     * @param mixed $urlormobile
     * @return string
     */

    protected function convertUrlToString($urlormobile)
    {

        return is_numeric($urlormobile) ? (string) $urlormobile : $urlormobile;
    }

        /**
     * Define validation rules for the import.
     *
     * @return array
     */

    public function rules(): array
    {
        $evidenceTypes = EvidenceType::where('status', 'active')
        ->whereNull('deleted_at')
        ->pluck('name')
        ->toArray();

        $uniqueItems = array_unique(array_map('strtolower', $evidenceTypes));

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
