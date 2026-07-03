<?php

require_once __DIR__ . '/../Services/AbonnementService.php';
require_once __DIR__ . '/../Repositories/AdherentRepository.php';
require_once __DIR__ . '/../Entities/Abonnement.php';

/**
 * AbonnementController
 * Orchestre le service et les repositories pour les fonctionnalités liées aux abonnements.
 */
class AbonnementController
{
    private AbonnementService $abonnementService;
    private AdherentRepository $adherentRepository;

    public function __construct(AbonnementService $abonnementService, AdherentRepository $adherentRepository)
    {
        $this->abonnementService  = $abonnementService;
        $this->adherentRepository = $adherentRepository;
    }

    /**
     * Affiche la liste des abonnements (views/abonnements/index.php)
     */
    public function index(): void
    {
        $abonnements = $this->abonnementService->getAllAbonnementsWithAdherent();

        require __DIR__ . '/../../views/abonnements/index.php';
    }

    /**
     * Affiche le formulaire de création + traite la soumission (views/abonnements/create.php)
     */
    public function create(): void
    {
        $adherents = $this->adherentRepository->findAll();
        $typesDisponibles = [
            Abonnement::TYPE_MENSUEL,
            Abonnement::TYPE_TRIMESTRIEL,
            Abonnement::TYPE_ANNUEL,
        ];
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->abonnementService->creerAbonnement(
                    (int) ($_POST['id_adherent'] ?? 0),
                    $_POST['type_abonnement'] ?? '',
                    $_POST['date_debut'] ?? date('Y-m-d')
                );

                header('Location: index.php?page=abonnements&success=1');
                exit;
            } catch (InvalidArgumentException | RuntimeException $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/abonnements/create.php';
    }

    /**
     * Résilie un abonnement
     */
    public function resilier(int $id): void
    {
        try {
            $this->abonnementService->resilierAbonnement($id);
            header('Location: index.php?page=abonnements&resilie=1');
        } catch (InvalidArgumentException $e) {
            header('Location: index.php?page=abonnements&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    /**
     * Supprime définitivement un abonnement.
     */
    public function supprimer(int $id): void
    {
        try {
            $this->abonnementService->supprimerAbonnement($id);
            header('Location: index.php?page=abonnements&supprime=1');
        } catch (InvalidArgumentException $e) {
            header('Location: index.php?page=abonnements&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    /**
     * Affiche l'historique des abonnements d'un adhérent donné
     */
    public function historique(int $idAdherent): void
    {
        $historique = $this->abonnementService->getHistoriqueAdherent($idAdherent);
        $adherent = $this->adherentRepository->findById($idAdherent);

        require __DIR__ . '/../../views/abonnements/historique.php';
    }
}
