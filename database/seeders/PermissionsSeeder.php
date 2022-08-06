<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::create([
            'name'     => 'superAdmin'
        ]);

        $distributionCenter = Role::create([
            'name'     => 'distributionCenter'
        ]);


        $warehouseManger = Role::create([
            'name'     => 'warehouseManger'
        ]);

        $Accountant = Role::create([
            'name'     => 'warehouseManger'
        ]);
    }
}
