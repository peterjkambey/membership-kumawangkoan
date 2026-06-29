<?php

namespace Tests\Feature;

use App\Models\FamilyCard;
use App\Models\PaymentGatewayTransaction;
use App\Services\XenditService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditServiceTest extends TestCase
{
    private XenditService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(XenditService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Dry-Run Tests (mode default: disabled)
    |--------------------------------------------------------------------------
    */

    function test_create_va_dry_run_returns_fake_data()
    {
        Config::set('xendit.enabled', false);

        $result = $this->service->createVA([
            'external_id' => 'INV-001',
            'bank_code' => 'BCA',
            'amount' => 20000,
            'name' => 'Test Member',
        ]);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dry_run']);
        $this->assertEquals('INV-001', $result['data']['external_id']);
        $this->assertEquals('BCA', $result['data']['bank_code']);
        $this->assertEquals('PENDING', $result['data']['status']);
    }

    function test_create_qris_dry_run_returns_fake_data()
    {
        Config::set('xendit.enabled', false);

        $result = $this->service->createQRIS([
            'external_id' => 'INV-002',
            'amount' => 50000,
        ]);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['dry_run']);
        $this->assertEquals('PENDING', $result['data']['status']);
        $this->assertNotNull($result['data']['qr_string']);
    }

    /*
    |--------------------------------------------------------------------------
    | Simulation Tests (endpoint test via simulateVA / simulateQRIS)
    |--------------------------------------------------------------------------
    */

    function test_simulate_va_returns_valid_structure()
    {
        $result = $this->service->simulateVA([
            'external_id' => 'SIM-001',
            'bank_code' => 'BNI',
            'amount' => 100000,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('SIM-001', $result['data']['external_id']);
        $this->assertEquals('PENDING', $result['data']['status']);
        $this->assertStringContainsString('8800', $result['data']['account_number']);
    }

    function test_simulate_qris_returns_valid_structure()
    {
        $result = $this->service->simulateQRIS([
            'external_id' => 'SIM-002',
            'amount' => 25000,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('PENDING', $result['data']['status']);
        $this->assertStringContainsString('XENDITSIMULASI', $result['data']['qr_string']);
    }

    /*
    |--------------------------------------------------------------------------
    | Real API Tests (with HTTP Fake)
    |--------------------------------------------------------------------------
    */

    function test_create_va_real_api_with_http_fake()
    {
        Config::set('xendit.enabled', true);
        Config::set('xendit.api_key', 'test-key');

        Http::fake([
            'api.xendit.co/*' => Http::response([
                'id' => 'xendit-va-001',
                'external_id' => 'INV-003',
                'bank_code' => 'BCA',
                'account_number' => '8800123456',
                'status' => 'PENDING',
            ], 200),
        ]);

        $result = $this->service->createVA([
            'external_id' => 'INV-003',
            'bank_code' => 'BCA',
            'amount' => 20000,
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('8800', $result['data']['account_number']);
    }

    function test_create_qris_real_api_with_http_fake()
    {
        Config::set('xendit.enabled', true);
        Config::set('xendit.api_key', 'test-key');

        Http::fake([
            'api.xendit.co/*' => Http::response([
                'id' => 'xendit-qris-001',
                'external_id' => 'INV-004',
                'qr_string' => '00020101021126580010IDXENDIT',
                'status' => 'PENDING',
            ], 200),
        ]);

        $result = $this->service->createQRIS([
            'external_id' => 'INV-004',
            'amount' => 30000,
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('PENDING', $result['data']['status']);
    }

    function test_create_va_handles_api_error()
    {
        Config::set('xendit.enabled', true);
        Config::set('xendit.api_key', 'test-key');

        Http::fake([
            'api.xendit.co/*' => Http::response(['message' => 'API key invalid'], 401),
        ]);

        $result = $this->service->createVA([
            'external_id' => 'INV-005',
            'bank_code' => 'BCA',
            'amount' => 20000,
        ]);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['error']);
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook & Transaction Model Tests
    |--------------------------------------------------------------------------
    */

    function test_webhook_callback_marks_transaction_as_paid()
    {
        $card = FamilyCard::factory()->create();
        $transaction = PaymentGatewayTransaction::factory()->create([
            'external_id' => 'INV-WEB-001',
            'family_card_id' => $card->id,
            'amount' => 50000,
            'channel' => 'VA',
            'status' => 'PENDING',
        ]);

        $this->assertTrue($transaction->isPending);

        $response = $this->postJson('/api/xendit/webhook', [
            'external_id' => 'INV-WEB-001',
            'status' => 'PAID',
            'paid_amount' => 50000,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_gateway_transactions', [
            'id' => $transaction->id,
            'status' => 'PAID',
        ]);
    }

    function test_webhook_ignores_non_paid_status()
    {
        $transaction = PaymentGatewayTransaction::factory()->create([
            'external_id' => 'INV-WEB-002',
            'status' => 'PENDING',
        ]);

        $response = $this->postJson('/api/xendit/webhook', [
            'external_id' => 'INV-WEB-002',
            'status' => 'EXPIRED',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_gateway_transactions', [
            'id' => $transaction->id,
            'status' => 'PENDING',
        ]);
    }

    function test_webhook_returns_404_for_unknown_transaction()
    {
        $response = $this->postJson('/api/xendit/webhook', [
            'external_id' => 'NONEXISTENT',
            'status' => 'PAID',
            'paid_amount' => 10000,
        ]);

        $response->assertStatus(404);
    }

    function test_transaction_model_relations()
    {
        $card = FamilyCard::factory()->create();
        $tx = PaymentGatewayTransaction::factory()->create([
            'family_card_id' => $card->id,
        ]);

        $this->assertInstanceOf(FamilyCard::class, $tx->familyCard);
        $this->assertEquals($card->id, $tx->familyCard->id);
    }

    function test_transaction_scopes()
    {
        PaymentGatewayTransaction::factory()->create(['status' => 'PENDING']);
        PaymentGatewayTransaction::factory()->create(['status' => 'PAID']);
        PaymentGatewayTransaction::factory()->create(['status' => 'EXPIRED']);

        $this->assertCount(1, PaymentGatewayTransaction::pending()->get());
        $this->assertCount(1, PaymentGatewayTransaction::paid()->get());
    }
}
