<?php
declare(strict_types=1);

namespace UtilitiesTests\Router\Controllers;

use Utilities\Router\Attributes\RateLimit;
use Utilities\Router\Attributes\Route;
use Utilities\Router\Controller;
use Utilities\Router\Request;
use Utilities\Router\Response;
use Utilities\Router\Utils\StatusCode;

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