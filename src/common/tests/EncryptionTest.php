<?php

namespace UtilitiesTests\Common;

use PHPUnit\Framework\TestCase;
use Utilities\Common\Common;
use Utilities\Common\Encryption;

class EncryptionTest extends TestCase
{

    private string $text = "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";

    public function test_encryption()
    {
        Encryption::$algorithm = 'sha256';
        Encryption::$cipher = 'AES-256-CBC';
        echo "Data: $this->text" . PHP_EOL;

        $secretKey = $this->generateSecretKey();
        echo "Secret Key: $secretKey" . PHP_EOL;

        $encrypted = Encryption::encrypt($secretKey, $this->text);
        echo "Encrypted: $encrypted" . PHP_EOL;

        $this->assertEquals($this->text, Encryption::decrypt($secretKey, $encrypted));
        $this->assertNotEquals($this->text, Encryption::decrypt($this->generateSecretKey(), $encrypted));
    }

    /**
     * Generate a random secret key
     *
     * @return string
     */
    private function generateSecretKey(): string
    {
        return Common::randomString(16);
    }

}