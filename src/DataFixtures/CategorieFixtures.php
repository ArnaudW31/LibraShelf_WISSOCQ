<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategorieFixtures extends Fixture
{
    public const NBCATEGORIES = 8;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $CATEGORIES = [
            'Roman',
            'Science-Fiction',
            'Livre de poche',
            'Histoire',
            'Biographie',
            'Fantastique',
            'Poésie',
            'Philosophie'
        ];

        foreach ($CATEGORIES as $i => $nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom);
            $categorie->setDureeEmprunt($faker->numberBetween(1, 30));
            $manager->persist($categorie);

            $this->addReference('categorie_' . $i, $categorie);
        }

        $manager->flush();
    }
}