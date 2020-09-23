<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Tests\Utils\AssertHasErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    use AssertHasErrors;
    use FixturesTrait;

    /**
     * Create a valid entity for tests
     *
     * @return Product
     */
    public function getEntity(): Product
    {
        $fixtures = $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/Configurations.yaml',
            dirname(__DIR__).'/fixtures/Products.yaml',
            dirname(__DIR__).'/fixtures/Images.yaml',
        ]);
        $product = new Product();
        $product->setName('product')
            ->setDescription('product description')
            ->setManufacturer('manufacturer')
            ->setCreatedAt(new \DateTime())
            ->setScreen(0.2)
            ->setDas(306.734)
            ->setweight(205.243)
            ->setlength(100.245)
            ->setWidth(55.364)
            ->setHeight(100.423)
            ->setWifi(true)
            ->setVideo4k(true)
            ->setBluetooth(false)
            ->setCamera(true);

        return $product;
    }

    /**
     * Assert valid entity is valid
     *
     * @return void
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * Assert invalid entity (email, firstName, lastName) in invalid
     *
     * @return void
     */
    public function testInvalidEntity()
    {
        $invalidProduct = $this->getEntity();
        $invalidProduct->setName('')
            ->setDescription('desc')
            ->setManufacturer(4)
            ->setScreen('zer')
            ->setDas(10.25)
            ->setweight('')
            ->setlength('')
            ->setWidth(true)
            ->setHeight('ert')
            ->setWifi('azert')
            ->setVideo4k(12)
            ->setBluetooth(1)
            ->setCamera(1);
        $this->assertHasErrors($invalidProduct, 14);
    }

    /**
     * Assert product unicity with email
     *
     * @return void
     */
    public function testInvalidUniqueName()
    {
        $invalidProduct = $this->getEntity()->setName('product1');
        $this->assertHasErrors($invalidProduct, 1);
    }
}