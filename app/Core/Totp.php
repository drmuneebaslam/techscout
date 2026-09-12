<?php
declare(strict_types=1);

namespace App\Core;

/**
 * TechScout TOTP Two-Factor Authentication Engine [TS-SEC-03]
 * Standard RFC 6238 (TOTP) & RFC 4648 (Base32) implementation in pure PHP.
 * Fully compatible with Google Authenticator, Microsoft Authenticator, Authy, and 1Password.
 */
class Totp {
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a cryptographically secure random Base32 secret (16-32 chars)
     */
    public static function generateSecret(int $length = 16): string {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }
        return $secret;
    }

    /**
     * Generate a 6-digit TOTP code for a secret at a given time slice (30s interval)
     */
    public static function getCode(string $secret, ?int $timeSlice = null): string {
        if ($timeSlice === null) {
            $timeSlice = (int)floor(time() / 30);
        }

        $secretKey = self::base32Decode($secret);
        if ($secretKey === '') {
            return '';
        }

        // Pack time counter into 8-byte binary string (big-endian)
        $time = pack('N*', 0) . pack('N*', $timeSlice);

        // Compute HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $modulo = 10 ** 6;
        return str_pad((string)($value % $modulo), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit code against the secret with allowable clock drift (+-1 time slice = +-30s)
     */
    public static function verify(string $secret, string $code, int $discrepancy = 1): bool {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTimeSlice = (int)floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate otpauth:// URI for authenticator app enrollment
     */
    public static function getOtpAuthUrl(string $label, string $secret, string $issuer = 'TechScout'): string {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label) . '?secret=' . rawurlencode($secret) . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Generate a set of emergency single-use backup recovery codes
     */
    public static function generateRecoveryCodes(int $count = 8): array {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)) . '-' . bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Base32 decode helper (RFC 4648)
     */
    private static function base32Decode(string $b32): string {
        $b32 = strtoupper(trim($b32));
        $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);
        if ($b32 === '') {
            return '';
        }

        $binary = '';
        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            $pos = strpos(self::BASE32_CHARS, $char);
            if ($pos === false) continue;
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        $len = strlen($binary);
        for ($i = 0; $i + 8 <= $len; $i += 8) {
            $bytes .= chr(bindec(substr($binary, $i, 8)));
        }

        return $bytes;
    }
}
