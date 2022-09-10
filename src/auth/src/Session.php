<?php
declare(strict_types=1);

namespace Utilities\Auth;

use Utilities\Common\Common;

/**
 * Session class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
class Session
{

    /**
     * @var User
     */
    private static User $user;

    /**
     * @var array
     */
    private static array $cookieParams = [];

    /**
     * Set session value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Check if session value exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Delete session value
     *
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     *
     * @return void
     */
    public static function destroy(): void
    {
        session_destroy();
    }

    /**
     * Set flash value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get old value
     *
     * @param string $key
     * @return mixed
     */
    public static function old(string $key): mixed
    {
        return $_SESSION['_old'][$key] ?? null;
    }

    /**
     * Check if old value exists
     *
     * @param string $key
     * @return bool
     */
    public static function hasOld(string $key): bool
    {
        return isset($_SESSION['_old'][$key]);
    }

    /**
     * Delete old value
     *
     * @param string $key
     * @return void
     */
    public static function deleteOld(string $key): void
    {
        unset($_SESSION['_old'][$key]);
    }

    /**
     * Destroy old values
     *
     * @return void
     */
    public static function destroyOld(): void
    {
        $_SESSION['_old'] = [];
    }

    /**
     * Regenerate session id (keeps session alive)
     *
     * @return void
     */
    public static function regenerateId(): void
    {
        session_regenerate_id(false);
    }

    /**
     * Regenerate session token
     *
     * @return void
     */
    public static function regenerateToken(): void
    {
        $_SESSION['_token'] = bin2hex(Common::randomString(32));
    }

    /**
     * Refresh session lifetime
     *
     * @param int|null $lifetime (optional) default: ini_get('session.gc_maxlifetime')
     * @return void
     */
    public static function refresh(int $lifetime = null): void
    {
        if ($lifetime === null) {
            $lifetime = intval(ini_get('session.gc_maxlifetime'));
        }

        static::setCookieParams([
            'lifetime' => $lifetime,
        ]);

        static::start();
    }

    /**
     * Start session
     *
     * @param array $params (optional) session cookie params
     * @return void
     */
    public static function start(array $params = []): void
    {
        self::setCookieParams($params);

        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(static::$cookieParams);
            session_start();
        }

        if (empty($_SESSION)) {
            $_SESSION = [];
        }

        $initialKeys = [
            '_flash' => [],
            '_old' => [],
            '_log' => [],
        ];

        foreach ($initialKeys as $key => $value) {
            if (empty($_SESSION[$key])) {
                $_SESSION[$key] = $value;
            }
        }

        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(Common::randomString(32));
        }
    }

    /**
     * move session to another session_name
     *
     * @param string $sessionName
     * @return void
     */
    public static function move(string $sessionName): void
    {
        self::regenerate();
        session_name($sessionName);
        self::start();
    }

    /**
     * Regenerate session (destroy and start)
     *
     * @return void
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * set session id
     *
     * @param string $id
     * @return void
     */
    public static function setId(string $id): void
    {
        session_id($id);
    }

    /**
     * get session id
     *
     * @return string
     */
    public static function getId(): string
    {
        return session_id();
    }

    /**
     * get session name
     *
     * @return string
     */
    public static function getName(): string
    {
        return session_name();
    }

    /**
     * get session status
     *
     * @return int (0 = disabled, 1 = none, 2 = active)
     */
    public static function getStatus(): int
    {
        return session_status();
    }

    /**
     * get session cookie params
     *
     * @return array
     */
    public static function getCookieParams(): array
    {
        return static::$cookieParams;
    }

    /**
     * Set session cookie params
     *
     * @param array $params
     * @return void
     */
    public static function setCookieParams(array $params): void
    {
        $defaults = [
            'lifetime' => intval(ini_get('session.gc_maxlifetime')),
            'domain' => ini_get('session.cookie_domain'),
            'httponly' => ini_get('session.cookie_httponly'),
        ];

        foreach ($defaults as $key => $value) {
            if (!isset(static::$cookieParams[$key]) && !is_null($value)) {
                static::$cookieParams[$key] = $value;
            }
        }

        static::$cookieParams = array_merge(static::$cookieParams, $params);
    }

    /**
     * set session save path
     *
     * @param string $path
     * @return void
     */
    public static function setSavePath(string $path): void
    {
        session_save_path($path);
    }

    /**
     * get session save path
     *
     * @return string
     */
    public static function getSavePath(): string
    {
        return session_save_path();
    }

    /**
     * set session cache limiter
     *
     * @param string $cache_limiter
     * @return void
     */
    public static function setCacheLimiter(string $cache_limiter): void
    {
        session_cache_limiter($cache_limiter);
    }

    /**
     * get session cache limiter
     *
     * @return string
     */
    public static function getCacheLimiter(): string
    {
        return session_cache_limiter();
    }

    /**
     * set session cache expire
     *
     * @param int $expire
     * @return void
     */
    public static function setCacheExpire(int $expire): void
    {
        session_cache_expire($expire);
    }

    /**
     * get session cache expire
     *
     * @return int
     */
    public static function getCacheExpire(): int
    {
        return session_cache_expire();
    }

    /**
     * log session data
     *
     * @param string $data
     * @return void
     */
    public static function log(string $data): void
    {
        $_SESSION['_log'][] = $data;
    }

    /**
     * get session log
     *
     * @param string|null $key (optional) Key a specific log or all logs
     * @return mixed
     */
    public static function getLog(string $key = null): mixed
    {
        if ($key === null) {
            return $_SESSION['_log'] ?? [];
        }

        return $_SESSION['_log'][$key] ?? null;
    }

    /**
     * clear session log
     *
     * @return void
     */
    public static function clearLog(): void
    {
        $_SESSION['_log'] = [];
    }

    /**
     * check if there's any session log
     *
     * @return bool
     */
    public static function hasLog(): bool
    {
        return isset($_SESSION['_log']) && count($_SESSION['_log']) > 0;
    }

    /**
     * delete session log
     *
     * @param string $key
     * @return void
     */
    public static function deleteLog(string $key): void
    {
        unset($_SESSION['_log'][$key]);
    }

    /**
     * destroy session log
     *
     * @return void
     */
    public static function destroyLog(): void
    {
        unset($_SESSION['_log']);
        $_SESSION['_log'] = [];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if ($name === 'user') {
            return static::getUser();
        }

        return call_user_func_array([static::getUser(), $name], $arguments);
    }

    /**
     * get user and initiate it if it doesn't exist
     *
     * @return User
     */
    public static function getUser(): User
    {
        if (!isset($_SESSION['_user'])) {
            $_SESSION['_user'] = new User();
        }

        return $_SESSION['_user'];
    }

}