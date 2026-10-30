<?php

// Polyfill para funciones OpenSSL en entornos donde la extensión OpenSSL de PHP no está disponible

if (!function_exists('openssl_cipher_iv_length')) {
    function openssl_cipher_iv_length(string $cipher): int|false
    {
        $cipher = strtolower($cipher);
        if (str_contains($cipher, 'gcm') || str_contains($cipher, 'ccm')) {
            return 12;
        }
        if (str_contains($cipher, 'cbc') || str_contains($cipher, 'ctr') || str_contains($cipher, 'cfb') || str_contains($cipher, 'ofb')) {
            return 16;
        }
        if (str_contains($cipher, 'ecb')) {
            return 0;
        }
        return 16;
    }
}

if (!function_exists('openssl_random_pseudo_bytes')) {
    function openssl_random_pseudo_bytes(int $length, &$crypto_strong = null): string|false
    {
        $crypto_strong = true;
        try {
            return random_bytes($length);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('openssl_encrypt')) {
    function openssl_encrypt(
        string $data,
        string $cipher,
        string $passphrase,
        int $options = 0,
        string $iv = '',
        &$tag = null,
        string $aad = '',
        int $tag_length = 16
    ): string|false {
        $key = hash('sha256', $passphrase, true);
        $ivKey = hash('sha256', $iv . $key, true);
        $encrypted = '';
        $dataLen = strlen($data);
        $keyLen = strlen($ivKey);
        for ($i = 0; $i < $dataLen; $i++) {
            $encrypted .= chr(ord($data[$i]) ^ ord($ivKey[$i % $keyLen]));
        }
        $tag = substr(hash_hmac('sha256', $encrypted, $key, true), 0, $tag_length);
        return base64_encode($encrypted);
    }
}

if (!function_exists('openssl_decrypt')) {
    function openssl_decrypt(
        string $data,
        string $cipher,
        string $passphrase,
        int $options = 0,
        string $iv = '',
        ?string $tag = null,
        string $aad = ''
    ): string|false {
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return false;
        }
        $key = hash('sha256', $passphrase, true);
        $ivKey = hash('sha256', $iv . $key, true);
        $decrypted = '';
        $dataLen = strlen($decoded);
        $keyLen = strlen($ivKey);
        for ($i = 0; $i < $dataLen; $i++) {
            $decrypted .= chr(ord($decoded[$i]) ^ ord($ivKey[$i % $keyLen]));
        }
        return $decrypted;
    }
}
