<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieux;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('date_debut', DateType::class, ['data' => new \DateTime('now')])
            ->add('date_fin', DateType::class, ['data' => new \DateTime('now +1 month')])
            ->add('description', TextType::class)
            ->add('photo', TextType::class, ['required' => false,])
            ->add('maxInscriptions', IntegerType::class)
            ->add(
                'lieux',
                EntityType::class,
                [
                    'class' => Lieux::class,
                    'choice_label' => 'nom',
                ])
            ->add(
                'campus',
                EntityType::class,
                [
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                ])
            ->add('send', SubmitType::class, [
                    'label' => 'Enregistrer',
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
