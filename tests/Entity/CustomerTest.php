<?php

namespace App\Tests\Entity;

use App\Entity\Customer;
use App\Tests\Utils\AssertHasErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerTest extends KernelTestCase
{
    use AssertHasErrors;
    use FixturesTrait;

    /**
     * Create a valid entity for tests.
     */
    public function getEntity(): Customer
    {
        $fixtures = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/clients.yaml',
            dirname(__DIR__).'/fixtures/customers.yaml',
        ]);
        $customer = new Customer();
        $customer->setEmail('validcustomer@email.com')
            ->setFirstName('customer')
            ->setLastName('valid')
            ->setCreatedAt(new \DateTime())
            ->addClient($fixtures['client1']);

        return $customer;
    }

    /**
     * Assert valid entity is valid.
     *
     * @return void
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * Assert invalid entity (email, firstName, lastName) in invalid.
     *
     * @return void
     */
    public function testInvalidEntity()
    {
        $invalidCustomer = $this->getEntity();
        $invalidCustomer->setEmail('invalidcustomer.com')
            ->setFirstName('c')
            ->setLastName('');
        $this->assertHasErrors($invalidCustomer, 4);
    }

    /**
     * Assert customer unicity with email.
     *
     * @return void
     */
    public function testInvalidUniqueEmail()
    {
        $invalidCustomer = $this->getEntity()->setEmail('customer1@email.com');
        $this->assertHasErrors($invalidCustomer, 1);
    }
}
