<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Projet;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('budgetinitial', NumberType::class, [
                'attr' => ['class' => 'form-control', 'step' => '0.01', 'min' => '0'],  // 'step' pour la précision, 'min' pour empêcher les valeurs négatives
            ])
            ->add('seuilalerte', NumberType::class, [
                'attr' => ['class' => 'form-control', 'step' => '0.01', 'min' => '0'],
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'nom', // Affiche le nom du client dans le select
            ])
            ->add('referent', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom', // Affiche le nom du référent dans le select
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
