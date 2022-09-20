<?php
declare(strict_types=1);

namespace Utilities\Database\Constraints;

/**
 * The strictData trait.
 *
 *
 * This is part of the Utilities package.
 *
 * @link https://github.com/utilities-php/utilities-php
 * @author Shahrad Elahi <shahrad@litehex.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait strictData
{

    /**
     * Strict data types
     *
     * @var array
     */
    protected static array $COLUMNS_TYPE = [];

}