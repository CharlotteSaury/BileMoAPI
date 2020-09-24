<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientFixtures extends Fixture
{    
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setEmail($faker->companyEmail)
                ->setCreatedAt($faker->dateTime())
                ->setCompany($faker->company)
                ->setPassword($this->encoder->encodePassword($client, 'password'))
                ->setRoles(['ROLE_USER']);

            $manager->persist($client);
            $this->addReference('client'.$i, $client);
        }

        $user = new Client();
        $user->setEmail('user@bilemo.com')
                ->setCreatedAt(new \DateTime())
                ->setCompany('BileMo')
                ->setPassword($this->encoder->encodePassword($user, 'user'))
                ->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $this->addReference('user', $user);

        $admin = new Client();
        $admin->setEmail('admin@bilemo.com')
                ->setCreatedAt(new \DateTime())
                ->setCompany('BileMo')
                ->setPassword($this->encoder->encodePassword($client, 'admin'))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $manager->flush();
    }
}
