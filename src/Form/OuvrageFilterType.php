<?php
namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Tags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class OuvrageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'required' => false,
                'label' => 'Titre contient',
            ])
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les auteurs',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => 'Toutes les catégories',
                'required' => false,
            ])
            ->add('tag', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les tags',
                'required' => false,
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Exemplaire disponible',
                'required' => false,
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => 'Filtrer'
            ]);
    }
}

?>