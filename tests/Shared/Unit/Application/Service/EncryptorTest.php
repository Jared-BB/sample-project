<?php

declare(strict_types=1);

namespace App\Tests\Shared\Unit\Application\Service;

use App\Shared\Application\Service\Encryptor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EncryptorTest extends TestCase
{
    public function test_encrypt_ok(): void
    {
        $encryptor = new Encryptor(str_repeat("\xAA", 32));

        $test1 = $encryptor->encrypt('testing');
        $test2 = $encryptor->encrypt('testing');

        self::assertNotSame($test1, $test2);

        self::assertGreaterThanOrEqual(12 + 16, strlen($test1));
        self::assertGreaterThanOrEqual(12 + 16, strlen($test2));
    }

    public function test_decrypt_ok(): void
    {
        $encryptor = new Encryptor(str_repeat("\xAA", 32));

        $encrypt = $encryptor->encrypt('testing');
        $decrypt = $encryptor->decrypt($encrypt);

        self::assertSame('testing', $decrypt);
    }

    public function test_decrypt_ko_when_payload_too_short(): void
    {
        $encryptor = new Encryptor(str_repeat("\xAA", 32));

        $tooShort = \base64_encode(str_repeat("\x00", 10));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Corrupt cipher');

        $encryptor->decrypt($tooShort);
    }
}
