<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name' => 'User Management',
                'sub_permission' => json_encode([
                    '1' => 'Add User',
                    '2' => 'Edit User',
                    '3' => 'Delete User',
                ]),
            ],
            [
                'name' => 'Role Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Role',
                    '2' => 'Edit Role',
                    '3' => 'Delete Role',
                    '4' => 'Show Permissions',
                ]),
            ],
            [
                'name' => 'Permission Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Permission',
                    '2' => 'Edit Permission',
                    '3' => 'Delete Permission',
                    '4' => 'Show Subpermissions',
                    '5' => 'Add Sub Permission',
                    '6' => 'Delete Sub Permission',
                ]),
            ],
            [
                'name' => 'Upload NCRP Case Data Management',
                'sub_permission' => json_encode([
                    '1' => 'Upload Primary Data',
                    '2' => 'Upload Bank Action',
                ]),
            ],
            [
                'name' => 'Upload Other Case Data Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'NCRP Case Data Management',
                'sub_permission' => json_encode([
                    '1' => 'Show Detail Page',
                    '2' => 'Self Assign',
                    '3' => 'Activate / Deactivate',
                ]),
            ],
            [
                'name' => 'Source Type Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Source Type',
                    '2' => 'Add Category',
                    '3' => 'Add Subcategory',
                    '4' => 'Add Profession',
                    '5' => 'Upload registrar',
                    '6' => 'Edit source Type',
                    '7' => 'Delete Source Type',
                ]),
            ],
            [
                'name' => 'Notice Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Evidence Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Mule Account Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Reports Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Evidence Type Management',
                'sub_permission' => json_encode([]),
            ],
        ];

        foreach ($permissions as $permission) {
            DB::collection('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'sub_permission' => $permission['sub_permission'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
