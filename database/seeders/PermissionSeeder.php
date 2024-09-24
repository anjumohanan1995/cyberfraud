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
                    '3' => 'User Status',
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
                    '1' => 'Show Subpermissions',
                    // '1' => 'Add Permission',
                    // '2' => 'Edit Permission',
                    // '3' => 'Delete Permission',
                    // '5' => 'Add Sub Permission',
                    // '6' => 'Delete Sub Permission',
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
                    '11' => 'View Evidence',
                    '12' => 'Add Evidence',
                    // '11' => 'Show Sub category Filter'
                ]),
            ],
            [
                'name' => 'Other Case Data Management',
                'sub_permission' => json_encode([
                    '1' => 'Show Others Self Assign Button',
                    '2' => 'Edit Other Case Data',

                ]),
            ],
            [
                'name' => 'Masters Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Source Type',
                    // '2' => 'Edit source Type',
                    '2' => 'Delete Source Type',
                    '3' => 'Add Category',
                    '4' => 'Delete Category',
                    '5' => 'Add Subcategory',
                    '6' => 'Delete Subcategory',
                    '7' => 'Add Profession',
                    '8' => 'Delete Profession',
                    '9' => 'Add Modus',
                    '10' => 'Delete Modus',
                    '11' => 'Upload Registrar',
                    '12' => 'Add Bank',
                    '13' => 'Delete Bank',
                    '14' => 'Add Wallet',
                    '15' => 'Delete Wallet',
                    '16' => 'Add Insurance',
                    '17' => 'Delete Insurance',
                    '18' => 'Add Merchant',
                    '19' => 'Delete Merchant',

                ]),
            ],
            [
                'name' => 'Notice Management',
                'sub_permission' => json_encode([
                    '1' => 'Against Evidence Permission',
                    '2' => 'Against Bank Management',
                    '3' => 'Against Mule Account Management',
                    '4' => 'Notice View',
                    '5'=> 'Approve Button',
                    '6' => 'Edit Notice',

                    // '2' => 'Show Evidence Source Type Filter',
                    // '3' => 'Show Evidence Type Filter',
                    // '4' => 'Show Notice Status Filter',
                    // '5' => 'Show Notice Type Filter',
                    // '6' => 'Generate Token',

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
                    '7' => 'NCRP Bulk Upload',

                ]),
            ],
            [
                'name' => 'Mule Account Management',
                'sub_permission' => json_encode([]),
            ],
            [
                'name' => 'Self Assigned Casedata Management',
                'sub_permission' => json_encode([
                    '1' => 'View Self Assigned NCRP Casedata',
                    '2' => 'View Self Assigned Others Casedata'
                ]),
            ],
            [
                'name' => 'Reports Management',
                'sub_permission' => json_encode([
                    '1' => 'View Evidence Based Casedata',
                    '2' => 'NCRP CSV Download',
                    '3' => 'Other CSV Download',
                    '4' => 'NCRP Excel Download',
                    '5' => 'Other Excel Download',
                    '6' => 'View Bank Action Based Casedata',
                    '7' => 'Bank Action CSV Download',
                    '8' => 'Bank  Action Excel Download',
                    '9' => 'View Daily Bank Reports',
                    '10' => 'Daily Bank CSV Download',
                    '11' => 'Daily Bank Excel Download',
                    '12' => 'View Amount wise Report',
                    '13' => 'Amount wise CSV Download',
                    '14' => 'Amount wise Excel Download',
                ]),
            ],
            [
                'name' => 'Evidence Type Management',
                'sub_permission' => json_encode([
                    '1' => 'Add Evidence Type',
                    // '2' => 'Edit Evidence Type',
                    '2' => 'Delete Evidence Type',
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
