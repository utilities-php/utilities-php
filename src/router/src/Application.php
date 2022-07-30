<?php

namespace Utilities\Router;

use ReflectionMethod;
use ReflectionParameter;
use Utilities\Common\Common;
use Utilities\Common\Connection;
use Utilities\Common\Loop;

/**
 * Application class
 *
 * @link    https://github.com/utilities-php/router
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/router/blob/master/LICENSE (MIT License)
 */
abstract class Application
{

    /**
     * @var array
     */
    private array $controllers = [];

    /**
     * Directories.
     *
     * @var array
     */
    private array $directories = [];

    /**
     * Routes. the second segment of the URL is the key of route.
     *
     * @var array
     */
    private array $routes = [];

    /**
     * Webhooks.
     *
     * @var array
     */
    private array $webhooks = [];

    /**
     * Watchers.
     *
     * @var array
     */
    private array $watchers = [];

    /**
     * Reserved words.
     *
     * @var array
     */
    private array $reservedWords = [
        'webhooks',
        'watchers',
    ];

    /**
     * @var int
     */
    private int $offset = 0;


    /**
     * Set the controllers
     *
     * @param array $input [sector => class]
     * @return void
     */
    public function addController(array $input): void
    {
        foreach ($input as $sector => $class) {
            if (!is_subclass_of($class, '\Utilities\API\Controller')) {
                throw new \RuntimeException(sprintf(
                    'The class `%s` must be a subclass of \Utilities\API\Controller',
                    $class
                ));
            }

            if (in_array($sector, $this->reservedWords)) {
                throw new \RuntimeException(sprintf(
                    'The sector `%s` is reserved.',
                    $sector
                ));
            }

            $this->controllers[$sector] = $class;
        }
    }

    /**
     * Set the routes
     *
     * @param array $input [routeKey => class]
     * @return void
     */
    public function addRouter(array $input): void
    {
        foreach ($input as $routeKey => $class) {
            if (!is_subclass_of($class, '\Utilities\API\Routes')) {
                throw new \RuntimeException(sprintf(
                    'The class `%s` must be a subclass of \Utilities\API\Router',
                    $class
                ));
            }

            if (in_array($routeKey, $this->reservedWords)) {
                throw new \RuntimeException(sprintf(
                    'The route `%s` is reserved.',
                    $routeKey
                ));
            }

            $this->routes[$routeKey] = $class;
        }
    }

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
                throw new \RuntimeException(
                    'The directory ' . $directory . ' does not exist'
                );
            }

            $this->directories[$key] = $directory;
        }
    }

    /**
     * Add a webhook.
     *
     * @param array $webhooks ['key' => function () { ... }, ...]
     * @return void
     */
    public function addWebhook(array $webhooks): void
    {
        foreach ($webhooks as $key => $webhook) {
            if (!is_callable($webhook)) {
                throw new \RuntimeException(sprintf(
                    'The webhook `%s` is not callable.',
                    $key
                ));
            }

            $this->webhooks[$key] = $webhook;
        }
    }

    /**
     * Add a watcher.
     *
     * @param array $watchers [['key', 'class', 'interval'], ...]
     * @return void
     */
    public function addWatcher(array $watchers): void
    {
        foreach ($watchers as $watcher) {
            if (!is_array($watcher) || !is_string($watcher['class']) || !is_numeric($watcher['interval'])) {
                throw new \RuntimeException(sprintf(
                    'The watcher `%s` is not valid.',
                    $watcher
                ));
            }

            if (!method_exists($watcher['class'], '__run')) {
                throw new \RuntimeException(sprintf(
                    'The watcher `%s` does not have a __run method.',
                    $watcher['class']
                ));
            }

            $this->watchers[] = $watcher;
        }
    }

    /**
     * Find the directories, route and controller by defined values
     * The priority is: directory > self > route > controller
     *
     * @param array $options ["insensitive", "offset"]
     * @return void
     */
    public function findPath(array $options = []): void
    {
        $this->offset = $options['offset'] ?? 0;

        $request = $this->getRequest($this->offset);

        if (self::passDataToMethod($this, $request['method'])) {
            return;
        }

        if ($request['sector'] === "webhooks" && $request['method'] !== null) {
            $this->findWebhook($request, $options['insensitive'] ?? true);
            return;
        }

        if (Common::insensitiveString($request['sector'], "watchers") && Common::insensitiveString($request['method'], "execute")) {
            $this->executeWatchers();
            return;
        }

        if ($request['sector'] !== null && $request['method'] !== null) {
            $this->findDirectory($request, $options['insensitive'] ?? true);
        }

        $this->findRoute($request, $options['insensitive'] ?? true);
        $this->findController($request, $options['insensitive'] ?? true);
    }

    /**
     * Get the request
     *
     * @param int $offset
     * @return array ["sector", "method"]
     */
    private function getRequest(int $offset): array
    {
        $segments = URLs::getSegments();
        $result = [];

        if (count($segments) == 1) {
            $result['sector'] = null;
            $result['method'] = $segments[0];
            return $result;
        }

        if (count($segments) == 2) {
            $result['sector'] = $segments[0];
            $result['method'] = $segments[1];
            return $result;
        }

        $segments = array_slice($segments, $offset);
        $result['sector'] = $segments[0];
        $result['method'] = array_pop($segments);

        return $result;
    }

    /**
     * Pass the request to the controller for self-processing
     *
     * @param object $class
     * @param string $method
     * @param array $mergeWith (optional)
     * @return bool
     */
    public static function passDataToMethod(object $class, string $method, array $mergeWith = []): bool
    {
        if (!method_exists($class, $method)) {
            return false;
        }

        $reflection = new ReflectionMethod($class, $method);
        $arguments = $reflection->getParameters();

        call_user_func_array([$class, $method], self::generatePassingData($arguments, $mergeWith));

        return true;
    }

    /**
     * Generate passing data
     *
     * @param ReflectionParameter[] $require ["headers", "queries", "params", "input"]
     * @param array $mergeWith
     * @return array
     */
    private static function generatePassingData(array $require, array $mergeWith = []): array
    {
        $methodParams = [];
        $fetchRequirements = [];

        foreach ($require as $key => $value) {
            $fetchRequirements[$key] = $value->getName();
        }

        foreach ($fetchRequirements as $key) {
            if ($key == 'headers') {
                $methodParams['headers'] = getallheaders();
            }
            if ($key == 'queries') {
                $methodParams['queries'] = array_merge($_GET, $_POST);
            }
            if ($key == 'params') {
                $methodParams['params'] = $request['params'] ?? [];
            }
            if ($key == 'input') {
                $methodParams['input'] = file_get_contents('php://input');
            }
        }

        return array_merge($methodParams, $mergeWith);
    }

    /**
     * Search for the webhook
     *
     * @param array $request
     * @param bool $insensitive
     * @return void
     */
    private function findWebhook(array $request, bool $insensitive): void
    {
        if (($webhook = $this->getWebhook($request['method'], $insensitive)) !== false) {
            Application::passDataToCallback($webhook);
        }
    }

    /**
     * Get the webhook by array key
     *
     * @param string $key
     * @param bool $insensitive
     * @return string|bool
     */
    private function getWebhook(string $key, bool $insensitive = false): string|bool
    {
        if ($insensitive) {
            return array_change_key_case($this->webhooks)[strtolower($key)] ?? false;
        }
        return $this->webhooks[$key] ?? false;
    }

    /**
     * Pass data to anonymous function
     *
     * @param callable $callback
     * @param array $mergeWith (optional)
     * @return bool
     */
    public static function passDataToCallback(callable $callback, array $mergeWith = []): bool
    {
        if (!is_callable($callback)) {
            return false;
        }

        try {

            $reflection = new \ReflectionFunction($callback);
            $arguments = $reflection->getParameters();

            call_user_func_array($callback, self::generatePassingData($arguments, $mergeWith));

            return true;

        } catch (\Exception $e) {
            throw new \RuntimeException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
    }

    /**
     * execute the watchers
     *
     * @return void
     */
    private function executeWatchers(): void
    {
        header('Content-Type: application/json');

        Connection::closeConnection(Common::prettyJson([
            'ok' => true,
            'message' => 'Services are running...'
        ]));

        $last_runs = [];
        $startTime = time();
        Loop::run(function () use (&$last_runs, $startTime) {
            if (time() - $startTime > 60) {
                Loop::stop();
            }

            foreach ($this->watchers as $watcher) {
                if (time() - $last_runs[$watcher['key']] >= $watcher['interval']) {
                    (new $watcher['class']())->__run();
                    $last_runs[$watcher['key']] = time();
                }
            }
        });

        die(200);
    }

    /**
     * Search for the directory
     *
     * @param array $request
     * @param bool $insensitive
     * @return void
     */
    private function findDirectory(array $request, bool $insensitive): void
    {
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

    /**
     * @param array $request
     * @param bool $insensitive
     * @return void
     */
    private function findRoute(array $request, bool $insensitive): void
    {
        if (($class = $this->getRoute($request['sector'], $insensitive)) !== false) {
            /** @var Routes $target */
            $target = new $class($request['sector']);

            $request = $this->getRequest($this->offset + 1);

            $target->__process($request);
        }
    }

    /**
     * Get the route by array key
     *
     * @param ?string $key
     * @param bool $insensitive
     * @return string|bool
     */
    private function getRoute(?string $key, bool $insensitive = false): string|bool
    {
        if (!is_string($key)) {
            return false;
        }

        if ($insensitive) {
            return array_change_key_case($this->routes)[strtolower($key)] ?? false;
        }

        return $this->routes[$key] ?? false;
    }

    /**
     * When the route has been called, this method will be called.
     *
     * @param array $request ["sector", "method"]
     * @return void
     */
    abstract public function __process(array $request): void;

    /**
     * @param array $request
     * @param bool $insensitive
     * @return void
     */
    private function findController(array $request, bool $insensitive): void
    {
        if (($class = $this->getController($request['sector'], $insensitive))) {
            $nextSegmentOfMethod = $this->findIndexOfSegment($request['method'], $insensitive);
            $middleSegments = array_slice(URLs::getSegments(), $this->offset + 1, count(URLs::getSegments()) - 2);
            new $class($request['method'], $middleSegments ?? []);
        }
    }

    /**
     * Get the controller by array key
     *
     * @param ?string $key
     * @param bool $insensitive
     * @return string|bool
     */
    private function getController(?string $key, bool $insensitive = false): string|bool
    {
        if (!is_string($key)) {
            return false;
        }

        if ($insensitive) {
            return array_change_key_case($this->controllers)[strtolower($key)] ?? false;
        }

        return $this->controllers[$key] ?? false;
    }

    /**
     * Find index of segment
     *
     * @param string $segment
     * @param bool $insensitive
     * @return int
     */
    private function findIndexOfSegment(string $segment, bool $insensitive): int
    {
        $segments = URLs::getSegments();

        if ($insensitive) {
            $segment = strtolower($segment);
        }

        foreach ($segments as $index => $value) {
            if ($insensitive) {
                $value = strtolower($value);
            }

            if ($value == $segment) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * Resolve the route
     *
     * @param array $settings (optional)
     * @return void
     */
    public function resolve(array $settings = []): void
    {
        $class = $this;

        set_exception_handler(function (\Throwable $throwable) use ($class) {
            if (!method_exists($class, '__exception')) {
                throw new \RuntimeException(
                    $throwable->getMessage(),
                    $throwable->getCode(),
                    $throwable
                );
            }

            $class::__exception($throwable);
            die(500);
        });

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (str_ends_with($_SERVER['HTTP_ORIGIN'], 'litehex.com') || str_starts_with($_SERVER['HTTP_ORIGIN'], 'localhost')) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Max-Age: 86400");
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(200);
        }

        session_set_cookie_params(86400, '/;SameSite=none', '', false, true);
        session_start();

        if (method_exists($this, '__process')) {
            $this->__process([
                'sector' => URLs::segment(0),
                'method' => URLs::segment(1),
            ]);
        }
    }

    /**
     * On exception, this method will be called.
     *
     * @param \Throwable $throwable
     * @return void
     */
    abstract public function __exception(\Throwable $throwable): void;

}