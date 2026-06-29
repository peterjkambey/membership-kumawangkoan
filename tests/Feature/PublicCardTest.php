<?php

namespace Tests\Feature;

use App\Models\Member;
use Tests\Helpers\CreatesUsers;
use Tests\TestCase;

class PublicCardTest extends TestCase
{
    use CreatesUsers;

    function test_public_card_page_is_accessible()
    {
        $member = Member::factory()->create();
        $response = $this->get("/card/{$member->id}");
        $response->assertStatus(200);
        $response->assertSee($member->full_name);
    }

    function test_public_card_api_returns_json()
    {
        $member = Member::factory()->create();
        $response = $this->getJson("/api/card/{$member->id}");
        $response->assertStatus(200)
            ->assertJson([
                'name' => $member->full_name,
                'membership_number' => $member->membership_number,
                'status' => $member->status,
            ]);
    }

    function test_public_card_api_returns_404_for_nonexistent_member()
    {
        $response = $this->getJson('/api/card/99999');
        $response->assertStatus(404);
    }

    function test_public_card_page_returns_404_for_nonexistent_member()
    {
        $response = $this->get('/card/99999');
        $response->assertStatus(404);
    }

    function test_public_card_api_includes_region_and_family_data()
    {
        $member = Member::factory()->create();
        $response = $this->getJson("/api/card/{$member->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'name', 'membership_number', 'region', 'status',
                'family_no', 'family_role', 'join_date',
            ]);
    }
}
