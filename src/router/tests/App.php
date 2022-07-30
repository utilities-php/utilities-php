<?php
declare(strict_types=1);

namespace UtilitiesTests\Router;

class App extends \Utilities\Router\Application
{

    /**
     * @param array $request
     * @return void
     */
    public function __process(array $request): void
    {
        self::addController([
            'william' => William::class,
            'james' => James::class,
        ]);

        self::findPath(['insensitive' => true]);

        \Utilities\Common\Connection::sendStatus(404, false, [
            'description' => "Not Found",
        ]);
    }

    /**
     * @param \Throwable $throwable
     * @return void
     */
    public function __exception(\Throwable $throwable): void
    {
        if ($_SERVER['REMOTE_ADDR'] === '145.14.158.27') {
            \Utilities\Common\Connection::sendStatus(500, false, [
                'file' => "{$throwable->getFile()}#{$throwable->getLine()}",
                'message' => $throwable->getMessage(),
                'trace' => explode("\n", $throwable->getTraceAsString()),
            ]);
        }

        \Utilities\Common\Connection::sendStatus(500, false, [
            'description' => "Internal Server Error",
        ]);
    }

    /**
     * @return void
     */
    public function echo(): void
    {
        \Utilities\Common\Printer::deco('requiredClass: ' . self::class);
    }

}

class James extends \Utilities\Router\Controller
{

    /**
     * @param array $queries
     * @return void
     */
    public function echo(array $queries): void
    {
        \Utilities\Common\Printer::deco('requiredClass: ' . self::class);
    }

}

class William extends \Utilities\Router\Controller
{

    /**
     * @param array $queries
     * @return void
     */
    public function echo(array $queries): void
    {
        \Utilities\Common\Printer::deco('requiredClass: ' . self::class);
    }

}

(new App())->resolve();