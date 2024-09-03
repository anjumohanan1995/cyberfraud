<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Evidence;
use App\Models\EvidenceType;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Support\Facades\Log;

class EvidenceBulkImport implements ToCollection, WithStartRow
{
    public $newRecordsInserted = false;
    public $errors = [];

    protected $categories;
    protected $evidenceTypes;

    protected $headerMapping = [
        'ack_no' => 'acknowledgement no',
        'url' => ['url','url/mobile','mobile'],
        'remarks' => 'remarks',
        'ticket' => 'content removal ticket',
        'data_disclosure' => 'data disclosure ticket',
        'preservation' => 'preservation ticket',
        'evidence_type' => 'evidence type',
        'category' => 'category',
        'domain' => ['post/profile', 'domain', 'domain/post/profile'],
        'ip' => 'ip',
        'registrar' => 'registrar',
        'registry_details' => 'registry details',
        'country_code' => 'country code',
    ];

    public function __construct()
    {
        $this->categories = Category::pluck('name')->map('strtolower')->toArray();
        $this->evidenceTypes = EvidenceType::pluck('name')->map('strtolower')->toArray();
    }

    public function startRow(): int
    {
        return 1;
    }

    public function collection(Collection $collection)
    {
        $header = $collection->first();
        if (!$header) {
            $this->errors[] = 'No data found in the file.';
            return;
        }

        $header = $header->map(fn($value) => is_string($value) ? strtolower(trim($value)) : $value)->toArray();

        Log::info('Header row:', ['headers' => $header]);

        $mappedHeader = $this->mapHeaders($header);

        $requiredColumns = array_keys($this->headerMapping);
        $optionalColumns = ['ip', 'registrar', 'registry_details', 'country_code', 'url', 'domain'];
        $requiredColumns = array_diff($requiredColumns, $optionalColumns);

        $missingColumns = array_diff($requiredColumns, array_keys($mappedHeader));
        if (!empty($missingColumns)) {
            foreach ($missingColumns as $col) {
                $columnName = $this->headerMapping[$col];

                $columnNameStr = is_array($columnName) ? implode(', ', $columnName) : $columnName;

                $this->errors[] = "Missing required column: {$columnNameStr}";
            }

            $this->logErrors();
            return;
        }

        $collection->shift();

        foreach ($collection as $index => $row) {
            $rowIndex = $index + $this->startRow();
            $data = $this->parseRow($row, $mappedHeader);

            if ($data['errors']) {
                $this->errors[] = "Row {$rowIndex}: " . implode(', ', $data['errors']);
                continue;
            }

            if (!$this->isValidForType($data['data'], $data['data']['evidence_type'] ?? '')) {
                $this->errors[] = "Row {$rowIndex}:Data not sufficient for evidence type '{$data['data']['evidence_type']}'";
                continue;
            }

            if (!$this->isDuplicate($data['data'])) {
                Evidence::create($data['data']);
                $this->newRecordsInserted = true;
            }
        }

        if (!empty($this->errors)) {
            $this->logErrors();
        }
    }

    protected function mapHeaders(array $header)
    {
        $mappedHeader = [];

        $normalizedHeaders = array_map(function($value) {
            return strtolower(trim($value));
        }, $header);

        foreach ($this->headerMapping as $field => $headerNames) {
            $headerNames = is_array($headerNames) ? $headerNames : [$headerNames];
            $headerNames = array_map(function($name) {
                return strtolower(trim($name));
            }, $headerNames);

            $columnIndex = null;
            foreach ($headerNames as $headerName) {
                $normalizedHeaderName = str_replace(' ', '_', $headerName);
                foreach ($normalizedHeaders as $index => $column) {
                    if (str_replace(' ', '_', $column) === $normalizedHeaderName) {
                        $columnIndex = $index;
                        break 2;
                    }
                }
            }

            if ($columnIndex !== null) {
                $mappedHeader[$field] = $columnIndex;
            } else {
                Log::warning("Column '{$headerName}' not found in the header row");
            }
        }

        Log::info('Mapped Headers:', ['mappedHeaders' => $mappedHeader]);

        return $mappedHeader;
    }

    protected function parseRow($row, $mappedHeader)
{
    $errors = [];
    $data = [];

    $rowArray = $row->toArray();

    Log::info('Row data:', ['rowData' => $rowArray]);

    foreach ($this->headerMapping as $field => $headerNames) {
        $headerNames = is_array($headerNames) ? $headerNames : [$headerNames];
        $headerNames = array_map('strtolower', array_map('trim', $headerNames));

        $columnIndex = null;
        foreach ($headerNames as $headerName) {
            $normalizedHeaderName = str_replace(' ', '_', $headerName);
            if (isset($mappedHeader[$field])) {
                $columnIndex = $mappedHeader[$field];
                break;
            }
        }

        if ($columnIndex === null || !isset($rowArray[$columnIndex])) {
            Log::warning("Column not found in the mapping for field '{$field}'");
            $data[$field] = null;
            continue;
        }

        $value = $rowArray[$columnIndex] ?? '';
        $value = is_array($value) ? implode(', ', array_map('trim', $value)) : (string) trim($value);

        Log::info("Processing value for field '{$field}':", ['index' => $columnIndex, 'value' => $value]);

        if ($field === 'ack_no') {
            $value = $this->convertAcknoToString($value);
        } elseif ($field === 'url') {
            $value = $this->convertUrlToString($value);
        }

        $data[$field] = $value;
    }

    $data = array_filter($data, function($value) {
        return $value !== null && $value !== '';
    });

    Log::info('Acknowledgment number:', ['ackNo' => $data['ack_no'] ?? 'N/A']);

    if (empty($data['ack_no']) || !$this->ackNoExists($data['ack_no'])) {
        $errors[] = "Acknowledgment number {$data['ack_no']} does not exist.";
    }

    if (!empty($data['evidence_type']) && !$this->evidenceTypeExists($data['evidence_type'])) {
        $errors[] = "Evidence type '{$data['evidence_type']}' does not exist.";
    }

    if (empty($data['evidence_type']) ) {
        $errors[] = "Evidence type is mandatory.";
    }

    if (!empty($data['category']) && !$this->categoryExists($data['category'])) {
        $errors[] = "Category '{$data['category']}' does not exist.";
    }

    if (empty($data['category'])) {
        $errors[] = "Category  is mandatory.";
    }

    Log::info('Parsed row data:', ['parsedData' => $data]);

    return ['data' => $data, 'errors' => $errors];
}


    protected function categoryExists($category)
    {
        return Category::where('name', 'regex', "/^" . preg_quote($category) . "$/i")->exists();
    }

    protected function evidenceTypeExists($evidenceType)
    {
        return EvidenceType::where('name', strtolower($evidenceType))->exists();
    }

    protected function convertToString($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map(fn($val) => trim($val), $value));
        }

        return trim($value);
    }

    protected function ackNoExists($ackNo)
    {
        $ackNoString = (string) trim($ackNo);

        Log::info("Checking acknowledgment number", [
            'ackNo' => $ackNo,
            'ackNoString' => $ackNoString,
            'ackNoInt' => is_numeric($ackNoString) ? (int) $ackNoString : null,
        ]);

        $exists = Complaint::where(function ($query) use ($ackNoString) {
            $query->orWhere('acknowledgement_no', $ackNoString)
                  ->orWhere('acknowledgement_no', (int) $ackNoString);
        })->exists();
        // dd($exists);
        Log::info("Acknowledgment number check result", [
            'ackNo' => $ackNoString,
            'exists' => $exists,
        ]);

        return $exists;
    }


    // protected function isDuplicate($data)
    // {
    //     $query = Evidence::where('url', $data['url']);

    //     $orConditions = [];

    //     if (!empty($data['domain'])) {
    //         $orConditions[] = ['domain' => $data['domain']];
    //     }
    //     if (!empty($data['ip'])) {
    //         $orConditions[] = ['ip' => $data['ip']];
    //     }
    //     if (!empty($data['ack_no'])) {
    //         $orConditions[] = ['ack_no' => $data['ack_no']];
    //     }
    //     if (!empty($data['ticket'])) {
    //         $orConditions[] = ['ticket' => $data['ticket']];
    //     }

    //     if (!empty($orConditions)) {
    //         $query->where(function ($q) use ($orConditions) {
    //             foreach ($orConditions as $condition) {
    //                 $q->orWhere($condition);
    //             }
    //         });
    //     }

    //     $isDuplicate = $query->exists();

    //     Log::info("Duplicate check result: " . ($isDuplicate ? 'Yes' : 'No'), $data);

    //     return $isDuplicate;
    // }

    protected function isDuplicate($data)
    {
        // Start the query scoped by the provided ack_no
        $query = Evidence::where('ack_no', $data['ack_no'])
                     ->where('url', $data['url']);

        // Add conditions to check for uniqueness of domain and ip within the same ack_no

        if (!empty($data['url'])) {
            // Check if the domain exists for the same ack_no
            $query->orWhere(function ($q) use ($data) {
                $q->where('url', $data['url']);
            });
        }

        if (!empty($data['domain'])) {
            // Check if the domain exists for the same ack_no
            $query->orWhere(function ($q) use ($data) {
                $q->where('domain', $data['domain']);
            });
        }

        if (!empty($data['ip'])) {
            // Check if the ip exists for the same ack_no
            $query->orWhere(function ($q) use ($data) {
                $q
                  ->where('ip', $data['ip']);
            });
        }

        // Execute the query to check if any duplicates exist
        $isDuplicate = $query->exists();

        // Log the result of the duplicate check for debugging purposes
        Log::info("Duplicate check result: " . ($isDuplicate ? 'Yes' : 'No'), $data);

        return $isDuplicate;
    }


    protected function isValidForType($data, $type)
    {
        $type = strtolower(trim($type));
        $typeRules = $this->getValidationRulesForType($type);

        foreach ($typeRules as $field => $isRequired) {
            if ($isRequired && empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    protected function getValidationRulesForType($type)
    {
        $rules = [
            'mobile' => [
                'url' => true,
                'category' => true,
                'ip' => false,
                'country_code' => true,
                'ticket' => true,
                'data_disclosure' => true,
                'preservation' => true,
                'evidence_type' => true,
                'ack_no' => true,

            ],
            'whatsapp' => [
                'url' => true,
                'category' => true,
                'country_code' => true,
                'ticket' => true,
                'data_disclosure' => true,
                'preservation' => true,
                'evidence_type' => true,
                'ack_no' => true
            ],
            'website' => [
                'url' => true,
                'category' => true,
                'ip' => true,
                'registrar' => true,
                'registry_details' => true,
                'ticket' => true,
                'data_disclosure' => true,
                'preservation' => true,
                'domain' => true,
                'evidence_type' => true,
                'ack_no' => true
            ],
            'others' => [
                'url' => true,
                'category' => true,
                'ticket' => true,
                'data_disclosure' => true,
                'preservation' => true,
                'domain' => true,
                'evidence_type' => true,
                'ack_no' => true
            ],
        ];

        return $rules[$type] ?? $rules['others'];
    }


    protected function logErrors()
    {
        foreach ($this->errors as $error) {
            Log::error($error);
        }

    }

    protected function convertUrlToString($url)
    {
        return $this->convertToString($url);
    }

    protected function convertAcknoToString($ackNo)
    {
        return $this->convertToString($ackNo);
    }


    public function getErrors()
    {
        return $this->errors;
    }
}
