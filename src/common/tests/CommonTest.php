<?php
declare(strict_types=1);

namespace UtilitiesTests\Common;

use Utilities\Common\Common;

class CommonTest extends \PHPUnit\Framework\TestCase
{

    public function test_create_random_string(): void
    {
        $this->assertEquals(32, strlen(Common::randomString(32)));
        $this->assertFalse(str_contains(Common::randomString(32), ' '));
        $this->assertEquals(32, preg_match_all('/[a-zA-Z0-9]/', Common::randomString(32)));
        $this->assertEquals(32, preg_match_all('/[a-zA-Z0-9@#$%&*_-]/', Common::randomString(32, '[a-zA-Z0-9@#$%&*_-]')));
    }

    public function test_find_index_in_array(): void
    {
        $array = [
            'name' => 'Shahrad',
            'age' => 32,
            'City' => 'Tehran',
            'country' => 'Iran',
        ];

        $this->assertEquals(0, Common::findIndex($array, 'name'));
        $this->assertEquals(1, Common::findIndex($array, 'age'));
        $this->assertEquals(2, Common::findIndex($array, 'city', true));
        $this->assertEquals(3, Common::findIndex($array, 'country'));
        $this->assertEquals(false, Common::findIndex($array, 'test'));
    }

}