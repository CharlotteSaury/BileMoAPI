<?php

namespace App\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthorizationJsonHandler
{
    public function forbiddenResponse(string $action = null, string $entityType)
    {
        if ($action != 'list') {
            $message = 'You are not authorized to '.$action.' this '.$entityType;
        } else {
            $message = 'You are not authorized to access this page.';
        }
        return new JsonResponse([
            'code' => 403,
            'message' => $message
        ],
        Response::HTTP_FORBIDDEN);
    }
}