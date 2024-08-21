<?php

namespace App\Imports;

use App\Models\Registrar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class RegistrarImport implements ToCollection, WithHeadingRow, WithValidation
{
    // public function startRow(): int
    // {
    //     return 2; // Start from the third row (skipping description and header)
    // }

    public function collection(Collection $rows)
    {
        $uniqueRows = $rows->unique(function ($row) {
            return $row['registrar'] . $row['contact_information'] . $row['email_id'] . $row['portal_link'];
        });

        foreach ($uniqueRows as $row) {
            $emailString = $row['email_id'];
            $emails = explode(' ', $emailString);
            $emails = array_filter($emails); // Remove empty email entries

            Registrar::updateOrCreate(
                [
                    'registrar' => $row['registrar'],
                    'contact_information' => $row['contact_information'],
                ],
                [
                    'email_id' => $emails, // Store emails as an array
                    'portal_link' => $row['portal_link']
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            'registrar' => 'required|string|max:255',
            'contact_information' => 'required|string|max:255',
            'email_id' => 'nullable|string',
            'portal_link' => 'nullable|url',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'registrar.required' => 'The registrar field is required.',
            'contact_information.required' => 'The contact information field is required.',
        ];
    }
}
