<?php

/**
 * Entité Seance
 * Représente une séance d'activité physique réalisée par un adhérent dans une salle.
 */
class Seance
{
    private ?int $idSeance;
    private string $dateSeance;       // Y-m-d
    private string $typeActivite;
    private int $duree;               // en minutes
    private ?string $equipementUtilise;
    private int $idAdherent;
    private int $idSalle;

    public function __construct(
        ?int $idSeance,
        string $dateSeance,
        string $typeActivite,
        int $duree,
        ?string $equipementUtilise,
        int $idAdherent,
        int $idSalle
    ) {
        $this->idSeance          = $idSeance;
        $this->dateSeance        = $dateSeance;
        $this->typeActivite      = $typeActivite;
        $this->duree             = $duree;
        $this->equipementUtilise = $equipementUtilise;
        $this->idAdherent        = $idAdherent;
        $this->idSalle           = $idSalle;
    }

    // --- Getters ---
    public function getIdSeance(): ?int
    {
        return $this->idSeance;
    }

    public function getDateSeance(): string
    {
        return $this->dateSeance;
    }

    public function getTypeActivite(): string
    {
        return $this->typeActivite;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function getEquipementUtilise(): ?string
    {
        return $this->equipementUtilise;
    }

    public function getIdAdherent(): int
    {
        return $this->idAdherent;
    }

    public function getIdSalle(): int
    {
        return $this->idSalle;
    }

    // --- Setters ---
    public function setIdSeance(?int $idSeance): void
    {
        $this->idSeance = $idSeance;
    }

    public function setDateSeance(string $dateSeance): void
    {
        $this->dateSeance = $dateSeance;
    }

    public function setTypeActivite(string $typeActivite): void
    {
        $this->typeActivite = $typeActivite;
    }

    public function setDuree(int $duree): void
    {
        $this->duree = $duree;
    }

    public function setEquipementUtilise(?string $equipementUtilise): void
    {
        $this->equipementUtilise = $equipementUtilise;
    }

    public function setIdAdherent(int $idAdherent): void
    {
        $this->idAdherent = $idAdherent;
    }

    public function setIdSalle(int $idSalle): void
    {
        $this->idSalle = $idSalle;
    }

    /**
     * Construit un objet Seance à partir d'une ligne de résultat PDO
     */
    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_seance'],
            $row['date_seance'],
            $row['type_activite'],
            (int) $row['duree'],
            $row['equipement_utilise'] ?? null,
            (int) $row['id_adherent'],
            (int) $row['id_salle']
        );
    }
}
