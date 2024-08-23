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
        $validData = [];
        $errors = [];
        foreach ($collection as $index => $row) {
            $rowIndex = $index + $this->startRow();

            $evidenceType = strtolower(trim($row[10]));
            $data = [
                'url/mobile' => $row[1],
                'evidencetype' => $evidenceType,
            ];

            switch ($evidenceType) {
                case 'mobile':
                case 'whatsapp':
                    // No additional fields required
                    break;

                case 'website':
                    $data['domain'] = $row[2];
                    $data['ip'] = $row[3];
                    $data['registrar'] = $row[4];
                    $data['registry_details'] = $row[5];
                    break;

                default:
                    $data['domain'] = $row[2];
                    break;
            }

            try {
                $this->validateRow($data, $rowIndex);

                $complaintData = [
                    'case_number' => $this->caseNumber,
                    'source_type' => $this->source_type,
                    'file_name'   => $this->filename,
                    'url'         => $this->convertUrlToString($data['url/mobile']),
                    'evidence_type' => $evidenceType,
                    'remarks'     => $row[6],
                    'content_removal_ticket' => $row[7],
                    'data_disclosure_ticket' => $row[8],
                    'preservation_ticket' => $row[9],
                    'source'       => $row[11],
                    'status'       => 1,
                    'reported_status' => 'active'
                ];

                if ($evidenceType === 'website') {
                    $complaintData['domain'] = $data['domain'];
                    $complaintData['ip'] = $data['ip'];
                    $complaintData['registrar'] = $data['registrar'];
                    $complaintData['registry_details'] = $data['registry_details'];
                } elseif ($evidenceType !== 'mobile' && $evidenceType !== 'whatsapp') {
                    $complaintData['domain'] = $data['domain'];
                }

                $validData[] = $complaintData;
            } catch (ValidationException $e) {
                $errors[$rowIndex] = $e->errors();
            }
        }

        if (!empty($errors)) {
            throw new \Exception(json_encode($errors));
        }

        // Directly insert valid data without a transaction
        foreach ($validData as $data) {
            if (!$this->isDuplicate($data)) {
                ComplaintOthers::create($data);
                $this->newRecordsInserted = true;
            }
        }


    }
    protected function validateRow($data, $rowIndex)
    {
        $rules = [
            'url/mobile' => 'required',
            'evidencetype' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtolower($value), $this->uniqueEvidenceTypes)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
        ];

        if ($data['evidencetype'] === 'website') {
            $rules['domain'] = 'required';
            $rules['ip'] = 'required';
            $rules['registrar'] = 'required';
        } elseif ($data['evidencetype'] !== 'mobile' && $data['evidencetype'] !== 'whatsapp') {
            $rules['domain'] = 'required';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    protected function convertUrlToString($urlormobile)
    {
        return is_numeric($urlormobile) ? (string) $urlormobile : $urlormobile;
    }

    protected function isDuplicate($data)
    {
        $query = ComplaintOthers::where('case_number', $this->caseNumber)
            ->where('url', $data['url'])
            ->where('evidence_type', $data['evidence_type']);

        if (isset($data['domain'])) {
            $query->where('domain', $data['domain']);
        }

        if (isset($data['ip'])) {
            $query->where('ip', $data['ip']);
        }

        return $query->exists();
    }


    protected function getUniqueEvidenceTypes()
    {
        $evidenceTypes = EvidenceType::where('status', 'active')
            ->whereNull('deleted_at')
            ->pluck('name')
            ->toArray();

        return array_unique(array_map('strtolower', $evidenceTypes));
    }
}
