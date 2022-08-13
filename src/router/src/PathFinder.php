<?php
declare(strict_types=1);

namespace Utilities\Router;

use Utilities\Router\Utils\MimeType;

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
        $mime = MimeType::get($extension);
        return $mime !== false ? $mime : 'text/plain';
    }

}