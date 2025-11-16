<?php

namespace App\DataFixtures;

use App\Entity\Exemplaire;
use App\Enum\Etat;
use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ExemplaireFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {

            $ex = new Exemplaire();

            $ex->setCote($faker->bothify('###-???'));

            // Enum Etat
            $etat = $faker->randomElement(Etat::cases());
            $ex->setEtat($etat);

            // Disponibilité : true ou false
            $ex->setDisponibilite(true); 

            // Association correcte avec un ouvrage déjà créé
            $ouvrageIndex = $faker->numberBetween(0, 500 - 1);
            $ouvrage = $this->getReference("ouvrage_" . $ouvrageIndex, Ouvrage::class);
            $ex->setOuvrage($ouvrage);

            $manager->persist($ex);

            // Si tu veux utiliser l'exemplaire ailleurs
            $this->addReference("exemplaire_" . $i, $ex);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OuvrageFixtures::class
        ];
    }
}
