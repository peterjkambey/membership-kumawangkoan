<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayTransaction;
use App\Services\PaymentAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Callback dari Xendit — dipanggil pas pembayaran sukses.
     * Endpoint: POST /api/xendit/webhook
     */
    public function callback(Request $request)
    {
        $payload = $request->all();
        $token = $request->header('x-callback-token');
        $expectedToken = config('xendit.webhook_verification_token');

        Log::info('[XENDIT WEBHOOK] Received', ['payload' => $payload]);

        // Verifikasi token (dry-test: lewatin kalo token kosong)
        if (!empty($expectedToken) && $token !== $expectedToken) {
            Log::warning('[XENDIT WEBHOOK] Invalid token');
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $externalId = $payload['external_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$externalId || !$status) {
            return response()->json(['error' => 'Missing fields'], 400);
        }

        if ($status !== 'PAID') {
            return response()->json(['message' => 'Ignored non-PAID status']);
        }

        $transaction = PaymentGatewayTransaction::where('external_id', $externalId)->first();

        if (!$transaction) {
            Log::warning('[XENDIT WEBHOOK] Transaction not found', ['external_id' => $externalId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->isPaid) {
            return response()->json(['message' => 'Already paid']);
        }

        $paidAmount = (float) ($payload['paid_amount'] ?? $transaction->amount);

        // Update transaction
        $transaction->update([
            'status' => 'PAID',
            'paid_amount' => $paidAmount,
            'paid_at' => now(),
            'gateway_response' => $payload,
        ]);

        // Allokasikan pembayaran ke tagihan lewat PaymentAllocator
        if ($transaction->family_card_id) {
            $card = $transaction->familyCard;
            if ($card) {
                $allocator = app(PaymentAllocator::class);
                $allocator->allocate($card, $paidAmount, [
                    'payment_date' => now()->toDateString(),
                    'payment_method' => $transaction->channel === 'QRIS' ? 'qris' : 'transfer',
                    'reference_number' => $transaction->external_id,
                    'notes' => "Pembayaran via {$transaction->channel} (Xendit)",
                ]);
            }
        }

        Log::info('[XENDIT WEBHOOK] Payment processed', [
            'external_id' => $externalId,
            'amount' => $paidAmount,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Simulasi callback — buat test dari Filament/admin tanpa Xendit beneran.
     */
    public function simulateCallback(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $transaction = PaymentGatewayTransaction::findOrFail($transactionId);

        // Rebuild fake payload
        $fakePayload = [
            'external_id' => $transaction->external_id,
            'status' => 'PAID',
            'paid_amount' => (float) $transaction->amount,
        ];

        // Forward ke callback handler
        $request->merge($fakePayload);

        return $this->callback($request);
    }
}
