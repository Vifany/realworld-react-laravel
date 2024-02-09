<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

trait RenderValidationExceptionAsJson
{
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return response()->json(
            data  : ['errors' => ['body' => $this->implodeErrors($e->errors())]],
            status: 422,
        );
    }

    protected function implodeErrors(array $errors): string
    {
        return collect($errors)->implode(
            value: static fn($messages) => implode(
                separator: ' ',
                array    : $messages
            ),
            glue : ' '
        );
    }
}
