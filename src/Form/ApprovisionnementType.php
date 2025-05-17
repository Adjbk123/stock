<?php

namespace App\Form;

use App\Entity\Approvisionnement;
use Monolog\DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApprovisionnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateAppro', DateTimeType::class, [
                    'data'=> new DateTimeImmutable('now'),
                    'widget'=>'single_text',
                    'input'=>'datetime_immutable',
                    'label'=>'Date Appro',
                    'attr'=>['class'=>'form-control mb-3']
                ]
            )
            ->add('produit', EntityType::class, [
                'class'=>'App\Entity\Produit',
                'label'=>'Produit',
                'attr'=>['class'=>'form-control mb-3']
            ])
            ->add('quantite',NumberType::class,[
                'label'=>'Quantité',
                'attr'=>['class'=>'form-control mb-3', 'min'=>'0', ]
            ]
             )


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Approvisionnement::class,
        ]);
    }
}
