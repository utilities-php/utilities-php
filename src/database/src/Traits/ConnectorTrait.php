<?php
declare(strict_types=1);

namespace Utilities\Database\Traits;

use PDO;
use Utilities\Common\Encryption;
use Utilities\Database\Exceptions\ConnectionException;
use Utilities\Database\Exceptions\InvalidSecretException;

/**
 * ConnectorTrait class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
trait ConnectorTrait
{

    /**
     * The login data
     *
     * @var array
     */
    private array $temp_login;

    /**
     * @var array
     */
    private array $default_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
    ];

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
            $repeats = (int)floor(32 / strlen($secret));
            $part = str_repeat($secret, $repeats) . substr($secret, 0, 32 - (strlen($secret) * $repeats));
        } else {
            $part = substr($secret, 0, 32);
        }

        return Encryption::encrypt($secret, $part, false);
    }

    /**
     * @todo Add support for SQL files alongside remote connections
     * @param array $data {host, user, pass, db}
     * @param array $options (optional)
     * @return PDO
     */
    public function setConnection(array $data, array $options = []): PDO
    {
        if (isset($data['host'], $data['user'], $data['pass'], $data['db'])) {

            $options = array_merge($this->default_options, $options);
            $dsn = "mysql:host={$data['host']};dbname={$data['db']}";

            $this->connection = new PDO($dsn, $data['user'], $data['pass'], $options);
            $this->temp_login = $data;

            if ($this->connection->errorCode() !== null && $this->connection->errorCode() !== "00000") {
                throw new ConnectionException(sprintf(
                    "Connection refused: %s", $this->connection->errorInfo()[2]
                ));
            }

            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            foreach ($options as $key => $value) {
                $this->connection->setAttribute($key, $value);
            }

            return $this->connection;
        }

        throw new InvalidSecretException("The given data doesn't contain all required fields.");
    }

    /**
     * Get the database connection
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

        return $this->connection;
    }

    /**
     * Save the connection info to a file and then use your secret to login
     *
     * @param string $secret
     * @param bool $overwrite
     * @return bool
     */
    public function saveConnection(string $secret, bool $overwrite = false): bool
    {
        if (!(strlen($secret) >= 8)) {
            throw new ConnectionException(sprintf(
                'Secret key must be larger than 8 characters, given len: %s',
                strlen($secret)
            ));
        }

        $startPath = $_SERVER['DOCUMENT_ROOT'] . "/.database/";

        if (!file_exists($startPath)) {
            mkdir($startPath, 0777, true);
        }

        $fileId = $this->getFileId($secret);
        $filePath = $startPath . $fileId;

        if (file_exists($filePath) && $overwrite !== true) {
            throw new ConnectionException('This secret key has already been in used');
        }

        $encryptedInfo = Encryption::encrypt($secret, json_encode($this->temp_login), false);

        return file_put_contents($filePath, $encryptedInfo) !== false;
    }

    /**
     * Get the server version for the connection.
     *
     * @return string
     */
    public function getServerVersion(): string
    {
        return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

}