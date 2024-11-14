<?php
namespace App\Controller\Api\Request;

use App\Formatter\ValidationErrorsFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    private array $errorMessages = ['message' => 'validation_failed', 'errors' => []];

    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack       $requestStack
    )
    {
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    public function isValid(): bool
    {
        $this->populate();

        $violations = $this->validator->validate($this);

        if (count($violations) < 1) {
            return true;
        }

        $this->errorMessages['errors'] = ValidationErrorsFormatter::formatValidationErrors($violations);

        return false;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
