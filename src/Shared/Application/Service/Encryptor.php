<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use RuntimeException;

final readonly class Encryptor
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LEN = 12;
    private const TAG_LEN = 16;

    public function __construct(
        private string $key32,
    ) {
    }

    public function encrypt(string $plain): string
    {
        $iv = random_bytes(self::IV_LEN);
        $tag = '';
        $ct = openssl_encrypt(
            $plain,
            self::CIPHER,
            $this->key32,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LEN
        );

        return base64_encode($iv . $tag . $ct);
    }

    public function decrypt(string $b64): string
    {
        $data = base64_decode($b64, true);

        if ($data === false || strlen($data) < self::IV_LEN + self::TAG_LEN) {
            throw new RuntimeException('Corrupt cipher');
        }

        $iv = substr($data, 0, self::IV_LEN);
        $tag = substr($data, self::IV_LEN, self::TAG_LEN);
        $ct = substr($data, self::IV_LEN + self::TAG_LEN);

        return openssl_decrypt(
            $ct,
            self::CIPHER,
            $this->key32,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }
}
