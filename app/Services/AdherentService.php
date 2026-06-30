<?php

require_once __DIR__ . '/../Repositories/AdherentRepository.php';
require_once __DIR__ . '/../Entities/Adherent.php';

/**
 * AdherentService
 * Applique les règles de gestion liées aux adhérents,
 * indépendamment de la couche de persistance (Repository).
 */
class AdherentService
{
    private AdherentRepository $adherentRepository;

    public function __construct(AdherentRepository $adherentRepository)
    {
        $this->adherentRepository = $adherentRepository;
    }

    public function getAllAdherents(): array
    {
        return $this->adherentRepository->findAll();
    }

    public function getAllAdherentsWithSalle(): array
    {
        return $this->adherentRepository->findAllWithSalle();
    }

    public function getAdherentById(int $id): ?Adherent
    {
        return $this->adherentRepository->findById($id);
    }

    /**
     * Crée un nouvel adhérent après validation des règles de gestion.
     * @throws InvalidArgumentException si une règle métier est violée
     */
    public function createAdherent(
        string $nom,
        string $prenom,
        string $email,
        ?string $telephone,
        string $dateInscription,
        int $idSalle
    ): int {
        $this->validateAdherentData($nom, $prenom, $email, $idSalle);

        // Règle : l'email doit être unique
        if ($this->adherentRepository->findByEmail($email) !== null) {
            throw new InvalidArgumentException("Un adhérent avec cet email existe déjà.");
        }

        $adherent = new Adherent(null, $nom, $prenom, $email, $telephone, $dateInscription, $idSalle);

        return $this->adherentRepository->create($adherent);
    }

    /**
     * Met à jour un adhérent existant.
     * @throws InvalidArgumentException
     */
    public function updateAdherent(
        int $idAdherent,
        string $nom,
        string $prenom,
        string $email,
        ?string $telephone,
        string $dateInscription,
        int $idSalle
    ): bool {
        $this->validateAdherentData($nom, $prenom, $email, $idSalle);

        $existing = $this->adherentRepository->findByEmail($email);
        if ($existing !== null && $existing->getIdAdherent() !== $idAdherent) {
            throw new InvalidArgumentException("Un autre adhérent utilise déjà cet email.");
        }

        $adherent = new Adherent($idAdherent, $nom, $prenom, $email, $telephone, $dateInscription, $idSalle);

        return $this->adherentRepository->update($adherent);
    }

    /**
     * Supprime un adhérent en respectant la règle :
     * "un adhérent ne peut pas être supprimé s'il possède des séances
     * enregistrées ou un abonnement en cours".
     * @throws RuntimeException si la suppression est interdite
     */
    public function deleteAdherent(int $idAdherent): bool
    {
        if ($this->adherentRepository->hasSeances($idAdherent)) {
            throw new RuntimeException("Impossible de supprimer : cet adhérent possède des séances enregistrées.");
        }

        if ($this->adherentRepository->hasAbonnement($idAdherent)) {
            throw new RuntimeException("Impossible de supprimer : cet adhérent possède un abonnement en cours.");
        }

        return $this->adherentRepository->delete($idAdherent);
    }

    /**
     * Validation centralisée des données d'un adhérent.
     * @throws InvalidArgumentException
     */
    private function validateAdherentData(string $nom, string $prenom, string $email, int $idSalle): void
    {
        if (trim($nom) === '' || trim($prenom) === '') {
            throw new InvalidArgumentException("Le nom et le prénom sont obligatoires.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'adresse email n'est pas valide.");
        }

        if ($idSalle <= 0) {
            throw new InvalidArgumentException("La salle d'inscription est obligatoire.");
        }
    }
}
