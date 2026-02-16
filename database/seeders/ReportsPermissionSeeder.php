<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create custom report permissions
        $permissions = [
            'view_reports',
            'view_bac_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Assign to super_admin role
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Optionally assign to BAC ADMIN role
        $bacAdmin = Role::where('name', 'BAC ADMIN')->first();
        if ($bacAdmin) {
            $bacAdmin->givePermissionTo($permissions);
        }

        $this->command->info('Reports permissions created and assigned successfully!');
    }
}
