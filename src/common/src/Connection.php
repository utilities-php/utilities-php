<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * Connection class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Connection
{

    /**
     * Close the connection with client and let server do rest of the job
     *
     * @param string $text
     * @return void
     */
    public static function closeConnection(string $text = ''): void
    {
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        echo($text);
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
    }

}