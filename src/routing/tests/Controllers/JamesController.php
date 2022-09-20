<?php
declare(strict_types=1);

namespace UtilitiesTests\Routing\Controllers;

use Utilities\Routing\Attributes\Route;
use Utilities\Routing\Controller;
use Utilities\Routing\Request;
use Utilities\Routing\Response;
use Utilities\Routing\Utils\StatusCode;

class JamesController extends Controller
{

    /**
     * @param Request $request
     * @return void
     */
    public function __process(Request $request): void
    {

    }

    #[Route('GET', '/echo')]
    public function echo(array $query): void
    {
        Response::send(StatusCode::OK, [
            'result' => $query ?? [],
        ]);
    }

}