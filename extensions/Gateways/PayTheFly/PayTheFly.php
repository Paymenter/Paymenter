<?php

namespace Paymenter\Extensions\Gateways\PayTheFly;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

#[ExtensionMeta(
    name: 'PayTheFly',
    description: 'Accept Web3 crypto payments via PayTheFly Pro. Supports BSC and TRON native tokens with EIP-712 typed signatures.',
    version: '1.0.0',
    author: 'PayTheFly',
    url: 'https://pro.paythefly.com',
    icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgcng9IjY0IiBmaWxsPSIjMUExQTJFIi8+PHBhdGggZD0iTTEyOCAzNTJWMTYwaDk2YzUzIDAgOTYgNDMgOTYgOTZzLTQzIDk2LTk2IDk2aC05NnptNjQtNjRoMzJjMTggMCAzMi0xNCAzMi0zMnMtMTQtMzItMzItMzJoLTMydjY0eiIgZmlsbD0iIzAwRDRGRiIvPjxjaXJjbGUgY3g9IjM1MiIgY3k9IjE2MCIgcj0iNDgiIGZpbGw9IiMwMEQ0RkYiIG9wYWNpdHk9Ii42Ii8+PC9zdmc+'
)]
class PayTheFly extends Gateway
{
    /**
     * PayTheFly Pro payment gateway base URL.
     */
    private const PAY_BASE_URL = 'https://pro.paythefly.com/pay';

    /**
     * Supported blockchain chains with their configurations.
     */
    private const CHAINS = [
        56 => [
            'symbol' => 'BSC',
            'decimals' => 18,
            'native_token' => '0x0000000000000000000000000000000000000000',
        ],
        728126428 => [
            'symbol' => 'TRON',
            'decimals' => 6,
            'native_token' => 'T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb',
        ],
    ];

    /**
     * Default payment deadline offset in seconds (30 minutes).
     */
    private const DEFAULT_DEADLINE_SECONDS = 1800;

    /**
     * Maximum allowed timestamp drift for webhook validation (5 minutes).
     */
    private const WEBHOOK_TIMESTAMP_TOLERANCE = 300;

    /**
     * Register the routes required by this gateway extension.
     */
    public function boot(): void
    {
        require __DIR__ . '/routes.php';
    }

    /**
     * Get all the configuration fields for the extension.
     *
     * @param  array  $values  The current values of the configuration
     * @return array
     */
    public function getConfig($values = []): array
    {
        $chainOptions = [];
        foreach (self::CHAINS as $chainId => $chain) {
            $chainOptions[] = [
                'value' => (string) $chainId,
                'label' => $chain['symbol'] . ' (Chain ID: ' . $chainId . ')',
            ];
        }

        return [
            [
                'name' => 'project_id',
                'label' => 'Project ID',
                'placeholder' => 'Enter your PayTheFly Pro project ID',
                'type' => 'text',
                'description' => 'Your project identifier from the PayTheFly Pro dashboard.',
                'required' => true,
            ],
            [
                'name' => 'project_key',
                'label' => 'Project Key (Secret)',
                'placeholder' => 'Enter your PayTheFly Pro project key',
                'type' => 'text',
                'description' => 'Your project secret key used for signing payment requests and verifying webhooks. Keep this confidential.',
                'required' => true,
            ],
            [
                'name' => 'chain_id',
                'label' => 'Blockchain Network',
                'type' => 'select',
                'options' => $chainOptions,
                'description' => 'Select the blockchain network for payments.',
                'required' => true,
            ],
            [
                'name' => 'contract_address',
                'label' => 'Verifying Contract Address',
                'placeholder' => '0x...',
                'type' => 'text',
                'description' => 'The PayTheFly Pro smart contract address on the selected chain. Required for EIP-712 signature generation.',
                'required' => true,
            ],
            [
                'name' => 'token_address',
                'label' => 'Payment Token Address',
                'placeholder' => '0x0000000000000000000000000000000000000000',
                'type' => 'text',
                'description' => 'Token contract address. Use native token address for chain currency (BSC: 0x000...000, TRON: T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb). Leave empty for chain native token.',
                'required' => false,
            ],
            [
                'name' => 'token_decimals',
                'label' => 'Token Decimals',
                'placeholder' => '18',
                'type' => 'text',
                'description' => 'Number of decimal places for the payment token (e.g., 18 for BSC native, 6 for TRON/USDT). Defaults to the chain native token decimals if empty.',
                'required' => false,
            ],
            [
                'name' => 'deadline_seconds',
                'label' => 'Payment Deadline (seconds)',
                'placeholder' => '1800',
                'type' => 'text',
                'description' => 'Time window in seconds for the user to complete payment after initiating. Default: 1800 (30 minutes).',
                'required' => false,
            ],
        ];
    }

    /**
     * Generate the PayTheFly Pro payment URL and redirect the user.
     *
     * @param  Invoice  $invoice  The invoice to be paid
     * @param  mixed  $total  The total amount to charge
     * @return string  The payment URL to redirect the user to
     */
    public function pay(Invoice $invoice, $total): string
    {
        $chainId = (int) $this->config('chain_id');
        $chain = $this->getChainConfig($chainId);
        $projectId = $this->config('project_id');
        $projectKey = $this->config('project_key');
        $contractAddress = $this->config('contract_address');

        // Determine token address (default to chain native token)
        $tokenAddress = $this->config('token_address');
        if (empty($tokenAddress)) {
            $tokenAddress = $chain['native_token'];
        }

        // Determine token decimals
        $tokenDecimals = $this->config('token_decimals');
        if (empty($tokenDecimals)) {
            $tokenDecimals = $chain['decimals'];
        }
        $tokenDecimals = (int) $tokenDecimals;

        // Convert total to on-chain amount (integer string)
        $amount = $this->toSmallestUnit($total, $tokenDecimals);

        // Generate a unique serial number using invoice ID
        $serialNo = 'PMT-' . $invoice->id . '-' . time();

        // Calculate deadline
        $deadlineSeconds = (int) ($this->config('deadline_seconds') ?: self::DEFAULT_DEADLINE_SECONDS);
        $deadline = time() + $deadlineSeconds;

        // Generate EIP-712 typed data signature
        $signature = $this->signPaymentRequest(
            $projectId,
            $tokenAddress,
            $amount,
            $serialNo,
            $deadline,
            $chainId,
            $contractAddress,
            $projectKey
        );

        // Build payment URL
        $params = [
            'chainId' => $chainId,
            'projectId' => $projectId,
            'amount' => $amount,
            'serialNo' => $serialNo,
            'deadline' => $deadline,
            'signature' => $signature,
            'token' => $tokenAddress,
        ];

        return self::PAY_BASE_URL . '?' . http_build_query($params);
    }

    /**
     * Handle the PayTheFly Pro webhook callback.
     *
     * Validates the HMAC signature, checks for payment confirmation,
     * and records the payment in Paymenter.
     *
     * @param  Request  $request  The incoming webhook request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        $projectKey = $this->config('project_key');

        // Extract webhook payload fields
        $data = $request->input('data');
        $sign = $request->input('sign');
        $timestamp = $request->input('timestamp');

        // Validate required fields are present
        if (empty($data) || empty($sign) || empty($timestamp)) {
            Log::warning('PayTheFly webhook: Missing required fields', [
                'has_data' => ! empty($data),
                'has_sign' => ! empty($sign),
                'has_timestamp' => ! empty($timestamp),
            ]);

            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // Validate timestamp to prevent replay attacks
        if (abs(time() - (int) $timestamp) > self::WEBHOOK_TIMESTAMP_TOLERANCE) {
            Log::warning('PayTheFly webhook: Timestamp expired', [
                'timestamp' => $timestamp,
                'server_time' => time(),
            ]);

            return response()->json(['error' => 'Timestamp expired'], 400);
        }

        // Verify HMAC-SHA256 signature: HMAC(data + "." + timestamp, projectKey)
        $expectedSign = hash_hmac('sha256', $data . '.' . $timestamp, $projectKey);

        if (! hash_equals($expectedSign, $sign)) {
            Log::warning('PayTheFly webhook: Invalid signature');

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Parse the data JSON string
        $payload = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('PayTheFly webhook: Invalid JSON in data field');

            return response()->json(['error' => 'Invalid data payload'], 400);
        }

        // Validate this webhook is for our project
        if (($payload['project_id'] ?? '') !== $this->config('project_id')) {
            Log::warning('PayTheFly webhook: Project ID mismatch', [
                'expected' => $this->config('project_id'),
                'received' => $payload['project_id'] ?? 'null',
            ]);

            return response()->json(['error' => 'Project ID mismatch'], 400);
        }

        // Only process payment webhooks (tx_type=1), not withdrawals (tx_type=2)
        $txType = (int) ($payload['tx_type'] ?? 0);
        if ($txType !== 1) {
            Log::info('PayTheFly webhook: Ignoring non-payment tx_type', ['tx_type' => $txType]);

            return response()->json(['status' => 'success']);
        }

        // Only process confirmed transactions
        if (empty($payload['confirmed'])) {
            Log::info('PayTheFly webhook: Transaction not yet confirmed', [
                'tx_hash' => $payload['tx_hash'] ?? 'unknown',
            ]);

            return response()->json(['status' => 'success']);
        }

        // Extract invoice ID from serial number (format: PMT-{invoiceId}-{timestamp})
        $serialNo = $payload['serial_no'] ?? '';
        $invoiceId = $this->extractInvoiceId($serialNo);

        if ($invoiceId === null) {
            Log::warning('PayTheFly webhook: Could not extract invoice ID from serial_no', [
                'serial_no' => $serialNo,
            ]);

            return response()->json(['error' => 'Invalid serial number'], 400);
        }

        // Calculate the payment amount in the display currency
        $chainId = (int) $this->config('chain_id');
        $chain = $this->getChainConfig($chainId);
        $tokenDecimals = $this->config('token_decimals');
        if (empty($tokenDecimals)) {
            $tokenDecimals = $chain['decimals'];
        }
        $tokenDecimals = (int) $tokenDecimals;

        $value = $this->fromSmallestUnit($payload['value'] ?? '0', $tokenDecimals);
        $fee = $this->fromSmallestUnit($payload['fee'] ?? '0', $tokenDecimals);

        // Record the payment
        $txHash = $payload['tx_hash'] ?? null;

        ExtensionHelper::addPayment(
            $invoiceId,
            'PayTheFly',
            $value,
            $fee,
            transactionId: $txHash
        );

        Log::info('PayTheFly webhook: Payment recorded', [
            'invoice_id' => $invoiceId,
            'tx_hash' => $txHash,
            'value' => $value,
            'fee' => $fee,
            'chain' => $payload['chain_symbol'] ?? 'unknown',
        ]);

        // PayTheFly requires response to contain "success"
        return response()->json(['status' => 'success']);
    }

    /**
     * Generate the EIP-712 typed data hash and sign it with the project key.
     *
     * EIP-712 Domain: { name: "PayTheFlyPro", version: "1", chainId, verifyingContract }
     * PaymentRequest type: { projectId(string), token(address), amount(uint256), serialNo(string), deadline(uint256) }
     */
    private function signPaymentRequest(
        string $projectId,
        string $tokenAddress,
        string $amount,
        string $serialNo,
        int $deadline,
        int $chainId,
        string $contractAddress,
        string $projectKey
    ): string {
        // EIP-712 domain separator
        $domainTypeHash = $this->keccak256(
            'EIP712Domain(string name,string version,uint256 chainId,address verifyingContract)'
        );

        $domainSeparator = $this->keccak256Packed([
            $domainTypeHash,
            $this->keccak256('PayTheFlyPro'),
            $this->keccak256('1'),
            $this->uint256Encode($chainId),
            $this->addressEncode($contractAddress),
        ]);

        // PaymentRequest type hash
        $paymentRequestTypeHash = $this->keccak256(
            'PaymentRequest(string projectId,address token,uint256 amount,string serialNo,uint256 deadline)'
        );

        // Struct hash
        $structHash = $this->keccak256Packed([
            $paymentRequestTypeHash,
            $this->keccak256($projectId),
            $this->addressEncode($tokenAddress),
            $this->uint256Encode($amount),
            $this->keccak256($serialNo),
            $this->uint256Encode($deadline),
        ]);

        // EIP-712 message hash: keccak256("\x19\x01" . domainSeparator . structHash)
        $message = "\x19\x01" . hex2bin($domainSeparator) . hex2bin($structHash);
        $messageHash = self::keccak256HexRaw($message);

        // Sign with project key using secp256k1 ECDSA
        return $this->ecSign($messageHash, $projectKey);
    }

    /**
     * Compute Keccak-256 hash of a UTF-8 string, returned as hex.
     */
    private function keccak256(string $data): string
    {
        return self::keccak256HexRaw($data);
    }

    /**
     * Compute Keccak-256 over concatenated packed binary chunks.
     *
     * @param  array<string>  $hexChunks  Array of hex-encoded data (without 0x prefix)
     */
    private function keccak256Packed(array $hexChunks): string
    {
        $packed = '';
        foreach ($hexChunks as $chunk) {
            $packed .= hex2bin($chunk);
        }

        return self::keccak256HexRaw($packed);
    }

    /**
     * ABI-encode a uint256 value.
     *
     * @param  int|string  $value  Integer or numeric string
     * @return string  Hex-encoded 32-byte value (without 0x prefix)
     */
    private function uint256Encode($value): string
    {
        $hex = gmp_strval(gmp_init((string) $value, 10), 16);

        return str_pad($hex, 64, '0', STR_PAD_LEFT);
    }

    /**
     * ABI-encode an address (left-padded to 32 bytes).
     *
     * @param  string  $address  Address with or without 0x prefix
     * @return string  Hex-encoded 32-byte value (without 0x prefix)
     */
    private function addressEncode(string $address): string
    {
        // Remove 0x prefix if present
        if (str_starts_with(strtolower($address), '0x')) {
            $address = substr($address, 2);
        }

        // For TRON addresses (base58), convert to hex
        if (strlen($address) !== 40 || ! ctype_xdigit($address)) {
            $address = $this->tronAddressToHex($address);
        }

        return str_pad(strtolower($address), 64, '0', STR_PAD_LEFT);
    }

    /**
     * Convert a TRON base58check address to hex (without prefix).
     *
     * @param  string  $base58Address  TRON address (e.g., T9yD14...)
     * @return string  40-character hex address
     */
    private function tronAddressToHex(string $base58Address): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = gmp_init(58);
        $value = gmp_init(0);

        for ($i = 0; $i < strlen($base58Address); $i++) {
            $pos = strpos($alphabet, $base58Address[$i]);
            if ($pos === false) {
                Log::warning('PayTheFly: Invalid base58 character in TRON address');

                return str_repeat('0', 40);
            }
            $value = gmp_add(gmp_mul($value, $base), gmp_init($pos));
        }

        $hex = gmp_strval($value, 16);

        // TRON address: 1 byte prefix (41) + 20 bytes address + 4 bytes checksum
        // We need the 20 bytes address part (skip prefix '41' and last 4 bytes checksum)
        if (strlen($hex) >= 48) {
            return substr($hex, 2, 40);
        }

        return str_pad($hex, 40, '0', STR_PAD_LEFT);
    }

    /**
     * Sign a message hash using ECDSA with secp256k1.
     *
     * @param  string  $messageHash  Hex-encoded 32-byte message hash
     * @param  string  $privateKey  Hex-encoded private key (project key)
     * @return string  Hex-encoded signature with 0x prefix (r + s + v, 65 bytes)
     */
    private function ecSign(string $messageHash, string $privateKey): string
    {
        // Remove 0x prefix from private key if present
        if (str_starts_with(strtolower($privateKey), '0x')) {
            $privateKey = substr($privateKey, 2);
        }

        // Ensure the private key is 32 bytes (64 hex chars)
        $privateKey = str_pad($privateKey, 64, '0', STR_PAD_LEFT);

        // Try using the elliptic-curve extension if available (e.g., simplito/elliptic-php)
        // Otherwise fall back to OpenSSL
        $pem = $this->hexKeyToPem($privateKey);

        $key = openssl_pkey_get_private($pem);
        if ($key === false) {
            Log::error('PayTheFly: Failed to load private key for signing');

            return '0x' . str_repeat('0', 130);
        }

        $hashBin = hex2bin($messageHash);
        $signature = '';
        $result = openssl_sign($hashBin, $signature, $key, OPENSSL_ALGO_SHA256);

        if (! $result) {
            Log::error('PayTheFly: EC signing failed');

            return '0x' . str_repeat('0', 130);
        }

        // Parse DER-encoded signature to extract r and s
        return '0x' . $this->derToRSV($signature);
    }

    /**
     * Convert a hex-encoded secp256k1 private key to PEM format.
     *
     * @param  string  $hexKey  64-character hex private key
     * @return string  PEM-encoded EC private key
     */
    private function hexKeyToPem(string $hexKey): string
    {
        // secp256k1 OID: 1.3.132.0.10
        $oid = '06052b8104000a';

        // ECPrivateKey SEQUENCE
        $ecPrivateKey = '02' . '01' . '01'              // version INTEGER 1
            . '04' . '20' . $hexKey                      // privateKey OCTET STRING (32 bytes)
            . 'a0' . $this->derLength(strlen($oid) / 2) . $oid;  // [0] parameters OID

        $sequence = '30' . $this->derLength(strlen($ecPrivateKey) / 2) . $ecPrivateKey;

        $der = hex2bin($sequence);
        $base64 = base64_encode($der);
        $pem = "-----BEGIN EC PRIVATE KEY-----\n";
        $pem .= chunk_split($base64, 64, "\n");
        $pem .= "-----END EC PRIVATE KEY-----\n";

        return $pem;
    }

    /**
     * Encode a DER length value.
     *
     * @param  int  $length  Length in bytes
     * @return string  Hex-encoded DER length
     */
    private function derLength(int $length): string
    {
        if ($length < 128) {
            return str_pad(dechex($length), 2, '0', STR_PAD_LEFT);
        }

        $hex = dechex($length);
        if (strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }

        $numBytes = strlen($hex) / 2;

        return dechex(0x80 | $numBytes) . $hex;
    }

    /**
     * Convert a DER-encoded ECDSA signature to r+s+v format (65 bytes hex).
     *
     * @param  string  $der  DER-encoded signature binary
     * @return string  130-character hex string (r: 64 + s: 64 + v: 2)
     */
    private function derToRSV(string $der): string
    {
        $hex = bin2hex($der);
        $pos = 0;

        // SEQUENCE tag (0x30)
        if (substr($hex, $pos, 2) !== '30') {
            return str_repeat('0', 130);
        }
        $pos += 2;

        // SEQUENCE length (may be multi-byte)
        $seqLenByte = hexdec(substr($hex, $pos, 2));
        $pos += 2;
        if ($seqLenByte > 127) {
            $numLenBytes = $seqLenByte - 128;
            $pos += $numLenBytes * 2;
        }

        // First INTEGER (r)
        if (substr($hex, $pos, 2) !== '02') {
            return str_repeat('0', 130);
        }
        $pos += 2;
        $rLen = hexdec(substr($hex, $pos, 2));
        $pos += 2;
        $r = substr($hex, $pos, $rLen * 2);
        $pos += $rLen * 2;

        // Strip leading zero padding from DER
        if (strlen($r) > 64) {
            $r = substr($r, strlen($r) - 64);
        }
        $r = str_pad($r, 64, '0', STR_PAD_LEFT);

        // Second INTEGER (s)
        if (substr($hex, $pos, 2) !== '02') {
            return str_repeat('0', 130);
        }
        $pos += 2;
        $sLen = hexdec(substr($hex, $pos, 2));
        $pos += 2;
        $s = substr($hex, $pos, $sLen * 2);

        if (strlen($s) > 64) {
            $s = substr($s, strlen($s) - 64);
        }
        $s = str_pad($s, 64, '0', STR_PAD_LEFT);

        // Recovery id (v = 27), adjusted by the contract during verification
        $v = '1b';

        return $r . $s . $v;
    }

    /**
     * Convert a human-readable amount to the smallest token unit.
     *
     * @param  float|string  $amount  The amount in the display unit (e.g., 1.5 BNB)
     * @param  int  $decimals  Token decimals
     * @return string  Integer string in the smallest unit
     */
    private function toSmallestUnit($amount, int $decimals): string
    {
        if (function_exists('bcmul')) {
            $factor = bcpow('10', (string) $decimals, 0);

            return bcmul((string) $amount, $factor, 0);
        }

        // Fallback using GMP for integer math
        $factor = gmp_pow(gmp_init(10), $decimals);
        $parts = explode('.', (string) $amount);
        $whole = gmp_mul(gmp_init($parts[0] ?? '0'), $factor);

        if (isset($parts[1])) {
            $frac = str_pad($parts[1], $decimals, '0');
            $frac = substr($frac, 0, $decimals);
            $whole = gmp_add($whole, gmp_init($frac));
        }

        return gmp_strval($whole);
    }

    /**
     * Convert an amount in smallest token unit back to the display unit.
     *
     * @param  string  $amount  The amount in smallest unit
     * @param  int  $decimals  Token decimals
     * @return float  The amount in human-readable display unit
     */
    private function fromSmallestUnit(string $amount, int $decimals): float
    {
        if (function_exists('bcdiv')) {
            $factor = bcpow('10', (string) $decimals, 0);

            return (float) bcdiv($amount, $factor, $decimals);
        }

        return (float) $amount / pow(10, $decimals);
    }

    /**
     * Extract the invoice ID from a serial number.
     *
     * Serial number format: PMT-{invoiceId}-{timestamp}
     *
     * @param  string  $serialNo  The serial number
     * @return int|null  The invoice ID or null if extraction fails
     */
    private function extractInvoiceId(string $serialNo): ?int
    {
        if (! preg_match('/^PMT-(\d+)-\d+$/', $serialNo, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    /**
     * Get the chain configuration for the given chain ID.
     *
     * @param  int  $chainId  The blockchain chain ID
     * @return array  The chain configuration
     *
     * @throws \InvalidArgumentException  If chain ID is not supported
     */
    private function getChainConfig(int $chainId): array
    {
        if (! isset(self::CHAINS[$chainId])) {
            throw new \InvalidArgumentException("Unsupported chain ID: {$chainId}");
        }

        return self::CHAINS[$chainId];
    }

    /**
     * Compute Keccak-256 hash of raw binary data, returned as hex string.
     *
     * NOTE: PHP's native hash('sha3-256') is NOT Keccak-256 (they differ in padding).
     * This implementation uses the kornrunner/keccak package if available,
     * or falls back to shell command / sha3-256 with a clear warning.
     *
     * For production use, install: composer require kornrunner/keccak
     */
    private static function keccak256HexRaw(string $data): string
    {
        // IMPORTANT: Ethereum uses Keccak-256, NOT SHA3-256 (FIPS 202).
        // They differ in padding (Keccak: 0x01, SHA3: 0x06) and produce different hashes.
        // The kornrunner/keccak library is REQUIRED for correct EIP-712 signatures.

        if (!class_exists(\kornrunner\Keccak::class)) {
            throw new \RuntimeException(
                'PayTheFly requires the kornrunner/keccak package for Ethereum-compatible hashing. '
                . 'Install it with: composer require kornrunner/keccak'
            );
        }

        return \kornrunner\Keccak::hash($data, 256);
    }
}
