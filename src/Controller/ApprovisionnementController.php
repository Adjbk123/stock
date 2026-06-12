<?php

namespace App\Controller;

use App\Entity\Approvisionnement;
use App\Form\ApprovisionnementType;
use App\Repository\ApprovisionnementRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/add', name: 'app_approvisionnement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ApprovisionnementRepository $approvisionnementRepository,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $produitsIds = $request->request->all('produits');
            $quantites   = $request->request->all('quantites');
            $dateStr     = $request->request->get('dateAppro');

            $date = $dateStr
                ? new \DateTimeImmutable($dateStr)
                : new \DateTimeImmutable();

            $enregistres = 0;

            foreach ($produitsIds as $i => $produitId) {
                $quantite = isset($quantites[$i]) ? (int)$quantites[$i] : 0;
                if (!$produitId || $quantite <= 0) continue;

                $produit = $produitRepository->find($produitId);
                if (!$produit) continue;

                $appro = new Approvisionnement();
                $appro->setProduit($produit);
                $appro->setQuantite($quantite);
                $appro->setDateAppro($date);
                $appro->setCoutUnit($produit->getPrix() * $quantite);

                $produit->setQuantiteStock($produit->getQuantiteStock() + $quantite);

                $em->persist($appro);
                $enregistres++;
            }

            if ($enregistres > 0) {
                $em->flush();
                $this->addFlash('success', $enregistres . ' approvisionnement(s) enregistré(s) avec succès.');
            } else {
                $this->addFlash('danger', 'Aucune ligne valide à enregistrer.');
            }

            return $this->redirectToRoute('app_approvisionnement_index');
        }

        return $this->render('approvisionnement/new.html.twig', [
            'produits' => $produitRepository->findAll(),
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
