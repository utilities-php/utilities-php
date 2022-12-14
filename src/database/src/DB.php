<?php
declare(strict_types=1);

namespace Utilities\Database;

use Dotenv\Dotenv;
use PDO;
use PDOStatement;
use Utilities\Common\Common;
use Utilities\Database\Exceptions\InvalidSecretException;
use Utilities\Database\Exceptions\QueryException;
use Utilities\Database\Traits\ConnectorTrait;
use Utilities\Database\Traits\ValueExtractionTrait;

/**
 * DB class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class DB
{

    use ConnectorTrait;
    use ValueExtractionTrait;

    /**
     * The database connection
     *
     * @var PDO
     */
    private PDO $connection;

    /**
     * The database constructor
     *
     * @param bool $autoConnect If true, it will try to load environment variables and connect to the database
     */
    public function __construct(bool $autoConnect =true)
    {
        if (!isset($_ENV['DATABASE_SECRET_KEY']) && $autoConnect) {
            $location = self::getRootPath();
            $dotenv = Dotenv::createImmutable($location);
            $dotenv->load();
        }

        if (isset($_ENV['DATABASE_SECRET_KEY']) && $autoConnect) {
            $db = self::connectWithSecret($_ENV['DATABASE_SECRET_KEY']);
            $this->connection = $db->connection;
        }
    }

    /**
     * Connect with Secret
     *
     * @param string $secret
     * @return DB
     */
    public static function connectWithSecret(string $secret): DB
    {
        if (!($loginInfo = self::getLoginInfo($secret))) {
            throw new InvalidSecretException(
                "The given database secret is not valid or doesn't secret file does not exist.",
            );
        }

        $db = new DB(false);
        $db->setConnection($loginInfo);

        return $db;
    }

    /**
     * Connect with login info
     *
     * @param array $loginInfo {host, port[optional], user, pass, db}
     * @return DB
     */
    public static function connect(array $loginInfo): DB
    {
        if (!isset($loginInfo['host'], $loginInfo['user'], $loginInfo['pass'], $loginInfo['db'])) {
            throw new InvalidSecretException(
                "The given database login info is not valid.",
            );
        }

        $db = new DB(false);
        $db->setConnection($loginInfo);

        return $db;
    }

    /**
     * Get the last error
     *
     * @return array
     */
    public function errorInfo(): array
    {
        return $this->getConnection()->errorInfo();
    }

    /**
     * Prepare a query
     *
     * @param string $query The query string to prepare
     * @return PDOStatement
     */
    public function prepare(string $query): PDOStatement
    {
        return $this->getConnection()->prepare($query);
    }

    /**
     * Execute a query
     *
     * @param string $query The query to execute
     * @param int $mode (optional) The fetch mode must be one of the PDO::FETCH_* constants.
     * @return PDOStatement
     */
    public function query(string $query, int $mode = PDO::FETCH_ASSOC): PDOStatement
    {
        $stmt = $this->getConnection()->query($query, $mode);
        \assert($stmt instanceof PDOStatement);
        return $stmt;
    }

    /**
     * Get the last insert ID.
     *
     * @param string|null $name
     * @return string|bool
     */
    public function lastInsertId(string|null $name = null): string|bool
    {
        if ($name === null) {
            return $this->connection->lastInsertId();
        }

        return $this->getConnection()->lastInsertId($name);
    }

    /**
     * Begin a new database transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a database transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a database transaction.
     *
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Wrap quotes around the given input.
     *
     * @param string $input
     * @param int $type (optional) The PDO::PARAM_* constant
     * @return string
     */
    public function quote(string $input, int $type = PDO::PARAM_STR): string
    {
        return $this->connection->quote($input, $type);
    }

    /**
     * Search for a specific row, and it sorted by the given condition
     *
     * @param array $data {debug, table, columns, search, order, limit}
     * @return array
     */
    public function search(array $data): array
    {
        if (!isset($data['table'], $data['columns'], $data['search'])) {
            throw new QueryException('The table, columns, and search properties must be set.');
        }

        $result = [];

        foreach ($data['columns'] as $column) {
            $result = array_merge($result, $this->select([
                'debug' => $data['debug'] ?? false,
                'table' => $data['table'],
                'where' => [
                    [
                        'column' => $column,
                        'operator' => 'LIKE',
                        'value' => "%{$data['search']}%"
                    ]
                ],
                'order' => $data['order'] ?? [],
                'limit' => $data['limit'] ?? []
            ]) ?? []);
        }

        return $result;
    }

    /**
     * Select rows from the database
     *
     * @param array $data {debug, table, columns, where, order, limit}
     * @return array|false
     */
    public function select(array $data): array|false
    {
        $query = QueryBuilder::select($data, true);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        $prepare = $this->prepare($query);
        $prepare->execute([
            ...($this->extractValues($data))
        ]);

        if ($this->getConnection()->errorCode() !== '00000') {
            return false;
        }

        $data = $prepare->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if (gettype($v) === "string") {
                    $data[$key][$k] = htmlspecialchars_decode($v);
                }
            }
        }

        return $data;
    }

    /**
     * Insert row into the database
     *
     * @param array $data {debug, table, columns}
     * @return bool
     */
    public function insert(array $data): bool
    {
        $query = QueryBuilder::insert($data, true);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        $this->prepare($query)->execute([
            ...($this->extractValues($data))
        ]);

        return $this->getConnection()->errorCode() === '00000';
    }

    /**
     * Update or insert row into the database
     *
     * @param array $data {debug, table, columns, update}
     * @return bool
     */
    public function upsert(array $data): bool
    {
        $query = QueryBuilder::upsert($data, true);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        $this->prepare($query)->execute([
            ...($this->extractValues($data))
        ]);

        return $this->getConnection()->errorCode() === '00000';
    }

    /**
     * Update row into the database
     *
     * @param array $data {debug, table, where, columns}
     * @return bool
     */
    public function update(array $data): bool
    {
        $query = QueryBuilder::update($data, true);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        $data['update'] = $data['columns'];
        unset($data['columns']);

        $this->prepare($query)->execute([
            ...($this->extractValues($data))
        ]);

        return $this->getConnection()->errorCode() === '00000';
    }

    /**
     * Delete row from the database
     *
     * @param array $data {debug, table, where}
     * @return bool
     */
    public function delete(array $data): bool
    {
        $query = QueryBuilder::delete($data, true);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        if ($data['where'] === '*') {
            $this->prepare($query)->execute();

        } else {
            $this->prepare($query)->execute([
                ...($this->extractValues($data))
            ]);
        }

        return $this->getConnection()->errorCode() === '00000';
    }

    /**
     * Check any row exists with the given data
     *
     * @param array $data {debug, table, where}
     * @return bool
     */
    public function exists(array $data): bool
    {
        $query = QueryBuilder::select($data);

        if (isset($data['debug']) && $data['debug'] === true) {
            Common::htmlCode($query);
        }

        if (($result = $this->query($query))) {
            return $result->rowCount() > 0;
        }

        throw new QueryException("The query failed to execute.");
    }

}