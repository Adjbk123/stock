<?php

namespace App\Controller;

use App\Entity\Approvisionnement;
use App\Form\ApprovisionnementType;
use App\Repository\ApprovisionnementRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/approvisionnement')]
class ApprovisionnementController extends AbstractController
{
    #[Route('/', name: 'app_approvisionnement_index', methods: ['GET'])]
    public function index(ApprovisionnementRepository $approvisionnementRepository): Response
    {
        return $this->render('approvisionnement/index.html.twig', [
            'approvisionnements' => $approvisionnementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_approvisionnement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ApprovisionnementRepository $approvisionnementRepository,
        ProduitRepository $produitRepository): Response
    {
        $approvisionnement = new Approvisionnement();
        $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quantiteApprovisionnement = $approvisionnement->getQuantite();
            $produit = $approvisionnement->getProduit();

            // Mettre à jour la quantité du produit
            $quantiteProduit = $produit->getQuantiteStock();
            $nouvelleQuantiteProduit = $quantiteProduit + $quantiteApprovisionnement;
            $produit->setQuantiteStock($nouvelleQuantiteProduit);

            // Récupérer le prix du produit
            $prixProduit = $produit->getPrix();

            // Définir le coût unitaire au niveau de l'approvisionnement
            $coutUnitaire = $prixProduit * $quantiteApprovisionnement;
            $approvisionnement->setCoutUnit($coutUnitaire);

            // Enregistrer l'approvisionnement et mettre à jour le produit
            $approvisionnementRepository->save($approvisionnement, true);
            $produitRepository->save($produit, true);





            return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('approvisionnement/new.html.twig', [
            'approvisionnement' => $approvisionnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_approvisionnement_show', methods: ['GET'])]
    public function show(Approvisionnement $approvisionnement): Response
    {
        return $this->render('approvisionnement/show.html.twig', [
            'approvisionnement' => $approvisionnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_approvisionnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Approvisionnement $approvisionnement, ApprovisionnementRepository $approvisionnementRepository): Response
    {
        $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $approvisionnementRepository->save($approvisionnement, true);

            return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('approvisionnement/edit.html.twig', [
            'approvisionnement' => $approvisionnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_approvisionnement_delete', methods: ['POST'])]
    public function delete(Request $request, Approvisionnement $approvisionnement, ApprovisionnementRepository $approvisionnementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$approvisionnement->getId(), $request->request->get('_token'))) {
            $approvisionnementRepository->remove($approvisionnement, true);
        }

        return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
