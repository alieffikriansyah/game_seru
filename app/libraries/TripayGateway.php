<?php
/**
 * TripayGateway - Airport Partner Hub
 * 
 * MODE:
 *  - PAYMENT_MODE=dummy   → Simulasi lokal, tidak ada API call. 
 *                           Tombol "Simulasi Bayar Berhasil" muncul di halaman payment.
 *  - PAYMENT_MODE=sandbox → Pakai Tripay Sandbox API (perlu daftar tripay.co.id gratis)
 *  - PAYMENT_MODE=live    → Produksi nyata
 */
if (!class_exists('Env') && defined('APPPATH')) {
    require_once APPPATH . 'core/Env.php';
}

class TripayGateway {

    private static function mode() {
        return Env::get('PAYMENT_MODE', 'dummy');
    }

    private static function apiBase() {
        return self::mode() === 'live'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    private static function apiKey()      { return Env::get('TRIPAY_API_KEY',      ''); }
    private static function privateKey()  { return Env::get('TRIPAY_PRIVATE_KEY',   ''); }
    private static function merchantCode(){ return Env::get('TRIPAY_MERCHANT_CODE', ''); }

    /**
     * Buat transaksi QRIS.
     * Jika mode=dummy, kembalikan data simulasi tanpa memanggil API.
     *
     * @return array ['success', 'reference', 'payment_url', 'qr_string', 'qr_url', 'expired_time']
     */
    public static function createTransaction(array $billing): array {
        if (self::mode() === 'dummy') {
            return self::dummyTransaction($billing);
        }

        $merchantRef = $billing['invoice_no'];
        $amount      = (int) $billing['amount'];

        $signature = hash_hmac('sha256',
            self::merchantCode() . $merchantRef . $amount,
            self::privateKey()
        );

        $payload = [
            'method'         => 'QRIS',
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $billing['tenant_name'] ?? 'Mitra Tenant',
            'customer_email' => $billing['tenant_email'] ?? 'tenant@airport.co.id',
            'customer_phone' => '08100000000',
            'order_items'    => [[
                'sku'      => 'AD-SLOT-' . $billing['id'],
                'name'     => $billing['title'],
                'price'    => $amount,
                'quantity' => 1,
            ]],
            'callback_url'   => Env::get('TRIPAY_CALLBACK_URL', ''),
            'return_url'     => Env::get('BASE_URL', '') . 'subscriptions',
            'expired_time'   => time() + (24 * 60 * 60), // 24 jam
            'signature'      => $signature,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::apiBase() . '/transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . self::apiKey()],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response) {
            return ['success' => false, 'error' => 'Koneksi ke Tripay gagal.'];
        }

        $data = json_decode($response, true);
        if (!$data || !$data['success']) {
            return ['success' => false, 'error' => $data['message'] ?? 'Tripay error tidak diketahui.'];
        }

        return [
            'success'      => true,
            'reference'    => $data['data']['reference'],
            'payment_url'  => $data['data']['checkout_url'],
            'qr_string'    => $data['data']['qr_string'] ?? '',
            'qr_url'       => $data['data']['qr_url'] ?? '',
            'expired_time' => $data['data']['expired_time'] ?? 0,
        ];
    }

    /**
     * Verifikasi signature callback dari Tripay.
     * Gunakan di PaymentCallback controller.
     */
    public static function verifyCallbackSignature(string $json, string $receivedSignature): bool {
        if (self::mode() === 'dummy') return true;

        $expectedSig = hash_hmac('sha256', $json, self::privateKey());
        return hash_equals($expectedSig, $receivedSignature);
    }

    /**
     * Simulasi transaksi untuk mode=dummy.
     * Tidak ada API call. Menghasilkan dummy QR string & reference.
     */
    private static function dummyTransaction(array $billing): array {
        $reference = 'DUMMY-' . strtoupper(substr(md5($billing['invoice_no'] . time()), 0, 10));
        return [
            'success'      => true,
            'reference'    => $reference,
            'payment_url'  => '',
            'qr_string'    => 'DUMMY_QR_' . $reference, // Placeholder, tidak scannable
            'qr_url'       => '', // Tidak ada QR URL sungguhan
            'expired_time' => time() + 86400,
            'is_dummy'     => true,
        ];
    }

    /**
     * Kembalikan apakah system sedang dalam mode dummy.
     */
    public static function isDummy(): bool {
        return self::mode() === 'dummy';
    }

    /**
     * Kembalikan mode aktif sebagai string.
     */
    public static function getMode(): string {
        return self::mode();
    }
}
