<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; ++$i) {
            $customer = new Customer();
            $customer->setEmail($faker->email)
                ->setCreatedAt($faker->dateTime())
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName);
            for ($j = 1; $j < mt_rand(1, 3); ++$j) {
                $customer->addClient($this->getReference('client'.mt_rand(0, 19)));
            }

            $manager->persist($customer);
        }
        for ($i = 0; $i < 5; ++$i) {
            $customer = new Customer();
            $customer->setEmail($faker->email)
                ->setCreatedAt($faker->dateTime())
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName);
            for ($j = 1; $j < mt_rand(1, 3); ++$j) {
                $customer->addClient($this->getReference('user'));
            }

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
