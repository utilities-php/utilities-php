<?php
declare(strict_types=1);

namespace UtilitiesTests\Routing\Creative;

use Throwable;
use Utilities\Routing\Application;
use Utilities\Routing\Attributes\Route;
use Utilities\Routing\Controller;
use Utilities\Routing\Origin;
use Utilities\Routing\Request;
use Utilities\Routing\Response;
use Utilities\Routing\Utils\StatusCode;
use UtilitiesTests\Routing\Controllers\JamesController;
use UtilitiesTests\Routing\Controllers\TodoController;

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