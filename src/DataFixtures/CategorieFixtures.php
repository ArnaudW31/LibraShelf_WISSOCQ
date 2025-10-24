<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{

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
            $categorie->setDureeEmprunt($faker->int);
            $manager->persist($categorie);

            $this->addReference('categorie_' . $i, $categorie);
        }

        $manager->flush();
    }
}