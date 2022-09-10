<?php
declare(strict_types=1);

namespace Utilities\Database;

use Utilities\Common\Encryption;

/**
 * JsonDatabase Class
 *
 * @link    https://github.com/utilities-php/database
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/database/blob/master/LICENSE (MIT License)
 */
class JsonDatabase
{

    private string $data_path;

    private array $data_holder;

    private string $secret;

    /**
     * @param string $path
     * @param string $secret
     */
    public function __construct(string $path, string $secret = "")
    {
        $this->data_path = $path;
        $this->secret = $secret;
        if (file_exists($path)) {
            $this->data_holder = $this->read($path);
        } else {
            $this->catch([]);
            $this->save();
        }
    }

    /**
     * @param $path
     * @return array|bool
     */
    private function read($path): array|bool
    {
        if (file_exists($path)) {
            $encryption = new Encryption();
            $content = file_get_contents($path);
            if ($this->secret != "") $content = $encryption->decrypt($this->secret, $content);
            if ($content == null) $content = "{}";
            return $this->data_holder = json_decode($content, true);
        }
        return false;
    }

    /**
     * @param array $database
     */
    public function catch(array $database): void
    {
        $this->data_holder = $database;
    }

    /**
     * @param ?array $database
     * @return bool
     */
    public function save(array $database = null): bool
    {
        $encryption = new Encryption();
        $content = json_encode($database == null ? $this->data_holder : $database);
        if ($this->secret != "") {
            $content = $encryption->encrypt($this->secret, $content);
        }

        return file_put_contents($this->data_path, $content);
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->data_holder;
    }

}