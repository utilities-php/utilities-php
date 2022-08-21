<?php
declare(strict_types=1);

namespace UtilitiesTests\Router\Creative;

use Utilities\Router\Attributes\Route;
use Utilities\Router\Controller;
use Utilities\Router\Origin;
use Utilities\Router\Request;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;
use UtilitiesTests\Router\Controllers\JamesController;
use UtilitiesTests\Router\Controllers\TodoController;

class App extends \Utilities\Router\Application
{

    /**
     * @param Request $request
     * @return void
     */
    public function __process(Request $request): void
    {
        Response::$PROCESS_KILLER = false;

        self::addController([
            Controller::__create('/james', JamesController::class),
            Controller::__create('/api/todo', TodoController::class),
        ]);

        Origin::addDomain('localhost', false, 60 * 60 * 24);
        Origin::addDomain('example.com', true);
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function __exception(\Exception $exception): void
    {
        if ($_SERVER['REMOTE_ADDR'] === '31.187.72.206') {
            Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
                'file' => "{$exception->getFile()}#{$exception->getLine()}",
                'message' => $exception->getMessage(),
                'trance' => explode("\n", $exception->getTraceAsString()),
            ]);
        }

        Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
            'description' => "Internal Server Error",
        ]);
    }

    /**
     * @param array $queries
     * @return void
     */
    #[Route('GET', '/echo')]
    public function echo(array $queries): void
    {
        Response::send(StatusCode::OK, [
            'result' => $queries ?? [],
        ]);
    }

}