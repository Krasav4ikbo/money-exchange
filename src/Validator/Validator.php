<?php
namespace App\Validator;

use App\Exception\ValidationDTOException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(protected ValidatorInterface $validator) {}

    /**
     * @throws ValidationDTOException
     */
    public function validate(mixed $value): void
    {
        $errors = $this->validator->validate($value);

        if (count($errors) > 0) {
            throw new ValidationDTOException($errors);
        }
    }
}