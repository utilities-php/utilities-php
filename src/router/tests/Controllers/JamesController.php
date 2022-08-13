<?php
declare(strict_types=1);

namespace UtilitiesTests\Router\Controllers;

use Utilities\Router\Attributes\Route;
use Utilities\Router\Request;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

class JamesController extends \Utilities\Router\Controller
{

    /**
     * @param Request $request
     * @return void
     */
    public function __process(Request $request): void
    {

    }

    #[Route('GET', '/echo')]
    public function echo(array $queries): void
    {
        Response::send(StatusCode::OK, [
            'queries' => $queries,
        ]);
    }

}