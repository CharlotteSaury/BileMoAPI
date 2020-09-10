<?php

namespace App\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthorizationJsonHandler
{
    public function forbiddenResponse(string $action = null, string $entityType = null, $data = null)
    {
        if ($action == 'list') {
            $message = 'You are not authorized to access this page.';
        } elseif ($action == 'password') {
            $message = 'You are not allowed to update client\'s password';
        } elseif ($action == 'update' && $entityType == 'client') {
            $message = 'You are not allowed to update field : '.$data;
        } else {
            $message = 'You are not authorized to '.$action.' this '.$entityType;
        }
        return new JsonResponse([
                'code' => 403,
                'message' => $message
            ],
            Response::HTTP_FORBIDDEN
        );
    }
}