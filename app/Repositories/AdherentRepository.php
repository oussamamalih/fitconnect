<?php

require_once __DIR__ . '/../Entities/Adherent.php';

/**
 * AdherentRepository
 * Gère l'accès aux données de la table `adherent`.
 * Toutes les requêtes sont paramétrées (protection contre les injections SQL).
 */
class AdherentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les adhérents.
     * @return Adherent[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM adherent ORDER BY nom ASC, prenom ASC");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => Adherent::fromArray($row), $rows);
    }

    /**
     * Récupère tous les adhérents avec le nom de leur salle (JOIN) - utile pour les vues.
     */
    public function findAllWithSalle(): array
    {
        $sql = "SELECT a.*, s.nom_salle, s.ville
                FROM adherent a
                INNER JOIN salle s ON a.id_salle = s.id_salle
                ORDER BY a.nom ASC, a.prenom ASC";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Adherent
    {
        $stmt = $this->pdo->prepare("SELECT * FROM adherent WHERE id_adherent = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Adherent::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?Adherent
    {
        $stmt = $this->pdo->prepare("SELECT * FROM adherent WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ? Adherent::fromArray($row) : null;
    }

    public function create(Adherent $adherent): int
    {
        $sql = "INSERT INTO adherent (nom, prenom, email, telephone, date_inscription, id_salle)
                VALUES (:nom, :prenom, :email, :telephone, :date_inscription, :id_salle)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom'              => $adherent->getNom(),
            'prenom'           => $adherent->getPrenom(),
            'email'            => $adherent->getEmail(),
            'telephone'        => $adherent->getTelephone(),
            'date_inscription' => $adherent->getDateInscription(),
            'id_salle'         => $adherent->getIdSalle(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Adherent $adherent): bool
    {
        $sql = "UPDATE adherent
                SET nom = :nom, prenom = :prenom, email = :email,
                    telephone = :telephone, date_inscription = :date_inscription, id_salle = :id_salle
                WHERE id_adherent = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom'              => $adherent->getNom(),
            'prenom'           => $adherent->getPrenom(),
            'email'            => $adherent->getEmail(),
            'telephone'        => $adherent->getTelephone(),
            'date_inscription' => $adherent->getDateInscription(),
            'id_salle'         => $adherent->getIdSalle(),
            'id'               => $adherent->getIdAdherent(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM adherent WHERE id_adherent = :id");

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Vérifie si un adhérent possède des séances enregistrées.
     * Utile pour respecter la règle : "un adhérent ne peut pas être supprimé
     * s'il possède des séances enregistrées ou un abonnement en cours".
     */
    public function hasSeances(int $idAdherent): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM seance WHERE id_adherent = :id");
        $stmt->execute(['id' => $idAdherent]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un adhérent possède un abonnement (peu importe le statut).
     */
    public function hasAbonnement(int $idAdherent): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM abonnement WHERE id_adherent = :id");
        $stmt->execute(['id' => $idAdherent]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
