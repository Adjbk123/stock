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

        // Données pour les graphiques — un point par jour lun→dim
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $statsParJour = $this->venteRepository->findStatsParJourSemaine($weekStartDate, $weekEndDate);

        $chartNbVentes  = array_fill(0, 7, 0);
        $chartMontants  = array_fill(0, 7, 0);
        foreach ($statsParJour as $stat) {
            $date = $stat['jour'] instanceof \DateTimeInterface ? $stat['jour'] : new \DateTimeImmutable($stat['jour']);
            $index = ((int)$date->format('N')) - 1; // 0=lun … 6=dim
            $chartNbVentes[$index]  = (int)$stat['nbVentes'];
            $chartMontants[$index]  = (float)$stat['montant'];
        }

        return $this->render('admin/index.html.twig', [
            'montantJour'    => $montantTotalVentes,
            'montantCaisse'  => $montantCaisse,
            'totalVentes'    => $totalVentes,
            'ventesSemaines' => $ventesSemaine,
            'chartJours'     => json_encode($jours),
            'chartNbVentes'  => json_encode($chartNbVentes),
            'chartMontants'  => json_encode($chartMontants),
        ]);
    }
}
