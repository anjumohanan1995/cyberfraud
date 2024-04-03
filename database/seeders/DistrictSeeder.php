<?php

namespace Database\Seeders;
use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MongoDB\Client;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $districts = [
            'Alappuzha',
            'Ernakulam',
            'Idukki',
            'Kannur',
            'Kasaragod',
            'Kollam',
            'Kottayam',
            'Kozhikode',
            'Malappuram',
            'Palakkad',
            'Pathanamthitta',
            'Thiruvananthapuram',
            'Thrissur',
            'Wayanad',
        ];

        // foreach ($districts as $district) {
        //     District::create([
        //         'name' => $district,
        //     ]);
        // }

        $mongoClient = new Client("mongodb://localhost:27017");
        $db = $mongoClient->cyber; // Replace 'your_database_name' with your MongoDB database name
        $collection = $db->districts;

        foreach ($districts as $district) {
            $collection->insertOne(['name' => $district]);
        }
       
    }
}
