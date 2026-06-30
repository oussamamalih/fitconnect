<?php

require_once __DIR__ . '/../Entities/Salle.php';

/**
 * SalleRepository
 * Gère l'accès aux données de la table `salle`.
 */
class SalleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les salles.
     * @return Salle[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM salle ORDER BY nom_salle ASC");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => Salle::fromArray($row), $rows);
    }

    public function findById(int $id): ?Salle
    {
        $stmt = $this->pdo->prepare("SELECT * FROM salle WHERE id_salle = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Salle::fromArray($row) : null;
    }

    public function create(Salle $salle): int
    {
        $sql = "INSERT INTO salle (nom_salle, ville, adresse) VALUES (:nom_salle, :ville, :adresse)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom_salle' => $salle->getNomSalle(),
            'ville'     => $salle->getVille(),
            'adresse'   => $salle->getAdresse(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Salle $salle): bool
    {
        $sql = "UPDATE salle SET nom_salle = :nom_salle, ville = :ville, adresse = :adresse WHERE id_salle = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom_salle' => $salle->getNomSalle(),
            'ville'     => $salle->getVille(),
            'adresse'   => $salle->getAdresse(),
            'id'        => $salle->getIdSalle(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM salle WHERE id_salle = :id");

        return $stmt->execute(['id' => $id]);
    }
}
