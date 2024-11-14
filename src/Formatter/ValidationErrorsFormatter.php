<?php
namespace App\Formatter;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorsFormatter
{
    public static function formatValidationErrors(ConstraintViolationListInterface $violations): array
    {
        $messages = [];
        foreach ($violations as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        return $messages;
    }
}