<?php

namespace App\Tests;

use App\Entity\Ouvrage;
use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Tags;
use PHPUnit\Framework\TestCase;

class OuvrageTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $ouvrage = new Ouvrage();
        $ouvrage->setTitre('Symfony 6')
                ->setEditeur('TechBooks')
                ->setIsbn('1234567890123');

        $this->assertSame('Symfony 6', $ouvrage->getTitre());
        $this->assertSame('TechBooks', $ouvrage->getEditeur());
        $this->assertSame('1234567890123', $ouvrage->getIsbn());
    }

    public function testAuteursCategoriesTags(): void
    {
        $ouvrage = new Ouvrage();
        $auteur = new Auteur();
        $categorie = new Categorie();
        $tag = new Tags();

        $ouvrage->addAuteur($auteur);
        $ouvrage->addTag($tag);

        $this->assertCount(1, $ouvrage->getAuteurs());
        $this->assertCount(1, $ouvrage->getTags());

        $ouvrage->removeAuteur($auteur);
        $ouvrage->removeTag($tag);

        $this->assertCount(0, $ouvrage->getAuteurs());
        $this->assertCount(0, $ouvrage->getTags());
    }
}
