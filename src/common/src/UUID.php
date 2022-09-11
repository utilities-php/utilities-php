<?php
declare(strict_types=1);

namespace Utilities\Common;

/**
 * UUID class - Everything about UUID
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class UUID
{

    /**
     * Generate a UUID
     *
     * @param string $content - The content to generate a UUID from, if not provided, a random UUID will be generated
     * @return string
     */
    public static function generate(string $content = ''): string
    {
        if (empty($content)) {
            return self::generateRandom();
        }

        return self::generateFromContent($content);
    }

    /**
     * Generate a random UUID
     *
     * @return string
     */
    private static function generateRandom(): string
    {
        return self::generateFromContent(uniqid((string)mt_rand(), true));
    }

    /**
     * Generate a UUID from a content
     *
     * @param string $content
     * @return string
     */
    private static function generateFromContent(string $content): string
    {
        $namespace = "1f5dcd39-3a03-4007-8640-73030245dafd";
        $nhex = str_replace(array('-', '{', '}'), '', $namespace);
        $nstr = '';
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }
        $hash = sha1($nstr . $content);
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * Validate a UUID
     *
     * @param string $uuid
     * @return bool
     */
    public static function validate(string $uuid): bool
    {
        return preg_match('/^{?[0-9a-f]{8}-?[0-9a-f]{4}-?[0-9a-f]{4}-?' . '[0-9a-f]{4}-?[0-9a-f]{12}}?$/i', $uuid) === 1;
    }

}