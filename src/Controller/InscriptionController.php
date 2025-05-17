<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateur  = new Utilisateur();
        $form =$this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            $data = $form->getData();
            $password = $passwordHasher->hashPassword($data, $data->getPassword());
            $utilisateur->setPassword($password);	
    
            $manager->persist($utilisateur);
            $manager->flush();
            return $this->redirectToRoute('app_inscription');
        }
        return $this->render('inscription/index.html.twig', [
               'form'=> $form
        ]);
    }
}
