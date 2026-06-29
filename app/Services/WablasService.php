<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasService
{
    /**
     * Kirim pesan WhatsApp via Wablas API.
     * Jika config enabled=false, hanya log (dry-run).
     *
     * @return array ['success' => bool, 'response' => mixed]
     */
    public function sendMessage(string $phone, string $message): array
    {
        $enabled = config('wablas.enabled', false);

        if (!$enabled) {
            Log::info('[WABLAS DRY-RUN] Pesan tidak dikirim (mode dry-test).', [
                'phone' => $phone,
                'message_preview' => substr($message, 0, 100),
            ]);

            return [
                'success' => true,
                'response' => ['dry_run' => true],
            ];
        }

        $domain = config('wablas.domain');
        $apiKey = config('wablas.api_key');

        if (empty($domain) || empty($apiKey)) {
            Log::warning('[WABLAS] Konfigurasi tidak lengkap.');

            return [
                'success' => false,
                'response' => 'Konfigurasi Wablas tidak lengkap.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post("{$domain}/api/send-message", [
                'phone' => $this->formatPhone($phone),
                'message' => $message,
            ]);

            Log::info('[WABLAS] API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('[WABLAS] Gagal kirim pesan: ' . $e->getMessage());

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim pesan ke banyak nomor sekaligus.
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $enabled = config('wablas.enabled', false);

        if (!$enabled) {
            Log::info('[WABLAS DRY-RUN] Bulk message dry-run.', [
                'count' => count($recipients),
            ]);

            return ['success' => true, 'response' => ['dry_run' => true]];
        }

        $results = [];
        foreach ($recipients as $phone) {
            $results[$phone] = $this->sendMessage($phone, $message);
        }

        return [
            'success' => true,
            'response' => $results,
        ];
    }

    /**
     * Format nomor HP: hapus spasi/tanda, tambah prefix 62.
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            // already correct
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}
