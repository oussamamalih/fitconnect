<?php

require_once __DIR__ . '/../Services/SeanceService.php';
require_once __DIR__ . '/../Repositories/AdherentRepository.php';
require_once __DIR__ . '/../Repositories/SalleRepository.php';

/**
 * SeanceController
 * Orchestre le service et les repositories pour les fonctionnalités liées aux séances.
 */
class SeanceController
{
    private SeanceService $seanceService;
    private AdherentRepository $adherentRepository;
    private SalleRepository $salleRepository;

    public function __construct(
        SeanceService $seanceService,
        AdherentRepository $adherentRepository,
        SalleRepository $salleRepository
    ) {
        $this->seanceService      = $seanceService;
        $this->adherentRepository = $adherentRepository;
        $this->salleRepository    = $salleRepository;
    }

    /**
     * Affiche la liste des séances (views/seances/index.php)
     */
    public function index(): void
    {
        $seances = $this->seanceService->getAllSeancesWithDetails();

        require __DIR__ . '/../../views/seances/index.php';
    }

    /**
     * Affiche le formulaire de création + traite la soumission (views/seances/create.php)
     */
    public function create(): void
    {
        $adherents = $this->adherentRepository->findAll();
        $salles    = $this->salleRepository->findAll();
        $erreur    = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->seanceService->enregistrerSeance(
                    $_POST['date_seance'] ?? date('Y-m-d'),
                    trim($_POST['type_activite'] ?? ''),
                    (int) ($_POST['duree'] ?? 0),
                    trim($_POST['equipement_utilise'] ?? '') ?: null,
                    (int) ($_POST['id_adherent'] ?? 0),
                    (int) ($_POST['id_salle'] ?? 0)
                );

                header('Location: index.php?page=seances&success=1');
                exit;
            } catch (InvalidArgumentException | RuntimeException $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/seances/create.php';
    }

    /**
     * Supprime une séance
     */
    public function delete(int $id): void
    {
        $this->seanceService->deleteSeance($id);
        header('Location: index.php?page=seances&deleted=1');
        exit;
    }
}
