<?php

require_once __DIR__ . '/../Repositories/AbonnementRepository.php';
require_once __DIR__ . '/../Entities/Abonnement.php';

/**
 * AbonnementService
 * Applique les règles de gestion liées aux abonnements :
 * - un adhérent ne détient qu'un seul abonnement actif à la fois
 * - calcul de la date de fin selon le type d'abonnement
 * - vérification de la validité d'un abonnement à la date du jour
 */
class AbonnementService
{
    private AbonnementRepository $abonnementRepository;

    /** Durée en jours selon le type d'abonnement (utilisée pour calculer date_fin) */
    private const DUREES = [
        Abonnement::TYPE_MENSUEL     => '+1 month',
        Abonnement::TYPE_TRIMESTRIEL => '+3 months',
        Abonnement::TYPE_ANNUEL      => '+1 year',
    ];

    public function __construct(AbonnementRepository $abonnementRepository)
    {
        $this->abonnementRepository = $abonnementRepository;
    }

    public function getAllAbonnements(): array
    {
        return $this->abonnementRepository->findAll();
    }

    public function getAllAbonnementsWithAdherent(): array
    {
        return $this->abonnementRepository->findAllWithAdherent();
    }

    public function getAbonnementById(int $id): ?Abonnement
    {
        return $this->abonnementRepository->findById($id);
    }

    public function getHistoriqueAdherent(int $idAdherent): array
    {
        return $this->abonnementRepository->findByAdherent($idAdherent);
    }

    /**
     * Crée un nouvel abonnement pour un adhérent.
     * Règle : un adhérent ne peut avoir qu'un seul abonnement Actif à la fois.
     * Si un abonnement actif existe déjà, il doit être résilié avant d'en créer un nouveau.
     *
     * @throws RuntimeException si l'adhérent a déjà un abonnement actif
     * @throws InvalidArgumentException si le type est invalide
     */
    public function creerAbonnement(int $idAdherent, string $typeAbonnement, string $dateDebut): int
    {
        if (!array_key_exists($typeAbonnement, self::DUREES)) {
            throw new InvalidArgumentException("Type d'abonnement invalide.");
        }

        $abonnementActif = $this->abonnementRepository->findActiveByAdherent($idAdherent);
        if ($abonnementActif !== null) {
            throw new RuntimeException(
                "Cet adhérent possède déjà un abonnement actif (#{$abonnementActif->getIdAbonnement()}). "
                . "Veuillez le résilier avant d'en créer un nouveau."
            );
        }

        $dateFin = $this->calculerDateFin($dateDebut, $typeAbonnement);

        $abonnement = new Abonnement(
            null,
            $typeAbonnement,
            $dateDebut,
            $dateFin,
            Abonnement::STATUT_ACTIF,
            $idAdherent
        );

        return $this->abonnementRepository->create($abonnement);
    }

    /**
     * Résilie un abonnement (passe son statut à "Resilie").
     */
    public function resilierAbonnement(int $idAbonnement): bool
    {
        $abonnement = $this->abonnementRepository->findById($idAbonnement);
        if ($abonnement === null) {
            throw new InvalidArgumentException("Abonnement introuvable.");
        }

        return $this->abonnementRepository->updateStatut($idAbonnement, Abonnement::STATUT_RESILIE);
    }

    /**
     * Vérifie et met à jour automatiquement le statut d'un abonnement
     * si sa date de fin est dépassée (Actif -> Expire).
     * Peut être appelée avant tout enregistrement de séance ou affichage.
     */
    public function rafraichirStatut(Abonnement $abonnement): Abonnement
    {
        if ($abonnement->getStatut() === Abonnement::STATUT_ACTIF
            && date('Y-m-d') > $abonnement->getDateFin()
        ) {
            $this->abonnementRepository->updateStatut($abonnement->getIdAbonnement(), Abonnement::STATUT_EXPIRE);
            $abonnement->setStatut(Abonnement::STATUT_EXPIRE);
        }

        return $abonnement;
    }

    /**
     * Règle centrale : vérifie si l'abonnement actif d'un adhérent est
     * valide à la date du jour. Utilisée par SeanceService avant
     * d'enregistrer une séance.
     */
    public function estAbonnementValide(int $idAdherent): bool
    {
        $abonnement = $this->abonnementRepository->findActiveByAdherent($idAdherent);

        if ($abonnement === null) {
            return false;
        }

        $abonnement = $this->rafraichirStatut($abonnement);

        return $abonnement->estValideAujourdhui();
    }

    private function calculerDateFin(string $dateDebut, string $typeAbonnement): string
    {
        $date = new DateTime($dateDebut);
        $date->modify(self::DUREES[$typeAbonnement]);
        $date->modify('-1 day'); // pour que la période soit inclusive (ex: 1er au 30 du mois)

        return $date->format('Y-m-d');
    }
}
