<?php

namespace App\Controller;



use App\Repository\VenteRepository;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    public function __construct(VenteRepository $venteRepository)
    {
        $this->venteRepository = $venteRepository;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        //jour
        $today = new DateTimeImmutable('today');

        $ventesDuJour = $this->venteRepository->findBy([
            'dateVente' => $today
        ]);

        //compte
        $totalVentes = $this->venteRepository->count([
            'dateVente' => $today
        ]);


        // Calcul du montant total des ventes
        $montantTotalVentes = 0;
        foreach ($ventesDuJour as $vente) {
            $montantTotalVentes += $vente->getMontantTotal();
        }

        $venteCaisse = $this->venteRepository->findAll();
        $montantCaisse= 0;

        foreach ($venteCaisse as $vente) {
            $montantCaisse += $vente->getMontantTotal();
        }

        // listes des ventes de la semaine

        $cejour = new DateTimeImmutable();
        $weekStartDate = $today->modify('this week')->modify('next Monday');
        $weekEndDate = $weekStartDate->modify('+5 days');

        $ventesSemaine = $this->venteRepository->findBy([
            'dateVente' => [
                '>=', $weekStartDate->format('Y-m-d H:i:s'),
                '<=', $weekEndDate->format('Y-m-d H:i:s')
            ]
        ]);



        return $this->render('admin/index.html.twig', [
            'montantJour' => $montantTotalVentes,
            'montantCaisse'=>$montantCaisse,
            'totalVentes' => $totalVentes,
            'ventesSemaines'=>$ventesSemaine
        ]);
    }
}
