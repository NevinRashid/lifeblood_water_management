<?php

namespace App\Exceptions;

use Exception;

class CrudException extends Exception
{
    public function __construct(string $message = 'Operation Error', protected int $status = 400)
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage()
        ], $this->status);
    }
}
