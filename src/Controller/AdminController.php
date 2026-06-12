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

        $weekStartDate = $today->modify('Monday this week');
        $weekEndDate = $today->modify('Sunday this week');

        $ventesSemaine = $this->venteRepository->findVentesSemaine($weekStartDate, $weekEndDate);



        return $this->render('admin/index.html.twig', [
            'montantJour' => $montantTotalVentes,
            'montantCaisse'=>$montantCaisse,
            'totalVentes' => $totalVentes,
            'ventesSemaines'=>$ventesSemaine
        ]);
    }
}
