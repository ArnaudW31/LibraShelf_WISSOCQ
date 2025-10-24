<?php

namespace App\DataFixtures;

use App\Entity\Ouvrage;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OuvrageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 500; $i++) {
            $ouvrage = new Ouvrage();

            $ouvrage->setTitre('product '.$i);
            $ouvrage->setResume('gagak');

            $manager->persist($ouvrage);
        }

        $manager->flush();
    }
}