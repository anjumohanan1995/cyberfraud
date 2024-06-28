<?php

namespace App\Imports;

use App\Models\Registrar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RegistrarImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $registrarNumber;

    public function __construct($registrarNumber)
    {
        // dd($registrarNumber);
        $this->registrarNumber = $registrarNumber;
    }

    public function collection(Collection $rows)
    {
        $importedRegistrarNumbers = [];

        foreach ($rows as $row) {
            // Check if registrar number is already imported
            if (!in_array($this->registrarNumber, $importedRegistrarNumbers)) {
                $importedRegistrarNumbers[] = $this->registrarNumber;

                // Process emails
                $emailString = $row['email_id'];
                $emails = explode(' ', $emailString);
                $emails = array_filter($emails); // Remove empty email entries

                // Create new Registrar entry
                Registrar::create([
                    'registrar_number' => $this->registrarNumber,
                    'registrar' => $row['registrar'],
                    'contact_information' => $row['contact_information'],
                    'email_id' => $emails, // Store emails as an array
                    'portal_link' => $row['portal_link']
                ]);
            }
        }
    }


    public function rules(): array
    {
        // Define validation rules for the imported data
        return [
                // Example validation rules (modify as per your requirements)
                // 'registrar' => 'required|string|max:255',
                // 'contact_information' => 'required|string|max:255',
                // 'email_id' => 'nullable|email',
                // 'portal_link' => 'nullable|url',
        ];
    }
}
