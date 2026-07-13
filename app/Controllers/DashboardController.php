<?php

require_once __DIR__ . '/../Repositories/AdherentRepository.php';
require_once __DIR__ . '/../Repositories/AbonnementRepository.php';
require_once __DIR__ . '/../Repositories/SeanceRepository.php';
require_once __DIR__ . '/../Repositories/SalleRepository.php';
require_once __DIR__ . '/../Entities/Abonnement.php';

/**
 * DashboardController
 * Fournit une vue d'ensemble du réseau FitConnect (les 4 salles)
 * - nombre total d'adhérents
 * - nombre d'abonnements actifs / expirés
 * - répartition des séances par salle
 */
class DashboardController
{
    private AdherentRepository $adherentRepository;
    private AbonnementRepository $abonnementRepository;
    private SeanceRepository $seanceRepository;
    private SalleRepository $salleRepository;

    public function __construct(
        AdherentRepository $adherentRepository,
        AbonnementRepository $abonnementRepository,
        SeanceRepository $seanceRepository,
        SalleRepository $salleRepository
    ) {
        $this->adherentRepository   = $adherentRepository;
        $this->abonnementRepository = $abonnementRepository;
        $this->seanceRepository     = $seanceRepository;
        $this->salleRepository      = $salleRepository;
    }

    public function index(): void
    {
        $totalAdherents = count($this->adherentRepository->findAll());
        $totalSalles    = count($this->salleRepository->findAll());
        $totalSeances   = $this->seanceRepository->countAll();

        // On compte les abonnements actifs et expirés en parcourant la liste
        $abonnements  = $this->abonnementRepository->findAll();
        $totalActifs  = 0;
        $totalExpires = 0;
        foreach ($abonnements as $abonnement) {
            if ($abonnement->getStatut() === Abonnement::STATUT_ACTIF) {
                $totalActifs++;
            } elseif ($abonnement->getStatut() === Abonnement::STATUT_EXPIRE) {
                $totalExpires++;
            }
        }

        $repartitionParSalle = $this->seanceRepository->countBySalle();

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
