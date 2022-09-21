<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Utilities\Database\QueryBuilder;

class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateUpdateQuery(): void
    {
        $this->assertEquals("UPDATE `users` SET `name` = 'John Doe' WHERE `id` = 1",
            QueryBuilder::update([
                'table' => 'users',
                'where' => [
                    'id' => 1
                ],
                'columns' => [
                    'name' => 'John Doe'
                ]
            ])
        );

        $this->assertEquals("UPDATE `users` SET `name` = 'John Doe'",
            QueryBuilder::update([
                'table' => 'users',
                'columns' => [
                    'name' => 'John Doe'
                ]
            ])
        );
    }

    public function testCreateInsertQuery(): void
    {
        $this->assertEquals(
            "INSERT INTO `users` (`name`, `email`) VALUES ('John', 'john@example.com')",
            QueryBuilder::insert([
                'table' => 'users',
                'columns' => [
                    'name' => 'John',
                    'email' => 'john@example.com'
                ]
            ])
        );
    }

    public function testCreateDeleteQuery(): void
    {
        $this->assertEquals(
            "DELETE FROM `users` WHERE `id` = 1",
            QueryBuilder::delete([
                'table' => 'users',
                'where' => [
                    'id' => 1
                ]
            ])
        );

        $this->assertEquals(
            "DELETE FROM `users`",
            QueryBuilder::delete([
                'table' => 'users',
                'where' => '*'
            ])
        );
    }

    public function testCreateSelectQuery(): void
    {
        $this->assertEquals(
            $this->createSelectQuery(),
            "SELECT `id`, `name`, `email` FROM `users` WHERE `id` = 1"
        );
    }

    private function createSelectQuery(): string
    {
        return QueryBuilder::select([
            'table' => 'users',
            'columns' => [
                'id',
                'name',
                'email'
            ],
            'where' => [
                [
                    'column' => 'id',
                    'operator' => '=',
                    'value' => 1
                ]
            ]
        ]);
    }

    public function testCreateSelectQueryWithMultipleWhereConditions()
    {
        $this->assertEquals(
            QueryBuilder::select([
                'table' => 'users',
                'where' => [
                    'id' => 1,
                    'name' => [
                        'operator' => 'LIKE',
                        'value' => '%John%'
                    ],
                    'status' => [1, 2, 3]
                ]
            ]),
            "SELECT * FROM `users` WHERE `id` = 1 AND `name` LIKE '%John%' AND `status` IN (1, 2, 3)"
        );
    }

    public function testUsingInOperator(): void
    {
        $query = QueryBuilder::select([
            'table' => 'users',
            'columns' => [
                'email'
            ],
            'where' => [
                'id' => [1, 2, 3],
                'status' => [
                    'operator' => 'IN',
                    'value' => [1, 2, 3]
                ]
            ]
        ]);
        $this->assertEquals("SELECT `email` FROM `users` WHERE `id` IN (1, 2, 3) AND `status` IN (1, 2, 3)", $query);

        $this->assertEquals("SELECT `email` FROM `users` WHERE `id` IN (1, 2, 3) LIMIT 10",
            QueryBuilder::select([
                'table' => 'users',
                'columns' => [
                    'email'
                ],
                'where' => [
                    'id' => [1, 2, 3]
                ],
                'limit' => 10
            ])
        );
    }

    public function testUpsert(): void
    {
        $query = QueryBuilder::upsert([
            'table' => 'users',
            'columns' => [
                'name' => 'John',
                'email' => 'john@litehex.com',
                'created_at' => '2020-01-01 00:00:00',
                'updated_at' => '2020-01-01 00:00:00'
            ],
            'where' => [
                'id' => 1
            ],
            'update' => [
                'name' => 'John Doe',
                'updated_at' => '2020-01-01 00:00:00'
            ]
        ]);

        $this->assertEquals(
            "INSERT INTO `users` (`name`, `email`, `created_at`, `updated_at`) VALUES ('John', 'john@litehex.com', '2020-01-01 00:00:00', '2020-01-01 00:00:00') ON DUPLICATE KEY UPDATE `name` = 'John Doe', `updated_at` = '2020-01-01 00:00:00'",
            $query
        );
    }

}