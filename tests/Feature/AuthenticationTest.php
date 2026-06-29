<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesUsers;

    function test_login_page_is_accessible()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    function test_root_redirects_to_admin()
    {
        $response = $this->get('/');
        $response->assertRedirect('/admin');
    }

    function test_authenticated_user_can_access_admin()
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user, 'web');

        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    function test_region_admin_can_access_admin()
    {
        $user = $this->createRegionAdmin();
        $this->actingAs($user, 'web');

        $response = $this->get('/admin');
        $response->assertStatus(200);
    }
}
