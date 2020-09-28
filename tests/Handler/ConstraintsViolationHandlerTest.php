<?php

namespace App\Tests\Handler;

use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Handler\ConstraintsViolationHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintsViolationHandlerTest extends TestCase
{
    /**
     * Test if method validate return null when $violations is empty.
     */
    public function testNoViolationsValidate()
    {
        $violations = new ConstraintViolationList();
        $constraintsViolationHandler = new ConstraintsViolationHandler();

        $this->assertSame(null, $constraintsViolationHandler->validate($violations));
    }

    /**
     * Test if method validate throw ResourceValidationException when $violations contains one violation.
     */
    public function testViolationsValidate()
    {
        $violations = new ConstraintViolationList();

        $message = 'This client already exists';
        $messageTemplate = 'This client already exists';
        $parameters = [
            'email' => 'existingemail@email.com',
        ];
        $root = new Client();
        $root->setEmail('existingemail@email.com');
        $propertyPath = 'email';
        $invalidValue = 'existingemail@email.com';

        $violation = new ConstraintViolation($message, $messageTemplate, $parameters, $root, $propertyPath, $invalidValue);
        $violations->add($violation);

        $constraintsViolationHandler = new ConstraintsViolationHandler();

        $this->expectException('App\Exception\ResourceValidationException');
        $this->expectExceptionMessage('The JSON sent contains invalid data. Here are the errors you need to correct: Field email: This client already exists ');
        $constraintsViolationHandler->validate($violations);
    }
}
