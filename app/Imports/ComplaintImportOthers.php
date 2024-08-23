<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\ComplaintOthers;
use App\Models\EvidenceType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ComplaintImportOthers implements ToCollection, WithStartRow
{
    public $newRecordsInserted = false;
    protected $source_type;
    protected $caseNumber;
    protected $filename;
    protected $uniqueEvidenceTypes;
    protected $errors = [];

    public function __construct($source_type, $caseNumber, $filename)
    {
        $this->source_type = $source_type;
        $this->caseNumber = $caseNumber;
        $this->filename = $filename;
        $this->uniqueEvidenceTypes = $this->getUniqueEvidenceTypes();
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            $rowIndex = $index + $this->startRow();
            $data = [
                'url_mobile' => $row[1],
                'domain_post_profile' => $row[2],
                'evidence_type' => $row[10],
                // Add other fields as needed
            ];

            try {
                $this->validateRow($data, $rowIndex);

                $complaintData = [
                    'case_number' => $this->caseNumber,
                    'source_type' => $this->source_type,
                    'file_name'   => $this->filename,
                    'url'         => $this->convertUrlToString($data['url_mobile']),
                    'domain'      => $data['domain_post_profile'],
                    'ip'          => $row[3],
                    'registrar'   => $row[4],
                    'registry_details' => $row[5],
                    'remarks'     => $row[6],
                    'content_removal_ticket' => $row[7],
                    'data_disclosure_ticket' => $row[8],
                    'preservation_ticket' => $row[9],
                    'evidence_type' => strtolower($data['evidence_type']),
                    'source'       => $row[11],
                    'status'       => 1,
                    'reported_status' => 'active'
                ];


                if (!$this->isDuplicate($complaintData)) {
                    ComplaintOthers::create($complaintData);
                    $this->newRecordsInserted = true;
                }
            } catch (ValidationException $e) {
                $this->errors["Row {$rowIndex}"] = $e->errors();
            } catch (\Exception $e) {
                $this->errors["Row {$rowIndex}"] = ["General Error" => $e->getMessage()];
                \Illuminate\Support\Facades\Log::error("Error processing row {$rowIndex}: " . $e->getMessage());
            }
        }

        if (!empty($this->errors)) {
            throw new \Exception("Validation errors occurred during import.");
        }
    }

    protected function validateRow($data, $rowIndex)
    {
        $validator = Validator::make($data, [
            'url_mobile' => 'required',
            'domain_post_profile' => 'required',
            'evidence_type' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower($value), $this->uniqueEvidenceTypes)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function convertUrlToString($urlormobile)
    {
        return is_numeric($urlormobile) ? (string) $urlormobile : $urlormobile;
    }

    protected function isDuplicate($data)
    {
        return ComplaintOthers::where('url', $data['url'])
            ->where('domain', $data['domain'])
            ->where('ip', $data['ip'])
            ->exists();
    }

    protected function getUniqueEvidenceTypes()
    {
        $evidenceTypes = EvidenceType::where('status', 'active')
            ->whereNull('deleted_at')
            ->pluck('name')
            ->toArray();

        return array_unique(array_map('strtolower', $evidenceTypes));
    }

        /**
     * Get all errors encountered during the import process.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
