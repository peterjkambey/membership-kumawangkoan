<?php

namespace Tests\Unit;

use App\Models\Benefit;
use App\Models\Member;
use Tests\TestCase;

class BenefitTest extends TestCase
{
    function test_can_create_benefit()
    {
        $benefit = Benefit::factory()->create([
            'code' => 'TEST_BENEFIT',
            'name' => 'Test Benefit',
        ]);

        $this->assertDatabaseHas('benefits', ['code' => 'TEST_BENEFIT']);
    }

    function test_benefit_belongs_to_many_members()
    {
        $benefit = Benefit::factory()->create();
        $members = Member::factory()->count(3)->create();

        $benefit->members()->attach($members->pluck('id'), [
            'status' => 'eligible',
        ]);

        $this->assertCount(3, $benefit->members);
    }

    function test_member_belongs_to_many_benefits()
    {
        $member = Member::factory()->create();
        $benefits = Benefit::factory()->count(2)->create();

        $member->benefits()->attach($benefits->pluck('id'), [
            'status' => 'granted',
            'granted_at' => now(),
        ]);

        $this->assertCount(2, $member->benefits);
    }

    function test_scope_active_filters_active_benefits()
    {
        Benefit::factory()->count(3)->create(['is_active' => true]);
        Benefit::factory()->create(['is_active' => false]);

        $this->assertCount(3, Benefit::active()->get());
    }

    function test_benefit_has_pivot_data()
    {
        $member = Member::factory()->create();
        $benefit = Benefit::factory()->create();

        $member->benefits()->attach($benefit->id, [
            'status' => 'granted',
            'granted_by' => null,
            'granted_at' => now(),
            'expires_at' => now()->addYear(),
            'notes' => 'Test grant',
        ]);

        $pivot = $member->benefits->first()->pivot;
        $this->assertEquals('granted', $pivot->status);
        $this->assertEquals('Test grant', $pivot->notes);
    }
}
