<?php

namespace App\Handler;

use App\Exception\ResourceValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintsViolationHandler
{
    public function validate(ConstraintViolationListInterface $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf('Field %s: %s ', $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new ResourceValidationException($message);
        }

        return;
    }
}
