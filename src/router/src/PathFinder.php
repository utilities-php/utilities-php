<?php
declare(strict_types=1);

namespace Utilities\Router;

use Symfony\Component\Mime\MimeTypes;

/**
 * PathFinder class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class PathFinder
{

    /**
     * @param string $filePath pass the file path or file name or extension
     * @return string
     */
    public static function getMimeType(string $filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return (new MimeTypes())->getMimeTypes($extension)[0] ?? 'application/octet-stream';
    }

    /**
     * Search for a file in the given path.
     *
     * @param string $path The path to search in.
     * @param string $fileName The file name to search for.
     * @return string|false
     */
    public static function search(string $path, string $fileName): string|false
    {
        $file = $path . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Search for environment file.
     *
     * @param string $path The path to search in.
     * @param bool $alternative [optional] If true, it will search for .env.* files too.
     * @return string|false
     */
    public static function searchEnvFile(string $path, bool $alternative = false): string|false
    {
        $file = $path . DIRECTORY_SEPARATOR . '.env';
        if (file_exists($file)) {
            return $file;
        }

        if ($alternative) {
            $envFiles = glob($path . DIRECTORY_SEPARATOR . '.env.*');
            if (count($envFiles) > 0) {
                return $envFiles[0];
            }
        }

        return false;
    }

}