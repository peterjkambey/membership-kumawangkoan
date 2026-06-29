<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private string $apiKey;
    private bool $enabled;

    public function __construct()
    {
        $this->apiKey = config('xendit.api_key');
        $this->enabled = config('xendit.enabled', false);
    }

    /**
     * Buat Virtual Account.
     */
    public function createVA(array $params): array
    {
        if (!config('xendit.enabled', false)) {
            Log::info('[XENDIT DRY-RUN] VA tidak dibuat (mode dry-test).', $params);

            return [
                'success' => true,
                'dry_run' => true,
                'data' => [
                    'id' => 'dry-' . uniqid(),
                    'external_id' => $params['external_id'],
                    'bank_code' => $params['bank_code'] ?? 'BCA',
                    'account_number' => '8800' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'status' => 'PENDING',
                ],
            ];
        }

        try {
            $apiKey = config('xendit.api_key');
            $response = Http::withBasicAuth($apiKey, '')
                ->post('https://api.xendit.co/callback_virtual_accounts', [
                    'external_id' => $params['external_id'],
                    'bank_code' => $params['bank_code'] ?? 'BCA',
                    'name' => $params['name'] ?? 'Pembayaran Kumawangkoan',
                    'is_closed' => true,
                    'expected_amount' => $params['amount'],
                    'expiration_date' => now()->addMinutes(config('xendit.va.expiry_minutes', 1440))->toIso8601String(),
                ]);

            $data = $response->json();

            Log::info('[XENDIT] Create VA response', [
                'status' => $response->status(),
                'data' => $data,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $data,
                'error' => $response->failed() ? ($data['message'] ?? 'Gagal buat VA') : null,
            ];

        } catch (\Exception $e) {
            Log::error('[XENDIT] Gagal buat VA: ' . $e->getMessage());

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Buat QRIS.
     */
    public function createQRIS(array $params): array
    {
        if (!config('xendit.enabled', false)) {
            Log::info('[XENDIT DRY-RUN] QRIS tidak dibuat (mode dry-test).', $params);

            return [
                'success' => true,
                'dry_run' => true,
                'data' => [
                    'id' => 'dry-' . uniqid(),
                    'external_id' => $params['external_id'],
                    'qr_string' => '00020101021126580010IDXENDIT...' . substr(md5($params['external_id']), 0, 8),
                    'status' => 'PENDING',
                ],
            ];
        }

        try {
            $apiKey = config('xendit.api_key');
            $response = Http::withBasicAuth($apiKey, '')
                ->post('https://api.xendit.co/qr_codes', [
                    'external_id' => $params['external_id'],
                    'type' => 'DYNAMIC',
                    'callback_url' => route('xendit.webhook'),
                    'amount' => $params['amount'],
                    'reference_id' => $params['external_id'],
                ]);

            $data = $response->json();

            Log::info('[XENDIT] Create QRIS response', [
                'status' => $response->status(),
                'data' => $data,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $data,
                'error' => $response->failed() ? ($data['message'] ?? 'Gagal buat QRIS') : null,
            ];

        } catch (\Exception $e) {
            Log::error('[XENDIT] Gagal buat QRIS: ' . $e->getMessage());

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simulasi create VA — ngasi hasil fake kayak response Xendit beneran.
     * Buat test / dry-run development.
     */
    public function simulateVA(array $params): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => 'sim-' . uniqid(),
                'external_id' => $params['external_id'],
                'bank_code' => $params['bank_code'] ?? 'BCA',
                'account_number' => '8800' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                'expected_amount' => $params['amount'],
                'status' => 'PENDING',
                'expiration_date' => now()->addDay()->toIso8601String(),
            ],
        ];
    }

    /**
     * Simulasi QRIS — fake response.
     */
    public function simulateQRIS(array $params): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => 'sim-' . uniqid(),
                'external_id' => $params['external_id'],
                'qr_string' => '00020101021126580010IDXENDITSIMULASI',
                'status' => 'PENDING',
            ],
        ];
    }
}
