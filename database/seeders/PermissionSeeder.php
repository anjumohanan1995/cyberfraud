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
                    '4' => 'Show Transaction Type Filter',
                    '5' => 'Show Bank/Wallet/Merchant/Insurance Filter',
                    '6' => 'Show Filled by(within 24 hrs) Filter',
                    '7' => 'Show Complaint Reported Filter',
                    '8' => 'Show FIR Lodge Filter',
                    '9' => 'Show Status Filter',
                    '10' => 'Show Search by Filter',
                    '11' => 'Show Sub category Filter'
                ]),
            ],
            [
                'name' => 'Other Case Data Management',
                'sub_permission' => json_encode([
                    '1' => 'Show Others Self Assign Button',
                ]),
            ],
            [
                'name' => 'Source Type Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Source Type',
                    '2' => 'Add Category',
                    '3' => 'Add Subcategory',
                    '4' => 'Add Profession',
                    '5' => 'Add Modus',
                    '6' => 'Upload registrar',
                    '7' => 'Edit source Type',
                    '8' => 'Delete Source Type',
                ]),
            ],
            [
                'name' => 'Notice Management',
                'sub_permission' => json_encode([
                    '1' => 'Against Evidence Permission',
                    '2' => 'Show Evidence Source Type Filter',
                    '3' => 'Show Evidence Type Filter',
                    '4' => 'Show Notice Status Filter',
                    '5' => 'Show Notice Type Filter',
                    '6' => 'Generate Token',
                    '7' => 'Against Bank Management',
                    '8' => 'Against Mule Account Management',
                    '9' => 'Notice View',
                    '10'=> 'Approve Button'
                ]),
            ],
            [
                'name' => 'Evidence Management',
                'sub_permission' => json_encode([
                    '1' => 'Show NCRP mail Merge',
                    '2' => 'Show Other mail Merge',
                    '3' => 'Show NCRP Evidence Type Filter',
                    '4' => 'Show Others Evidence Type Filter',
                    '5' => 'View / Update NCRP Evidence Status',
                    '6' => 'View / Update Other Evidence Status',
                ]),
            ],
            [
                'name' => 'Mule Account Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Reports Management',
                'sub_permission' => json_encode([
                    '1' => 'NCRP CSV Download',
                    '2' => 'Other CSV Download',
                    '3' => 'NCRP Excel Download',
                    '4' => 'Other Excel Download',
                ]),
            ],
            [
                'name' => 'Evidence Type Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Evidence Type',
                    '2' => 'Edit Evidence Type',
                    '3' => 'Delete Evidence Type',
                ]),
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
