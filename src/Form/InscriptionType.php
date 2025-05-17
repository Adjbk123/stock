<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add('nom', TextType::class,[
                'label' => 'Nom', 
                'attr'=> [
                    'class' =>'form-control mb-3'
                ]
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prénom', 
                'attr'=> [
                    'class' =>'form-control mb-3'
                ]
            ])
            ->add('adresse', TextType::class,[
                'label' => 'Adresse', 
                'attr'=> [
                    'class' =>'form-control mb-3'
                ]
            ])
            ->add('username', TextType::class,[
                'label' => 'Nom d\'utilisateur', 
                'attr'=> [
                    'class' =>'form-control mb-3'
                ]
            ])
            ->add('password', PasswordType::class,[
                'label' => 'Mot de passe', 
                'attr'=> [
                    'class' =>'form-control mb-3'
                ]
            ])
            ->add('submit', SubmitType::class
            ,[
                'label' => 'Ajouter', 
                'attr'=> [
                    'class' =>'btn btn-primary'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
