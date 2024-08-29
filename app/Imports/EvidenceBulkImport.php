<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Evidence;
use App\Models\EvidenceType;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class EvidenceBulkImport implements ToCollection, WithStartRow
{
    public $newRecordsInserted = false;
    public $errors = []; // Array to store error messages

    /**
     * Start row for the import, skipping the header.
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Handle the collection of rows from the import file.
     *
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            $rowIndex = $index + $this->startRow(); // Correctly adjust row index

            // Clean input values
            $evidenceType = strtolower(trim($row[10]));
            $categoryName = strtolower(trim($row[11]));

            // Validate empty or missing category name
            if (empty($categoryName)) {
                $this->errors[] = "Row {$rowIndex}: Category is missing.";
                continue;
            }

            // Check if category exists
            if (!$this->categoryExists($categoryName)) {
                $this->errors[] = "Row {$rowIndex}: Invalid category.";
                continue;
            }

            // Validate empty or missing evidence type
            if (empty($evidenceType)) {
                $this->errors[] = "Row {$rowIndex}: Evidence type is missing.";
                continue;
            }

            // Check if evidence type exists
            if (!$this->evidenceTypeExists($evidenceType)) {
                $this->errors[] = "Row {$rowIndex}: Invalid evidence type.";
                continue;
            }

            $data = [
                'ack_no' => $this->convertAcknoToString($row[0]),
                'url' => $this->convertUrlToString($row[1]),
                'remarks' => $row[6] ?? null,
                'category' => $categoryName,
                'ticket' => $row[7],
                'data_disclosure' => $row[8],
                'preservation' => $row[9],
                'reported_status' => 'active',
                'evidence_type' => $evidenceType,
                'evidence_type_id' => '2', // Adjust if needed
            ];

            // Additional checks based on evidence type
            switch (strtolower($evidenceType)) {
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

            // Validate for mandatory fields based on evidence type
            if (!$this->isValidForType($data, strtolower($evidenceType))) {
                $this->errors[] = "Row {$rowIndex}: Missing mandatory fields for evidence type.";
                continue;
            }

            // Log data before saving
            Log::info("Processing Row {$rowIndex}", $data);

            // Check for duplicates before inserting
            if (!$this->isDuplicate($data)) {
                // Save valid record to the database
                Evidence::create($data);
                $this->newRecordsInserted = true;
            }
        }

        // Log errors if any
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                Log::error($error); // Log errors to error log
            }
        }
    }

    // Helper method to check if category exists
    protected function categoryExists($category)
    {
        return Category::where('name', 'regex', "/^" . preg_quote($category) . "$/i")->exists();
    }

    // Helper method to check if evidence type exists
    protected function evidenceTypeExists($evidenceType)
    {
        return EvidenceType::where('name', strtolower($evidenceType))->exists();
    }

    // Helper method to validate mandatory fields based on evidence type
    protected function isValidForType($data, $evidenceType)
    {
        switch ($evidenceType) {
            case 'mobile':
            case 'whatsapp':
                return !empty($data['ticket']);
            case 'website':
                return !empty($data['domain']) && !empty($data['ip']) && !empty($data['registrar']) && !empty($data['registry_details']);
            case 'facebook':
                return !empty($data['domain']);
            default:
                return !empty($data['domain']);
        }
    }

    // Example helper to check for duplicates
    protected function isDuplicate($data)
    {
        $query = Evidence::where('url', $data['url']);

        // Add $or conditions dynamically based on available data fields
        $orConditions = [];

        if (!empty($data['domain'])) {
            $orConditions[] = ['domain' => $data['domain']];
        }
        if (!empty($data['ip'])) {
            $orConditions[] = ['ip' => $data['ip']];
        }
        if (!empty($data['registrar'])) {
            $orConditions[] = ['registrar' => $data['registrar']];
        }

        // Apply the $or conditions if they exist
        if (!empty($orConditions)) {
            $query->where(function ($q) use ($orConditions) {
                foreach ($orConditions as $condition) {
                    $q->orWhere($condition);
                }
            });
        }

        return $query->exists();
    }

    /**
     * Convert acknowledgment number to a string if it's numeric.
     *
     * @param mixed $ack_no
     * @return string
     */
    protected function convertAcknoToString($ack_no)
    {
        return is_numeric($ack_no) ? (string) $ack_no : $ack_no;
    }

    /**
     * Convert URL to a string if it's numeric.
     *
     * @param mixed $url
     * @return string
     */
    protected function convertUrlToString($url)
    {
        return is_numeric($url) ? (string) $url : $url;
    }

    /**
     * Get the errors collected during the import process.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
