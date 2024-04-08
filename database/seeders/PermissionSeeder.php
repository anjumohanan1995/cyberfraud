<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = $this->getPermissions();
        foreach ($permissions as $val) {
            Permission::create($val);
        }
    }

    public function getPermissions()
    {
        return [
            
             ['name' => 'user-management', 'sub_permission' => json_encode([
                "users",
                "add-role",
                "permission"

                ]
            )],

           
        ];
    }
}
