<?php

require_once __DIR__ . '/../Services/AdherentService.php';
require_once __DIR__ . '/../Repositories/SalleRepository.php';

/**
 * AdherentController
 * Orchestre le service et les repositories pour les fonctionnalités liées aux adhérents.
 */
class AdherentController
{
    private AdherentService $adherentService;
    private SalleRepository $salleRepository;

    public function __construct(AdherentService $adherentService, SalleRepository $salleRepository)
    {
        $this->adherentService = $adherentService;
        $this->salleRepository = $salleRepository;
    }

    /**
     * Affiche la liste des adhérents (views/adherents/index.php)
     */
    public function index(): void
    {
        $adherents = $this->adherentService->getAllAdherentsWithSalle();

        require __DIR__ . '/../../views/adherents/index.php';
    }

    /**
     * Affiche le formulaire de création + traite la soumission (views/adherents/create.php)
     */
    public function create(): void
    {
        $salles = $this->salleRepository->findAll();
        $erreur = null;
        $succes = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->adherentService->createAdherent(
                    trim($_POST['nom'] ?? ''),
                    trim($_POST['prenom'] ?? ''),
                    trim($_POST['email'] ?? ''),
                    trim($_POST['telephone'] ?? '') ?: null,
                    $_POST['date_inscription'] ?? date('Y-m-d'),
                    (int) ($_POST['id_salle'] ?? 0)
                );

                header('Location: index.php?page=adherents&success=1');
                exit;
            } catch (InvalidArgumentException $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/adherents/create.php';
    }

    /**
     * Affiche le formulaire d'édition + traite la soumission (views/adherents/edit.php)
     */
    public function edit(int $id): void
    {
        $adherent = $this->adherentService->getAdherentById($id);
        if ($adherent === null) {
            echo "Adhérent introuvable.";
            return;
        }

        $salles = $this->salleRepository->findAll();
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->adherentService->updateAdherent(
                    $id,
                    trim($_POST['nom'] ?? ''),
                    trim($_POST['prenom'] ?? ''),
                    trim($_POST['email'] ?? ''),
                    trim($_POST['telephone'] ?? '') ?: null,
                    $_POST['date_inscription'] ?? date('Y-m-d'),
                    (int) ($_POST['id_salle'] ?? 0)
                );

                header('Location: index.php?page=adherents&success=1');
                exit;
            } catch (InvalidArgumentException $e) {
                $erreur = $e->getMessage();
            }
        }

        require __DIR__ . '/../../views/adherents/edit.php';
    }

    /**
     * Supprime un adhérent (en respectant les règles de gestion via le Service)
     */
    public function delete(int $id): void
    {
        try {
            $this->adherentService->deleteAdherent($id);
            header('Location: index.php?page=adherents&deleted=1');
        } catch (RuntimeException $e) {
            header('Location: index.php?page=adherents&error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}
