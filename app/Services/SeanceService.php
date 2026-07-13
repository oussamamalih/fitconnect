<?php

require_once __DIR__ . '/../Repositories/SeanceRepository.php';
require_once __DIR__ . '/../Entities/Seance.php';
require_once __DIR__ . '/AbonnementService.php';

/**
 * SeanceService
 * Applique les règles de gestion liées aux séances.
 * Règle centrale : "une séance ne peut être enregistrée que si
 * l'abonnement de l'adhérent est valide à la date du jour."
 */
class SeanceService
{
    private SeanceRepository $seanceRepository;
    private AbonnementService $abonnementService;

    public function __construct(SeanceRepository $seanceRepository, AbonnementService $abonnementService)
    {
        $this->seanceRepository  = $seanceRepository;
        $this->abonnementService = $abonnementService;
    }

    public function getAllSeances(): array
    {
        return $this->seanceRepository->findAll();
    }

    public function getAllSeancesWithDetails(): array
    {
        return $this->seanceRepository->findAllWithDetails();
    }

    public function getSeanceById(int $id): ?Seance
    {
        return $this->seanceRepository->findById($id);
    }

    /**
     * Enregistre une nouvelle séance, après vérification de la règle de gestion :
     * l'abonnement de l'adhérent doit être valide à la date du jour.
     *
     * @throws RuntimeException si l'abonnement n'est pas valide
     * @throws InvalidArgumentException si les données sont invalides
     */
    public function enregistrerSeance(
        string $dateSeance,
        string $typeActivite,
        int $duree,
        ?string $equipementUtilise,
        int $idAdherent,
        int $idSalle
    ): int {
        $this->validateSeanceData($typeActivite, $duree, $idAdherent, $idSalle);

        // Règle de gestion centrale : abonnement valide obligatoire
        if (!$this->abonnementService->estAbonnementValide($idAdherent)) {
            throw new RuntimeException(
                "Impossible d'enregistrer la séance : l'abonnement de cet adhérent "
                . "n'est pas valide (inexistant, expiré ou résilié)."
            );
        }

        $seance = new Seance(null, $dateSeance, $typeActivite, $duree, $equipementUtilise, $idAdherent, $idSalle);

        return $this->seanceRepository->create($seance);
    }

    public function deleteSeance(int $idSeance): bool
    {
        return $this->seanceRepository->delete($idSeance);
    }

    private function validateSeanceData(string $typeActivite, int $duree, int $idAdherent, int $idSalle): void
    {
        if (trim($typeActivite) === '') {
            throw new InvalidArgumentException("Le type d'activité est obligatoire.");
        }

        if ($duree <= 0) {
            throw new InvalidArgumentException("La durée doit être supérieure à 0 minute.");
        }

        if ($idAdherent <= 0 || $idSalle <= 0) {
            throw new InvalidArgumentException("L'adhérent et la salle sont obligatoires.");
        }
    }
}
