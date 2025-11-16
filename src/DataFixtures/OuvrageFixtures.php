<?php

namespace App\DataFixtures;

use App\Entity\Ouvrage;
use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class OuvrageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // On récupère les nombres de références disponibles
        $nbAuteurs = \App\DataFixtures\AuteurFixtures::NBAUTEURS;
        $nbCategories = \App\DataFixtures\CategorieFixtures::NBCATEGORIES;
        $nbTags = \App\DataFixtures\TagsFixtures::NBTAGS;

        for ($i = 0; $i < 500; $i++) {
            $ouvrage = new Ouvrage();
            $ouvrage->setTitre($faker->sentence(3));
            $ouvrage->setEditeur($faker->company());
            $ouvrage->setIsbn($faker->isbn13());
            $ouvrage->setParution(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-100 years', 'now')));
            $ouvrage->setResume($faker->paragraph(4));

            $auteurCount = $faker->numberBetween(1, 3);
            $auteurIndexes = $faker->randomElements(range(0, $nbAuteurs - 1), $auteurCount);
            foreach ($auteurIndexes as $index) {
                $ouvrage->addAuteur($this->getReference('auteur_' . $index, Auteur::class));
            }

            $categorieCount = $faker->numberBetween(1, 2);
            $categorieIndexes = $faker->randomElements(range(0, $nbCategories - 1), $categorieCount);
            foreach ($categorieIndexes as $index) {
                $ouvrage->addCategory($this->getReference('categorie_' . $index, Categorie::class));
            }

            $tagCount = $faker->numberBetween(1, 3);
            $tagIndexes = $faker->randomElements(range(0, $nbTags - 1), $tagCount);
            foreach ($tagIndexes as $index) {
                $ouvrage->addTag($this->getReference('tag_' . $index, Tags::class));
            }

            $manager->persist($ouvrage);

            $this->addReference("ouvrage_" . $i, $ouvrage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuteurFixtures::class,
            CategorieFixtures::class,
            TagsFixtures::class,
        ];
    }
}