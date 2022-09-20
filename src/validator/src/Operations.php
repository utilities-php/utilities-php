<?php
declare(strict_types=1);

namespace Utilities\Validator;

/**
 * The Operations class.
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
trait Operations
{

    /**
     * Numeric operations.
     *
     * @var array
     */
    private static array $NUMERIC_OPERATIONS = [
        'in',
        'is',
        'isDate',
        'isNot',
        'isTime',
        'isTimestamp',
        'max',
        'min',
        'notIn',
        'regex'
    ];

    /**
     * Array operations.
     *
     * @var array
     */
    private static array $ARRAY_OPERATIONS = [
        'in',
        'is',
        'isNot',
        'length',
        'maxLength',
        'minLength',
        'notIn',
        'regex'
    ];

    /**
     * Object operations.
     *
     * @var array
     */
    private static array $OBJECT_OPERATIONS = [
        'is',
        'isNot',
        'regex'
    ];

    /**
     * The email operations.
     *
     * @var array
     */
    private static array $EMAIL_OPERATIONS = [
        'domainIs',
        'domainIsIn',
        'domainIsNot',
        'domainIsNotIn',
        'is',
        'isLocal',
        'isNot',
        'regex'
    ];

    /**
     * The url operations.
     *
     * @var array
     */
    private static array $URL_OPERATIONS = [
        'is',
        'isNot',
        'domainIs',
        'domainIsNot',
        'domainIsIn',
        'domainIsNotIn',
        'isLocal',
        'protocolIs',
        'protocolIsNot',
        'protocolIsIn',
        'protocolIsNotIn',
        'regex'
    ];

    /**
     * The ip operations.
     *
     * @var array
     */
    private static array $IP_OPERATIONS = [
        'is',
        'isAnycast',
        'isBroadcast',
        'isGlobal',
        'isIn',
        'isIpv4',
        'isIpv6',
        'isLinkLocal',
        'isLocal',
        'isMac',
        'isMulticast',
        'isNot',
        'isNotIn',
        'isPrivate',
        'isPublic',
        'isReserved',
        'regex'
    ];

    /**
     * The int operations.
     *
     * @var array
     */
    private static array $INT_OPERATIONS = [
        'is',
        'isEven',
        'isIn',
        'isNot',
        'isNotIn',
        'isOdd',
        'IsPrime',
        'isGreaterThan',
        'isLessThan',
        'isGreaterThanOrEqual',
        'isLessThanOrEqual',
        'isBetween',
        'isNotBetween',
        'regex'
    ];

}