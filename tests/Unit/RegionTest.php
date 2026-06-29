<?php

namespace Tests\Unit;

use App\Models\Region;
use App\Models\Member;
use App\Models\FamilyCard;
use Tests\TestCase;

class RegionTest extends TestCase
{
    function test_has_many_members()
    {
        $region = Region::factory()->create();
        Member::factory()->count(3)->create(['region_id' => $region->id]);
        $this->assertCount(3, $region->members);
    }

    function test_has_many_family_cards_through_members()
    {
        $region = Region::factory()->create();
        $card1 = FamilyCard::factory()->create();
        $card2 = FamilyCard::factory()->create();
        Member::factory()->create(['region_id' => $region->id, 'family_card_id' => $card1->id]);
        Member::factory()->create(['region_id' => $region->id, 'family_card_id' => $card2->id]);
        $this->assertCount(2, $region->familyCards);
    }
}
