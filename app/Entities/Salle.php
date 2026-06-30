<?php

/**
 * Entité Salle
 * Représente une salle de sport du réseau FitConnect.
 */
class Salle
{
    private ?int $idSalle;
    private string $nomSalle;
    private string $ville;
    private string $adresse;

    public function __construct(
        ?int $idSalle,
        string $nomSalle,
        string $ville,
        string $adresse
    ) {
        $this->idSalle  = $idSalle;
        $this->nomSalle = $nomSalle;
        $this->ville    = $ville;
        $this->adresse  = $adresse;
    }

    // --- Getters ---
    public function getIdSalle(): ?int
    {
        return $this->idSalle;
    }

    public function getNomSalle(): string
    {
        return $this->nomSalle;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    // --- Setters ---
    public function setIdSalle(?int $idSalle): void
    {
        $this->idSalle = $idSalle;
    }

    public function setNomSalle(string $nomSalle): void
    {
        $this->nomSalle = $nomSalle;
    }

    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * Construit un objet Salle à partir d'une ligne de résultat PDO (tableau associatif)
     */
    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_salle'],
            $row['nom_salle'],
            $row['ville'],
            $row['adresse']
        );
    }
}
