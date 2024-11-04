<?php
namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationDTOException extends \Exception
{
    protected array $errorMessages = [];

    public function __construct(ConstraintViolationListInterface $errors, int $code = 0, ?Throwable $previous = null)
    {
        $this->errorMessages = ['message' => 'validation_failed', 'errors' => []];

        foreach ($errors as $message) {
            $this->errorMessages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        parent::__construct('validation_failed', $code, $previous);
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}