<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setEmail($faker->companyEmail)
                ->setCreatedAt($faker->dateTime())
                ->setCompany($faker->company)
                ->setPassword($faker->password)
                ->setRoles(['ROLE_USER']);

            $manager->persist($client);
            $this->addReference('client'.$i, $client);
        }

        $manager->flush();
    }
}
