<?php

namespace App\Form;

use App\Entity\Vente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomsClient', TextType::class, [
                'label' => 'Noms ',
                'mapped'=>false,
                'attr' => ['placeholder' =>'Nom et Prénoms du client',
                    'class' => 'form-control mb-3']

            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone ',
                'mapped'=>false,
                'attr' => [
                    'class' => 'form-control mb-3'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
