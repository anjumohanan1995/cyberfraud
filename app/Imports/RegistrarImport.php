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
        foreach ($rows as $row) {
            // dd("hello");
            $data = [
                'registrar_number' => $this->registrarNumber,
                'name' => $row['name'],
                'address' => $row['address'],
                'contact_number' => $row['contact_number'],
                'date_of_registration' => $row['date_of_registration'],
                'expiry_date' => $row['expiry_date'],
                'web_url' => $row['web_url'],
                'ip_address' => $row['ip_address'],
                'email' => $row['email']
            ];

            Registrar::create($data);
        }
    }

    public function rules(): array
    {
        // Define validation rules for the imported data
        return [
            // You can define your validation rules here if needed
            // Example: 'name' => 'required|string|max:255'
        ];
    }
}
