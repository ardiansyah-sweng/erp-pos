<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HrisService
{
    /**
     * Cek apakah email terdaftar sebagai karyawan berstatus kasir aktif di HRIS.
     */
    public function isCashierEmail(string $email): bool
    {
        $baseUrl = config('services.hris.url');

        if (! $baseUrl) {
            Log::warning('HRIS_API_URL belum dikonfigurasi.');

            return false;
        }

        try {
            $response = Http::timeout(5)->get(rtrim($baseUrl, '/') . '/cashiers');
        } catch (\Throwable $e) {
            Log::warning('Gagal menghubungi API HRIS: ' . $e->getMessage());

            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $cashiers = collect($response->json('data', []));

        return $cashiers->contains(function ($employee) use ($email) {
            return isset($employee['email'])
                && strcasecmp($employee['email'], $email) === 0
                && ($employee['status'] ?? 'active') === 'active';
        });
    }
}
