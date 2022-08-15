<?php

namespace Utilities\Database;

use PDO;
use PDOStatement;
use Utilities\Common\Common;
use Utilities\Common\Encryption;
use Utilities\Database\Exceptions\ConnectionRefusedException;
use Utilities\Database\Exceptions\InvalidSecretException;

/**
 * Database class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class DB
{

    /**
     * The database connection
     *
     * @var PDO
     */
    private PDO $PDOClass;

    /**
     * The login data
     *
     * @var array
     */
    private array $login_data;

    /**
     * Database constructor.
     *
     * @param ?string $secret The secret key for auto logging into the database
     */
    public function __construct(string $secret = null)
    {
        if ($secret !== null) {
            if (($loginInfo = $this->getLoginInfo($secret)) !== false) {
                $this->setConnection($loginInfo);
            } else {
                throw new InvalidSecretException(sprintf(
                    "The given database secret is not valid or doesn't secret file does not exist. Key: %s",
                    $secret
                ));
            }
        }
    }

    /**
     * Get login info by using the secret key
     *
     * @param string $secret
     * @return array|false
     */
    public function getLoginInfo(string $secret): array|false
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/.database/" . $this->getFileId($secret);

        if (file_exists($filePath)) {
            $encryptedData = file_get_contents($filePath);
            $rawData = Encryption::decrypt($secret, $encryptedData, false);
            return json_decode($rawData, true);
        }

        return false;
    }

    /**
     * @param string $secret
     * @return string
     */
    private function getFileId(string $secret): string
    {
        if (strlen($secret) < 32) {
            $repeats = floor(32 / strlen($secret));
            $part = str_repeat($secret, $repeats) . substr($secret, 0, 32 - (strlen($secret) * $repeats));
        } else {
            $part = substr($secret, 0, 32);
        }

        return Encryption::encrypt($secret, $part, false);
    }

    /**
     * @param array $data ["host", "user", "pass", "db"]
     * @return PDO
     */
    public function setConnection(array $data): PDO
    {
        if (isset($data["host"], $data["user"], $data["pass"], $data["db"])) {
            $this->login_data = $data;

            $connect = new PDO('mysql:host=' . $data['host'] . ';dbname=' . $data['db'], $data['user'], $data['pass']);

            if ($connect->errorCode() !== null && $connect->errorCode() !== "00000") {
                throw new ConnectionRefusedException(sprintf(
                    "Connection refused: %s",
                    $connect->errorInfo()[2]
                ));
            }

            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return ($this->PDOClass = $connect);
        }

        throw new InvalidSecretException("The given data doesn't contain all required fields.");
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
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $this->PDOClass->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->PDOClass->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        return $this->PDOClass;
    }

    /**
     * Save the connection info to a file
     *
     * @param string $secret
     * @return bool
     */
    public function saveConnection(string $secret): bool
    {
        if (!(strlen($secret) >= 8)) {
            return false;
        }

        $encryptedInfo = Encryption::encrypt($secret, json_encode($this->login_data), false);
        $startPath = $_SERVER['DOCUMENT_ROOT'] . "/.database/";

        if (!file_exists($startPath)) {
            mkdir($startPath, 0777, true);
        }

        return file_put_contents($startPath . $this->getFileId($secret), $encryptedInfo);
    }

    /**
     * @param string $query The query to execute
     * @return array|bool
     */
    public function QueryResult(string $query): array|bool
    {
        if (($result = $this->Query($query))) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * Execute a query
     *
     * @param string $query The query to execute
     * @param int $mode (optional) The fetch mode must be one of the PDO::FETCH_* constants.
     * @return PDOStatement|false
     */
    public function Query(string $query, int $mode = PDO::FETCH_ASSOC): PDOStatement|false
    {
        $stmt = $this->getConnection()->query($query, $mode);

        if ($stmt === false) {
            return false;
        }

        return $stmt;
    }

    /**
     * @return int|string
     */
    public function insertId(): int|string
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Insert row and if it existed update
     *
     * @param array $data ["table", "column", "where"]
     * @return mixed
     */
    public function select_column(array $data): mixed
    {
        $query = $this->Query(SQLQueryBuilder::select($data));
        $rowCount = $query->rowCount();

        if ($rowCount > 0) {
            return $query->fetchAll(PDO::FETCH_ASSOC)[0][$data['column']];
        }

        return false;
    }

    /**
     * Select a specific row
     *
     * @param array $data ["debug", "table", "where", "columns", "order", "extra"]
     * @return array|false
     */
    public function select(array $data): array|false
    {
        $query = SQLQueryBuilder::select($data);
        if (isset($data['debug'])) {
            Common::htmlCode($query);
        }

        if (($result = $this->Query($query))) {
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as $key => $value) {
                foreach ($value as $k => $v) {
                    if (gettype($v) === "string") {
                        $data[$key][$k] = htmlspecialchars_decode($v);
                    }
                }
            }

            return $data;
        }

        return false;
    }

    /**
     * Insert row and if it existed update
     *
     * @param array $data ["debug", "table", "where", "columns"]
     * @return bool
     */
    public function MetaQuery(array $data): bool
    {
        $query = $this->Query(SQLQueryBuilder::select($data));
        if (isset($data['debug'])) {
            Common::htmlCode($query);
        }

        if ($query !== false) {
            $rowCount = $query->rowCount();

            if ($rowCount > 0) {
                return $this->update($data);
            }

            return $this->insert($data);
        }

        return false;
    }

    /**
     * @param array $data ["debug", "table", "where", "columns"]
     * @return bool
     */
    public function update(array $data): bool
    {
        $Query = SQLQueryBuilder::update($data);
        if (isset($data['debug'])) {
            Common::htmlCode($Query);
        }

        return $this->Query($Query) !== false;
    }

    /**
     * @param array $data ["debug", "table", "columns"]
     * @return bool
     */
    public function insert(array $data): bool
    {
        $Query = SQLQueryBuilder::insert($data);
        if (isset($data['debug'])) {
            Common::htmlCode($Query);
        }

        return $this->Query($Query) !== false;
    }

    /**
     * @param array $data ["debug", "table", "where"]
     * @return bool
     */
    public function delete(array $data): bool
    {
        $query = SQLQueryBuilder::delete($data);
        if (isset($data['debug'])) {
            Common::htmlCode($query);
        }

        return $this->Query($query) !== false;
    }

    /**
     * Check if any row exists with the given data
     *
     * @param array $data ["debug", "table", "where"]
     * @return bool
     */
    public function exists(array $data): bool
    {
        $query = SQLQueryBuilder::select($data);
        if (isset($data['debug'])) {
            Common::htmlCode($query);
        }

        if (($result = $this->Query($query))) {
            return $result->rowCount() > 0;
        }

        throw new \RuntimeException("Error while checking if row exists");
    }

    /**
     * Search for a specific row, and it sorted by the given condition
     *
     * @param array $data ["debug", "table", "columns", "search"]
     * @return array
     */
    public function search(array $data): array
    {
        $result = [];

        foreach ($data['columns'] as $column) {
            $result = array_merge($result, $this->select([
                'debug' => $data['debug'],
                'table' => $data['table'],
                'where' => [
                    [
                        'column' => $column,
                        'operator' => 'LIKE',
                        'value' => "%{$data['search']}%"
                    ]
                ]
            ]));
        }

        return $result;
    }

}