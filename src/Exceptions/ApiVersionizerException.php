<?php

namespace Ahmedessam\ApiVersionizer\Exceptions;

use Exception;

class ApiVersionizerException extends Exception
{
    public function __construct($message = 'An error occurred', $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response()->json(['message' => $this->getMessage()], $this->getCode());
    }
}
