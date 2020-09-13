<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'campus',
                EntityType::class,
                [
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                    'required' => false,
                ])
            ->add(
                'nom',
                SearchType::class,
                [
                    'label' => "Le nom de la sortie contient",
                    'required' => false,
                ])
            ->add(
                'date_debut',
                DateType::class,
                [
                    'label' => 'Entre',
                    'required' => false,
                ])
            ->add(
                'date_fin',
                DateType::class,
                [
                    'label' => 'et',
                    'required' => false,
                ])
            ->add(
                'owned',
                CheckboxType::class,
                [
                    'label' => "Sorties dont je suis l'organisateur/trice",
                    'mapped' => false,
                    'required' => false,
                ])
            ->add(
                'subscribed',
                CheckboxType::class,
                [
                    'label' => "Sorties auxquelles je suis inscrit/e",
                    'mapped' => false,
                    'required' => false,
                ])
            ->add(
                'notSubscribed',
                CheckboxType::class,
                [
                    'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                    'mapped' => false,
                    'required' => false,
                ])
            ->add(
                'expired',
                CheckboxType::class,
                [
                    'label' => "Sorties passées",
                    'mapped' => false,
                    'required' => false,
                ])
            ->add('send', SubmitType::class, [
                'label' => 'Rechercher',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
