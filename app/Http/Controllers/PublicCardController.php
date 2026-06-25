<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicCardController extends Controller
{
    public function show($memberId)
    {
        $member = Member::with(['region', 'familyCard'])->findOrFail($memberId);

        return view('ecard.show', compact('member'));
    }

    public function cardData($memberId)
    {
        $member = Member::with(['region', 'familyCard'])->findOrFail($memberId);

        return response()->json([
            'name' => $member->full_name,
            'membership_number' => $member->membership_number,
            'region' => $member->region?->name,
            'status' => $member->status,
            'family_no' => $member->familyCard?->family_no,
            'family_role' => $member->family_role_label,
            'join_date' => $member->join_date?->format('d/m/Y'),
            'valid_until' => $member->activeMembership?->end_date?->format('d/m/Y'),
        ]);
    }
}
