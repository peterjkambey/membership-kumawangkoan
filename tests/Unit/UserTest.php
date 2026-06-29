<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Region;
use App\Models\Payment;
use Tests\TestCase;

class UserTest extends TestCase
{
    function test_belongs_to_region()
    {
        $region = Region::factory()->create();
        $user = User::factory()->create(['region_id' => $region->id]);
        $this->assertInstanceOf(Region::class, $user->region);
        $this->assertEquals($region->id, $user->region->id);
    }

    function test_can_verify_many_payments()
    {
        $user = User::factory()->create();
        Payment::factory()->count(3)->create(['verified_by' => $user->id]);
        $this->assertCount(3, $user->verifiedPayments);
    }

    function test_has_roles_via_spatie()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->assertTrue($user->hasRole('super-admin'));
    }

    function test_implements_filament_user()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(\Filament\Models\Contracts\FilamentUser::class, $user);
    }
}
