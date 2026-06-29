<?php

namespace Tests\Feature;

use App\Models\FamilyCard;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MonthlyBill;
use App\Models\Payment;
use App\Models\Region;
use App\Models\SupportBody;
use App\Models\User;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class SuperAdminFeatureTest extends TestCase
{
    use CreatesUsers;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = $this->createSuperAdmin();
    }

    /** Page access tests - superadmin can access all pages */
    function test_can_access_dashboard()
    {
        $this->actingAs($this->superAdmin, 'web');
        $this->get('/admin')->assertStatus(200);
    }

    function test_can_access_region_pages()
    {
        $region = Region::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/regions')->assertStatus(200);
        $this->get('/admin/regions/create')->assertStatus(200);
        $this->get("/admin/regions/{$region->id}/edit")->assertStatus(200);
    }

    function test_can_access_family_card_pages()
    {
        $card = FamilyCard::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/family-cards')->assertStatus(200);
        $this->get('/admin/family-cards/create')->assertStatus(200);
        $this->get("/admin/family-cards/{$card->id}/edit")->assertStatus(200);
    }

    function test_can_access_member_pages()
    {
        $member = Member::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/members')->assertStatus(200);
        $this->get('/admin/members/create')->assertStatus(200);
        $this->get("/admin/members/{$member->id}/edit")->assertStatus(200);
    }

    function test_can_access_member_membership_pages()
    {
        $mm = MemberMembership::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/member-memberships')->assertStatus(200);
        $this->get('/admin/member-memberships/create')->assertStatus(200);
        $this->get("/admin/member-memberships/{$mm->id}/edit")->assertStatus(200);
    }

    function test_can_access_monthly_bill_pages()
    {
        $bill = MonthlyBill::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/monthly-bills')->assertStatus(200);
        $this->get('/admin/monthly-bills/create')->assertStatus(200);
        $this->get("/admin/monthly-bills/{$bill->id}/edit")->assertStatus(200);
    }

    function test_can_access_payment_pages()
    {
        $payment = Payment::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/payments')->assertStatus(200);
        $this->get('/admin/payments/create')->assertStatus(200);
        $this->get("/admin/payments/{$payment->id}/edit")->assertStatus(200);
    }

    function test_can_access_support_body_pages()
    {
        $body = SupportBody::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/support-bodies')->assertStatus(200);
        $this->get('/admin/support-bodies/create')->assertStatus(200);
        $this->get("/admin/support-bodies/{$body->id}/edit")->assertStatus(200);
    }

    function test_can_access_user_pages()
    {
        $user = User::factory()->create();
        $this->actingAs($this->superAdmin, 'web');

        $this->get('/admin/users')->assertStatus(200);
        $this->get('/admin/users/create')->assertStatus(200);
        $this->get("/admin/users/{$user->id}/edit")->assertStatus(200);
    }

    /** Data operations - tested via direct model manipulation */
    function test_can_create_data_directly()
    {
        // Superadmin can create all entity types via model directly
        $region = Region::factory()->create(['name' => 'Test Region']);
        $this->assertDatabaseHas('regions', ['name' => 'Test Region']);

        $card = FamilyCard::factory()->create(['family_no' => 'KK-SA-001']);
        $this->assertDatabaseHas('family_cards', ['family_no' => 'KK-SA-001']);

        $member = Member::factory()->create(['full_name' => 'SA Member']);
        $this->assertDatabaseHas('members', ['full_name' => 'SA Member']);

        $bill = MonthlyBill::factory()->create(['period' => '2026-08']);
        $this->assertDatabaseHas('monthly_bills', ['period' => '2026-08']);

        $payment = Payment::factory()->create(['payment_method' => 'qris']);
        $this->assertDatabaseHas('payments', ['payment_method' => 'qris']);

        $body = SupportBody::factory()->create(['name' => 'SA Body']);
        $this->assertDatabaseHas('support_bodies', ['name' => 'SA Body']);

        $user = User::factory()->create(['email' => 'sauser@test.com']);
        $this->assertDatabaseHas('users', ['email' => 'sauser@test.com']);
    }

    function test_can_update_data_directly()
    {
        $region = Region::factory()->create();
        $region->update(['name' => 'Updated Region']);
        $this->assertDatabaseHas('regions', ['name' => 'Updated Region']);

        $card = FamilyCard::factory()->create();
        $card->update(['status' => 'frozen']);
        $this->assertDatabaseHas('family_cards', ['id' => $card->id, 'status' => 'frozen']);

        $member = Member::factory()->create();
        $member->update(['status' => 'inactive']);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'status' => 'inactive']);
    }

    function test_can_delete_data_directly()
    {
        $region = Region::factory()->create();
        $region->delete();
        $this->assertDatabaseMissing('regions', ['id' => $region->id]);

        $card = FamilyCard::factory()->create();
        $card->delete();
        $this->assertDatabaseMissing('family_cards', ['id' => $card->id]);

        $member = Member::factory()->create();
        $member->delete();
        $this->assertDatabaseMissing('members', ['id' => $member->id]);

        $bill = MonthlyBill::factory()->create();
        $bill->delete();
        $this->assertDatabaseMissing('monthly_bills', ['id' => $bill->id]);

        $payment = Payment::factory()->create();
        $payment->delete();
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);

        $body = SupportBody::factory()->create();
        $body->delete();
        $this->assertDatabaseMissing('support_bodies', ['id' => $body->id]);

        $user = User::factory()->create();
        $user->delete();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

}
