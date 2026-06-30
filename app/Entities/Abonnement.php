<?php

/**
 * Entité Abonnement
 * Représente l'abonnement d'un adhérent (Mensuel, Trimestriel, Annuel).
 */
class Abonnement
{
    public const TYPE_MENSUEL    = 'Mensuel';
    public const TYPE_TRIMESTRIEL = 'Trimestriel';
    public const TYPE_ANNUEL     = 'Annuel';

    public const STATUT_ACTIF   = 'Actif';
    public const STATUT_EXPIRE  = 'Expire';
    public const STATUT_RESILIE = 'Resilie';

    private ?int $idAbonnement;
    private string $typeAbonnement; // Mensuel | Trimestriel | Annuel
    private string $dateDebut;      // Y-m-d
    private string $dateFin;        // Y-m-d
    private string $statut;         // Actif | Expire | Resilie
    private int $idAdherent;

    public function __construct(
        ?int $idAbonnement,
        string $typeAbonnement,
        string $dateDebut,
        string $dateFin,
        string $statut,
        int $idAdherent
    ) {
        $this->idAbonnement  = $idAbonnement;
        $this->typeAbonnement = $typeAbonnement;
        $this->dateDebut     = $dateDebut;
        $this->dateFin       = $dateFin;
        $this->statut        = $statut;
        $this->idAdherent    = $idAdherent;
    }

    // --- Getters ---
    public function getIdAbonnement(): ?int
    {
        return $this->idAbonnement;
    }

    public function getTypeAbonnement(): string
    {
        return $this->typeAbonnement;
    }

    public function getDateDebut(): string
    {
        return $this->dateDebut;
    }

    public function getDateFin(): string
    {
        return $this->dateFin;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getIdAdherent(): int
    {
        return $this->idAdherent;
    }

    /**
     * Règle métier : un abonnement est valide "aujourd'hui" si
     * son statut est Actif ET que la date du jour est comprise
     * entre date_debut et date_fin.
     */
    public function estValideAujourdhui(): bool
    {
        $aujourdhui = date('Y-m-d');

        return $this->statut === self::STATUT_ACTIF
            && $aujourdhui >= $this->dateDebut
            && $aujourdhui <= $this->dateFin;
    }

    // --- Setters ---
    public function setIdAbonnement(?int $idAbonnement): void
    {
        $this->idAbonnement = $idAbonnement;
    }

    public function setTypeAbonnement(string $typeAbonnement): void
    {
        $this->typeAbonnement = $typeAbonnement;
    }

    public function setDateDebut(string $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function setDateFin(string $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function setIdAdherent(int $idAdherent): void
    {
        $this->idAdherent = $idAdherent;
    }

    /**
     * Construit un objet Abonnement à partir d'une ligne de résultat PDO
     */
    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_abonnement'],
            $row['type_abonnement'],
            $row['date_debut'],
            $row['date_fin'],
            $row['statut'],
            (int) $row['id_adherent']
        );
    }
}
