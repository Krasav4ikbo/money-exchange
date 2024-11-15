<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiBaseController extends AbstractController
{
    protected function sendFailedValidationResponse(array $errorMessages): Response
    {
        $body = [
            'status' => 'failed',
            'messages' => $errorMessages
        ];

        return $this->json($body, Response::HTTP_BAD_REQUEST);
    }

    protected function sendSuccessResponse(mixed $amount): Response
    {
        $body = [
            'status' => 'success',
            'amount' => $amount
        ];

        return $this->json($body, Response::HTTP_OK);
    }
}