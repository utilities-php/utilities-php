<?php
declare(strict_types=1);

namespace UtilitiesTests\Routing\Controllers;

use Utilities\Routing\Attributes\RateLimit;
use Utilities\Routing\Attributes\Route;
use Utilities\Routing\Controller;
use Utilities\Routing\Request;
use Utilities\Routing\Response;
use Utilities\Routing\Utils\StatusCode;

class TransactionsController extends Controller
{

    /**
     * @param Request $request
     * @return void
     */
    public function __process(Request $request): void
    {

    }

    #[Route('GET', '/')]
    public function transactions(Request $request): void
    {
        Response::send(StatusCode::OK, [
            'id' => 1,
        ]);
    }

    #[RateLimit(6000, 30)]
    #[Route('POST', '/', true)]
    public function createTransaction(Request $request): void
    {
        Response::send(StatusCode::CREATED, [
            'id' => 1,
        ]);
    }

    #[Route('GET', '/{id}')]
    public function getTransaction(Request $request, int $id): void
    {
        Response::send(StatusCode::OK, [
            'id' => $id,
        ]);
    }

    #[Route('PUT', '/{id}/update', true)]
    public function updateTransaction(Request $request, int $id): void
    {
        Response::send(StatusCode::OK, [
            'id' => $id,
        ]);
    }

}