<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Customer;
use App\DataFixtures\ClientFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $customer = new Customer();
            $customer->setEmail($faker->email)
                ->setCreatedAt($faker->dateTime())
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setClient($this->getReference('client'.mt_rand(0, 19)));

            $manager->persist($customer);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ClientFixtures::class,
        ];
    }
}
