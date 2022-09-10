<?php
declare(strict_types=1);

namespace Utilities\Router\Traits;

use RuntimeException;

/**
 * DirectoryTrait class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
trait DirectoryTrait
{

    /**
     * Directories.
     *
     * @var array
     */
    private array $directories = [];

    /**
     * Add directories to the autoloader
     *
     * @param array $directories e.g. ["key" => __dir__ . "/../key/"]
     * @return void
     */
    public function addDirectory(array $directories): void
    {
        foreach ($directories as $key => $directory) {
            if (!is_dir($directory)) {
                throw new RuntimeException(
                    'The directory ' . $directory . ' does not exist'
                );
            }

            $this->directories[$key] = $directory;
        }
    }

    /**
     * Search for the directory
     *
     * @param array $request
     * @param bool $insensitive
     * @return void
     */
    private function findDirectory(array $request, bool $insensitive = false): void
    {
        if (is_string($request['sector']) && is_string($request['method'])) {
            if (($directory = $this->getDirectory($request['sector'], $insensitive)) !== false) {
                if (!str_ends_with($directory, '/')) {
                    $directory .= '/';
                }

                $filename = str_contains($request['method'], '.') ? $request['method'] : $request['method'] . '.php';
                $file = $directory . $filename;

                if (file_exists($file)) {
                    $fileMime = mime_content_type($file);

                    if ($fileMime != 'text/php') {
                        header('Content-Type: ' . $fileMime);
                    }

                    include_once $file;
                    die(200);
                }
            }
        }
    }

    /**
     * Get the directories
     *
     * @param ?string $key
     * @param bool $insensitive
     * @return string|false
     */
    private function getDirectory(?string $key, bool $insensitive): string|false
    {
        if (!is_string($key)) {
            return false;
        }

        if ($insensitive) {
            return array_change_key_case($this->directories)[strtolower($key)] ?? false;
        }

        return $this->directories[$key] ?? false;
    }

}