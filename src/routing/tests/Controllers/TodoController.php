<?php
declare(strict_types=1);

namespace UtilitiesTests\Routing\Controllers;

use Utilities\Routing\Attributes\RateLimit;
use Utilities\Routing\Attributes\Route;
use Utilities\Routing\Controller;
use Utilities\Routing\Request;
use Utilities\Routing\Response;
use Utilities\Routing\Utils\StatusCode;

#[RateLimit(60, 10)]
class TodoController extends Controller
{

    public function __process(Request $request): void
    {

    }

    public function index(): void
    {
        Response::send(StatusCode::OK, [
            'description' => "You are on the index page",
        ]);
    }

    #[Route('POST', '/')]
    public function create(string $input): void
    {
        Response::send(StatusCode::OK, [
            'result' => [
                'input' => $input,
            ],
        ]);
    }

    #[Route('DELETE', '/{id}')]
    public function destroy(array $params): void
    {
        Response::send(StatusCode::OK, [
            'result' => [
                'id' => $params['id'],
            ],
        ]);
    }

}