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

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->word)
                ->setDescription($faker->paragraph)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setDimensions('L'.$faker->randomFloat(2,12,15).' cm x l'.$faker->randomFloat(2,6,10).' cm x H'.$faker->randomFloat(2,0.7,1.5).'cm')
                ->setScreen($faker->randomFloat(1,4,7).'"')
                ->setDas($faker->randomFloat(3, 0.1, 1))
                ->setWeight($faker->randomFloat(1, 150, 250))
                ->setManufacturer($this->getReference('manufacturer'.mt_rand(0,14)));
            for ($j = 0; $j < mt_rand(0,5); $j++) {
                $product->addFeature($this->getReference('feature'.mt_rand(0, 10)));
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
            ManufacturerFixtures::class,
            FeatureFixtures::class,
        ];
    }
}
