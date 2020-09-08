<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Feature;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FeatureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $features = [
            'Double-SIM',
            'Bluetooth',
            '4G',
            'USB Type C',
            'Appareil photo',
            'Video 4K',
            'WIFI',
            'NFC',
            'Jack',
            'Compatibilité GPS',
            'Extension mémoire'
        ];

        for ($i = 0; $i < count($features); $i++) {
            $feature = new Feature();
            $feature->setName($features[$i]);
            $manager->persist($feature);
            $this->addReference('feature'.$i, $feature);
        }
        $manager->flush();
    }
}
