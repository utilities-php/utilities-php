<?php
declare(strict_types=1);

namespace UtilitiesTests\Common;

class UUIDTest extends \PHPUnit\Framework\TestCase
{

    public function testGenerate(): void
    {
        $this->assertTrue(\Utilities\Common\UUID::generate() !== \Utilities\Common\UUID::generate());
        $this->assertTrue(\Utilities\Common\UUID::validate(\Utilities\Common\UUID::generate()));
    }

    public function testGenerateFromContent(): void
    {
        $uuid = \Utilities\Common\UUID::generate('test');
        $this->assertEquals('88c8c513-7d54-54a1-81c1-7f8fe8753d3b', $uuid);
    }

}