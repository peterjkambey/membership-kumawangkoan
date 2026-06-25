<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Wilayah
            'view_region', 'create_region', 'edit_region', 'delete_region',
            // Badan Pembantu
            'view_support_body', 'create_support_body', 'edit_support_body', 'delete_support_body',
            // Kartu Keluarga
            'view_family_card', 'create_family_card', 'edit_family_card', 'delete_family_card',
            // Anggota
            'view_member', 'create_member', 'edit_member', 'delete_member',
            // Keanggotaan
            'view_member_membership', 'create_member_membership', 'edit_member_membership', 'delete_member_membership',
            // Tagihan
            'view_monthly_bill', 'create_monthly_bill', 'edit_monthly_bill', 'delete_monthly_bill',
            // Pembayaran
            'view_payment', 'create_payment', 'edit_payment', 'delete_payment', 'verify_payment',
            // Pengguna
            'view_user', 'create_user', 'edit_user', 'delete_user',
            // Laporan
            'view_report', 'export_report',
            // Dashboard
            'view_dashboard',
            // Notifikasi
            'view_notification', 'send_notification',
            // E-Card
            'view_ecard', 'generate_ecard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - all permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Finance Admin
        $financeAdmin = Role::create(['name' => 'finance-admin']);
        $financeAdmin->givePermissionTo([
            'view_monthly_bill', 'create_monthly_bill', 'edit_monthly_bill',
            'view_payment', 'create_payment', 'edit_payment', 'verify_payment',
            'view_report', 'export_report',
            'view_dashboard',
            'view_notification', 'send_notification',
            'view_member', 'view_family_card',
        ]);

        // Region Admin
        $regionAdmin = Role::create(['name' => 'region-admin']);
        $regionAdmin->givePermissionTo([
            'view_region',
            'view_family_card', 'create_family_card', 'edit_family_card',
            'view_member', 'create_member', 'edit_member',
            'view_member_membership',
            'view_monthly_bill',
            'view_payment', 'create_payment',
            'view_dashboard',
            'view_ecard', 'generate_ecard',
            'view_notification', 'send_notification',
        ]);

        // Body Admin
        $bodyAdmin = Role::create(['name' => 'body-admin']);
        $bodyAdmin->givePermissionTo([
            'view_support_body', 'create_support_body', 'edit_support_body',
            'view_member',
            'view_dashboard',
            'view_ecard',
        ]);

        // Viewer
        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view_region',
            'view_support_body',
            'view_family_card',
            'view_member',
            'view_member_membership',
            'view_monthly_bill',
            'view_payment',
            'view_dashboard',
            'view_report',
            'view_ecard',
        ]);
    }
}
