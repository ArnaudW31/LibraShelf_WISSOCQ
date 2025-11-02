<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public const NBAUTEURS = 50;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::NBAUTEURS; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->lastName());
            $auteur->setPrenom($faker->firstName());
            $dateNaissance = $faker->dateTimeBetween('1940-01-01', '2000-12-31');
            $auteur->setDateNaissance(\DateTimeImmutable::createFromMutable($dateNaissance));
            $manager->persist($auteur);

            // Pour pouvoir y accéder depuis OuvrageFixtures
            $this->addReference('auteur_' . $i, $auteur);
        }

        $manager->flush();
    }
}