<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\SessionFormation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('session', EntityType::class, [
                'class' => SessionFormation::class,
                'choice_label' => function(SessionFormation $session) {
                    return $session->getProjet()->getNom() . ' - ' . $session->getDatedebut()->format('d/m/Y');
                },
                'label' => 'Session',
                'placeholder' => 'Choisir une session',
                'required' => true,
            ])
            ->add('organisme', TextType::class, [
                'label' => 'Organisme de formation',
                'required' => true,
            ])
            ->add('couht', MoneyType::class, [
                'label' => 'Coût HT',
                'required' => true,
                'currency' => 'EUR',
            ])
            ->add('tauxtva', NumberType::class, [
                'label' => 'Taux TVA (%)',
                'required' => true,
                'scale' => 2,
            ])
            ->add('dateformation', DateType::class, [
                'label' => 'Date de formation',
                'widget' => 'single_text',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
} 