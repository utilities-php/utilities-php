<?php
declare(strict_types=1);

namespace UtilitiesTests\Router\Creative;

use Throwable;
use Utilities\Router\Application;
use Utilities\Router\Attributes\Route;
use Utilities\Router\Controller;
use Utilities\Router\Origin;
use Utilities\Router\Request;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;
use UtilitiesTests\Router\Controllers\JamesController;
use UtilitiesTests\Router\Controllers\TodoController;

class App extends Application
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
     * @param Throwable $throwable
     * @return void
     */
    public function __exception(Throwable $throwable): void
    {
        if ($_SERVER['REMOTE_ADDR'] === '31.187.72.206') {
            Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
                'file' => "{$throwable->getFile()}#{$throwable->getLine()}",
                'message' => $throwable->getMessage(),
                'trance' => explode("\n", $throwable->getTraceAsString()),
            ]);
        }

        Response::send(StatusCode::INTERNAL_SERVER_ERROR, [
            'description' => "Internal Server Error",
        ]);
    }

    /**
     * @param array $query
     * @return void
     */
    #[Route('GET', '/echo')]
    public function echo(array $query): void
    {
        Response::send(StatusCode::OK, [
            'result' => $query ?? [],
        ]);
    }

}