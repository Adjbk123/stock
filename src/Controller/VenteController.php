<?php

namespace App\Controller;


use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Vente;
use App\Entity\VenteProduit;
use App\Form\VenteType;
use App\Repository\ProduitRepository;
use App\Repository\VenteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vente')]
class VenteController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_vente_index')]
    public function index( VenteRepository $venteRepository): Response

    {
        $ventes =  $venteRepository->findAll();

        return $this->render('vente/index.html.twig', [
            'ventes' =>  $ventes,
        ]);
    }

    //
    #[Route('/nouveau', name: 'app_vente_new')]
    public function indexForm( ProduitRepository $produitRepository): Response

    {
        $produits =  $produitRepository->findAll();

        return $this->render('vente/new.html.twig', [
            'produits' =>  $produits,
        ]);
    }

    #[Route('/enregistrement', name: 'app_vente_processvente')]
    public function processVente(Request $request, EntityManagerInterface $manager)
    {

        $venteValider = $request->get('venteValider');

        $data = json_decode($venteValider, true);
        //tableau de vente

        // Récupérer l'utilisateur connecté
        $user =$this->getUser();


        // Récupérer les données du formulaire
        $nomsclient = $request->request->get('client');
        $numero = $request->request->get('numero');


        // Créer une nouvelle vente
        $client = new Client();
        $client->setNomsClient($nomsclient);
        $client->setTelephone($numero);
        $manager->persist($client);

        $montantTotal= 0;

        foreach ($data as $mesVentes) {
            $montantTotal += $mesVentes['coutProduit'];
        }
        $heureVente = new DateTime('now', new \DateTimeZone('Africa/Porto-Novo'));

        $vente= new Vente();
        $vente->setClient($client);
        $vente->setUtilisateur($user);
        $vente->setMontantTotal($montantTotal);
        $vente->setDateVente(new \DateTime());
        $vente->setHeureVente($heureVente);
        $manager->persist($vente);

        foreach ($data as $venteP) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($venteP['id']);

            // Déduire la quantité vendue du stock du produit
            $nouveauStock = $produit->getQuantiteStock() - $venteP['quantite'];
            $produit->setQuantiteStock($nouveauStock);

            // Persiste l'entité Produit pour enregistrer les changements
            $manager->persist($produit);

            // Vérifie si l'objet Produit a été chargé avec succès
            if ($produit instanceof Produit) {
                $venteProduit = new VenteProduit();
                $venteProduit->setVente($vente);
                $venteProduit->setProduit($produit);
                $venteProduit->setQuantite($venteP['quantite']);
                $venteProduit->setPrixUnitaire($venteP['prix']);

                $manager->persist($venteProduit);
            }
        }


       $manager->flush();
        $this->addFlash('success', "L'enregistrement de la vente s'est effectué avec succès");

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'])]
    public function show(Vente $vente): Response
    {
        return $this->render('vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vente $vente, VenteRepository $venteRepository): Response
    {
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $venteRepository->save($vente, true);

            return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vente/edit.html.twig', [
            'vente' => $vente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vente_delete', methods: ['POST'])]
    public function delete(Request $request, Vente $vente, VenteRepository $venteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->request->get('_token'))) {
            $venteRepository->remove($vente, true);
        }

        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }
}
