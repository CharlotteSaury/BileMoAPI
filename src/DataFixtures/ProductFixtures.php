<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface 
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $date = $faker->DateTime();

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
