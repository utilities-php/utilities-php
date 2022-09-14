<?php
declare(strict_types=1);

namespace UtilitiesTests\Database;

use Utilities\Database\QueryBuilder;

class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function test_create_update_query(): void
    {
        $this->assertEquals(
            "UPDATE `users` SET `name` = 'John Doe' WHERE `id` = 1",
            $this->create_update_query()
        );
    }

    private function create_update_query(): string
    {
        return QueryBuilder::update([
            'table' => 'users',
            'where' => [
                'id' => 1
            ],
            'columns' => [
                'name' => 'John Doe'
            ]
        ]);
    }

    public function test_create_insert_query(): void
    {
        $this->assertEquals(
            $this->create_insert_query(),
            "INSERT INTO `users` (`name`, `email`) VALUES ('John', 'john@example.com')"
        );
    }

    private function create_insert_query(): string
    {
        return QueryBuilder::insert([
            'table' => 'users',
            'columns' => [
                'name' => 'John',
                'email' => 'john@example.com'
            ]
        ]);
    }

    public function test_create_delete_query(): void
    {
        $this->assertEquals(
            $this->create_delete_query(),
            "DELETE FROM `users` WHERE `id` = 1"
        );
    }

    private function create_delete_query(): string
    {
        return QueryBuilder::delete([
            'table' => 'users',
            'where' => [
                'id' => 1
            ]
        ]);
    }

    public function test_create_select_query(): void
    {
        $this->assertEquals(
            $this->create_select_query(),
            "SELECT `id`, `name`, `email` FROM `users` WHERE `id` = 1"
        );
    }

    private function create_select_query(): string
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

    public function test_create_select_query_with_multiple_where_conditions()
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

    public function test_using_IN_operator(): void
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
    }

}