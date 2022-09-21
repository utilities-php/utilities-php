<?php declare(strict_types=1);

namespace UtilitiesTests\Database\Traits;

use Utilities\Database\Traits\ValueExtractionTrait;

class ValueExtractionTest extends \PHPUnit\Framework\TestCase
{

    use ValueExtractionTrait;

    public function testExtractWhereValues(): void
    {
        $this->assertEquals(
            [
                'WHERE_id' => 1,
                'WHERE_name' => 'John Doe'
            ],
            $this->extractWhereValues([
                'id' => 1,
                'name' => 'John Doe'
            ])
        );

        $this->assertEquals(
            [
                'WHERE_id' => 1,
                'WHERE_name' => 'John Doe',
                'WHERE_email' => 'john@doe.com'
            ],
            $this->extractWhereValues([
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@doe.com'
            ])
        );
    }

}