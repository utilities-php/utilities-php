<?php
declare(strict_types=1);

namespace Utilities\Validator\Constraints;

/**
 * The uuid constraint.
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
class UuidConstraint extends \Utilities\Validator\Constraint
{

    /**
     * @error_message This value is not a valid uuid.
     * @error_code INVALID
     *
     * @return bool
     */
    public function isUuid(): bool
    {
        return $this->match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
    }

}