<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use CreatesUsers;

    function test_all_roles_are_created_by_seeder()
    {
        foreach (['super-admin', 'finance-admin', 'region-admin', 'body-admin', 'viewer'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role]);
        }
    }

    function test_super_admin_has_all_permissions()
    {
        $role = Role::findByName('super-admin');
        $this->assertEquals(Permission::count(), $role->permissions->count());
    }

    function test_region_admin_has_limited_permissions()
    {
        $role = Role::findByName('region-admin');
        $this->assertTrue($role->hasPermissionTo('view_member'));
        $this->assertTrue($role->hasPermissionTo('create_member'));
        $this->assertTrue($role->hasPermissionTo('edit_member'));
        $this->assertFalse($role->hasPermissionTo('delete_member'));
        $this->assertFalse($role->hasPermissionTo('view_user'));
        $this->assertFalse($role->hasPermissionTo('view_report'));
    }

    function test_viewer_has_readonly_permissions()
    {
        $role = Role::findByName('viewer');
        $this->assertTrue($role->hasPermissionTo('view_member'));
        $this->assertTrue($role->hasPermissionTo('view_family_card'));
        $this->assertTrue($role->hasPermissionTo('view_monthly_bill'));
        $this->assertFalse($role->hasPermissionTo('create_member'));
        $this->assertFalse($role->hasPermissionTo('delete_family_card'));
    }

    function test_user_can_be_assigned_super_admin_role()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->assertTrue($user->hasRole('super-admin'));
        $this->assertTrue($user->can('view_member'));
        $this->assertTrue($user->can('delete_user'));
    }

    function test_user_can_be_assigned_multiple_roles()
    {
        $user = User::factory()->create();
        $user->assignRole('region-admin', 'body-admin');
        $this->assertTrue($user->hasRole('region-admin'));
        $this->assertTrue($user->hasRole('body-admin'));
    }

    function test_all_permissions_exist()
    {
        $perms = [
            'view_region', 'create_region', 'edit_region', 'delete_region',
            'view_support_body', 'create_support_body', 'edit_support_body', 'delete_support_body',
            'view_family_card', 'create_family_card', 'edit_family_card', 'delete_family_card',
            'view_member', 'create_member', 'edit_member', 'delete_member',
            'view_member_membership', 'create_member_membership', 'edit_member_membership', 'delete_member_membership',
            'view_monthly_bill', 'create_monthly_bill', 'edit_monthly_bill', 'delete_monthly_bill',
            'view_payment', 'create_payment', 'edit_payment', 'delete_payment', 'verify_payment',
            'view_user', 'create_user', 'edit_user', 'delete_user',
            'view_report', 'export_report', 'view_dashboard',
            'view_notification', 'send_notification', 'view_ecard', 'generate_ecard',
        ];
        foreach ($perms as $perm) {
            $this->assertDatabaseHas('permissions', ['name' => $perm]);
        }
    }
}
