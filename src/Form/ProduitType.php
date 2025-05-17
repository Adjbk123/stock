<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProduit', TextType::class, [
                'label' => 'Nom du produit',
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
            ])
            ->add('prix', MoneyType::class,[
                'currency' => 'XOF',

                'label'=>'Prix (FCFA)',
                'attr'=>['class'=>'form-control mb-3',
                    ]
            ])
//            ->add('dateAjout', DateTimeType::class,[
//                'label'=>'Date'
//            ])

            ->add('categorie', EntityType::class, [
                'class'=>'App\Entity\Categorie',
                'label'=>'Catégorie',
                'attr'=>['class'=>'form-control mb-3',]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
