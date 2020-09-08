<?php

namespace App\DataFixtures;

use App\Entity\Manufacturer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ManufacturerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $companies = [
            'Huawei',
            'Wiko',
            'Alcatel',
            'Apple',
            'Samsung',
            'Nokia',
            'Motorola',
            'Sony',
            'BlackBerry',
            'Google',
            'LG',
            'HTC',
            'Honor',
            'Xiaomi',
            'Asus',
        ];

        for ($i = 0; $i < count($companies); $i++) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($companies[$i]);

            $manager->persist($manufacturer);
            $this->addReference('manufacturer' . $i, $manufacturer);
        }
        $manager->flush();
    }
}
