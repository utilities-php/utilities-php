<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

/**
 * The url constraint.
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
class UrlConstraint extends \Utilities\Validator\Constraint
{

    /**
     * @error_message This value is not a valid url.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isUrl(): bool
    {
        return $this->match('/^(http|https):\/\/[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(\/\S*)?$/');
    }

}