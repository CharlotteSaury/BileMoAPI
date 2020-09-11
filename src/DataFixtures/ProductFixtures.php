<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Configuration;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface 
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $date = $faker->DateTime();
        $memory = [
            '32 Go',
            '64 Go',
            '128 Go',
            '256 Go',
        ];

        $features = [
            'Wifi',
            'Video4K',
            'Bluetooth',
            'Lte4G',
            'Camera',
            'Nfc'
        ];

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->word)
                ->setDescription($faker->paragraph)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setLength($faker->randomFloat(2,12,15))
                ->setWidth($faker->randomFloat(2,6,10))
                ->setHeight($faker->randomFloat(2,0.7,1.5))
                ->setScreen($faker->randomFloat(1,4,7))
                ->setDas($faker->randomFloat(3, 0.1, 1))
                ->setWeight($faker->randomFloat(1, 150, 250))
                ->setManufacturer($this->getReference('manufacturer'.mt_rand(0,14)));
            
            foreach ($features as $feature) {
                $setter = 'set'.$feature;
                $product->$setter((bool)random_int(0,1));
            }

            for ($k = 0; $k < mt_rand(1,3); $k++) {
                $config = new Configuration();
                $config->setMemory($memory[mt_rand(0, 3)])
                    ->setColor($faker->safeColorName)
                    ->setPrice($faker->randomFloat(2, 800, 1500));
    
                    for ($j = 0; $j < mt_rand(0,4); $j++) {
                        $image = new Image();
                        $image->setUrl($faker->url);
                        $config->addImage($image);
                    }
                $product->addConfiguration($config);
            }
            
            $manager->persist($product);
            $this->addReference('product'.$i, $product);
            $manager->flush();
        }
        
    }

    public function getDependencies() 
    {
        return [
            ManufacturerFixtures::class
        ];
    }
}
