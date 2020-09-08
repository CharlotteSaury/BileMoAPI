<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Customer;
use App\DataFixtures\ClientFixtures;
use App\Repository\ClientRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $customer = new Customer();
            $customer->setEmail($faker->email)
                ->setCreatedAt($faker->dateTime())
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
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
