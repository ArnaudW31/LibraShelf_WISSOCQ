<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TagsFixtures extends Fixture
{
    public const NBTAGS = 7;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $TAGS = [
            '18+',
            'Pour enfant',
            'Court mais bon',
            'Long',
            'Illustré',
            'Best seller',
            'Édition originale',
        ];

        foreach ($TAGS as $i => $nom) {
            $tag = new Tags();
            $tag->setNom($nom);
            $manager->persist($tag);

            $this->addReference('tag_' . $i, $tag);
        }

        $manager->flush();
    }
}
