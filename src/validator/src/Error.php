<?php declare(strict_types=1);

namespace Utilities\Validator;

class Error
{

    public function __construct(public string $code, public string $message)
    {
    }

}