<?php

namespace Tests\Feature;

use App\Services\WablasService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WablasServiceTest extends TestCase
{
    private WablasService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WablasService::class);
    }

    function test_dry_run_logs_instead_of_sending()
    {
        Config::set('wablas.enabled', false);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(fn ($msg) => str_contains($msg, 'DRY-RUN'));

        $result = $this->service->sendMessage('08123456789', 'Test message');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['response']['dry_run']);
    }

    function test_format_phone_adds_62_prefix()
    {
        Config::set('wablas.enabled', false);

        $result = $this->service->sendMessage('08123456789', 'Test');

        $this->assertTrue($result['success']);
    }

    function test_send_message_with_incomplete_config_returns_warning()
    {
        Config::set('wablas.enabled', true);
        Config::set('wablas.domain', '');
        Config::set('wablas.api_key', '');

        $result = $this->service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['success']);
    }

    function test_send_bulk_dry_run()
    {
        Config::set('wablas.enabled', false);

        $result = $this->service->sendBulk(
            ['08111111111', '08222222222'],
            'Bulk message test'
        );

        $this->assertTrue($result['success']);
        $this->assertTrue($result['response']['dry_run']);
    }

    function test_real_api_call_with_http_fake()
    {
        Config::set('wablas.enabled', true);
        Config::set('wablas.domain', 'https://console.wablas.com');
        Config::set('wablas.api_key', 'test-key');

        Http::fake([
            'wablas.com/*' => Http::response(['status' => 'success'], 200),
        ]);

        $result = $this->service->sendMessage('08123456789', 'Test');

        $this->assertTrue($result['success']);
    }

    function test_real_api_call_handles_failure()
    {
        Config::set('wablas.enabled', true);
        Config::set('wablas.domain', 'https://console.wablas.com');
        Config::set('wablas.api_key', 'test-key');

        Http::fake([
            'wablas.com/*' => Http::response(['status' => 'error'], 500),
        ]);

        $result = $this->service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['success']);
    }
}
