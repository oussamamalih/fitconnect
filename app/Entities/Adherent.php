<?php

/**
 * Entité Adherent
 * Représente un membre inscrit dans une salle du réseau FitConnect.
 */
class Adherent
{
    private ?int $idAdherent;
    private string $nom;
    private string $prenom;
    private string $email;
    private ?string $telephone;
    private string $dateInscription; // format Y-m-d
    private int $idSalle;

    public function __construct(
        ?int $idAdherent,
        string $nom,
        string $prenom,
        string $email,
        ?string $telephone,
        string $dateInscription,
        int $idSalle
    ) {
        $this->idAdherent      = $idAdherent;
        $this->nom             = $nom;
        $this->prenom          = $prenom;
        $this->email           = $email;
        $this->telephone       = $telephone;
        $this->dateInscription = $dateInscription;
        $this->idSalle         = $idSalle;
    }

    // --- Getters ---
    public function getIdAdherent(): ?int
    {
        return $this->idAdherent;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    public function getIdSalle(): int
    {
        return $this->idSalle;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Note : pas de setters ici. Pour modifier un adhérent, on recrée
    // simplement un nouvel objet Adherent avec le constructeur (voir
    // AdherentService::updateAdherent) - plus simple à suivre qu'une
    // série de setters.

    /**
     * Construit un objet Adherent à partir d'une ligne de résultat PDO
     */
    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id_adherent'],
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['telephone'] ?? null,
            $row['date_inscription'],
            (int) $row['id_salle']
        );
    }
}
