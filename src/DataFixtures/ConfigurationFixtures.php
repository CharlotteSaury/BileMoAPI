<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Configuration;
use App\DataFixtures\PhoneFixtures;
use App\Repository\PhoneRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ConfigurationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $memory = [
            '32 Go',
            '64 Go',
            '128 Go',
            '256 Go',
        ];

        for ($i = 0; $i < 100; $i++) {
            $config = new Configuration();
            $config->setMemory($memory[mt_rand(0, 3)])
                ->setColor($faker->safeColorName)
                ->setPrice($faker->randomFloat(2, 800, 1500))
                ->setPhone($this->getReference('phone' . mt_rand(0, 49)));

                for ($j = 0; $j < mt_rand(0,4); $j++) {
                    $image = new Image();
                    $image->setUrl($faker->url);
                    $config->addImage($image);
                }

            $manager->persist($config);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PhoneFixtures::class,
        ];
    }
}
