<?php

require_once __DIR__ . '/../Entities/Abonnement.php';

/**
 * AbonnementRepository
 * Gère l'accès aux données de la table `abonnement`.
 */
class AbonnementRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Abonnement[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM abonnement ORDER BY date_debut DESC");
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => Abonnement::fromArray($row), $rows);
    }

    /**
     * Récupère tous les abonnements avec les infos de l'adhérent (JOIN) - pour les vues.
     */
    public function findAllWithAdherent(): array
    {
        $sql = "SELECT ab.*, a.nom, a.prenom
                FROM abonnement ab
                INNER JOIN adherent a ON ab.id_adherent = a.id_adherent
                ORDER BY ab.date_debut DESC";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Abonnement
    {
        $stmt = $this->pdo->prepare("SELECT * FROM abonnement WHERE id_abonnement = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Abonnement::fromArray($row) : null;
    }

    /**
     * Récupère l'abonnement actif d'un adhérent (règle : un seul abonnement actif à la fois).
     */
    public function findActiveByAdherent(int $idAdherent): ?Abonnement
    {
        $sql = "SELECT * FROM abonnement
                WHERE id_adherent = :id_adherent AND statut = 'Actif'
                ORDER BY date_debut DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_adherent' => $idAdherent]);
        $row = $stmt->fetch();

        return $row ? Abonnement::fromArray($row) : null;
    }

    /**
     * Tous les abonnements d'un adhérent (historique).
     * @return Abonnement[]
     */
    public function findByAdherent(int $idAdherent): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM abonnement WHERE id_adherent = :id ORDER BY date_debut DESC");
        $stmt->execute(['id' => $idAdherent]);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => Abonnement::fromArray($row), $rows);
    }

    public function create(Abonnement $abonnement): int
    {
        $sql = "INSERT INTO abonnement (type_abonnement, date_debut, date_fin, statut, id_adherent)
                VALUES (:type_abonnement, :date_debut, :date_fin, :statut, :id_adherent)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'type_abonnement' => $abonnement->getTypeAbonnement(),
            'date_debut'      => $abonnement->getDateDebut(),
            'date_fin'        => $abonnement->getDateFin(),
            'statut'          => $abonnement->getStatut(),
            'id_adherent'     => $abonnement->getIdAdherent(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Abonnement $abonnement): bool
    {
        $sql = "UPDATE abonnement
                SET type_abonnement = :type_abonnement, date_debut = :date_debut,
                    date_fin = :date_fin, statut = :statut, id_adherent = :id_adherent
                WHERE id_abonnement = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'type_abonnement' => $abonnement->getTypeAbonnement(),
            'date_debut'      => $abonnement->getDateDebut(),
            'date_fin'        => $abonnement->getDateFin(),
            'statut'          => $abonnement->getStatut(),
            'id_adherent'     => $abonnement->getIdAdherent(),
            'id'              => $abonnement->getIdAbonnement(),
        ]);
    }

    /**
     * Met à jour uniquement le statut (ex: passer de Actif à Expire/Resilie).
     */
    public function updateStatut(int $idAbonnement, string $statut): bool
    {
        $stmt = $this->pdo->prepare("UPDATE abonnement SET statut = :statut WHERE id_abonnement = :id");

        return $stmt->execute(['statut' => $statut, 'id' => $idAbonnement]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM abonnement WHERE id_abonnement = :id");

        return $stmt->execute(['id' => $id]);
    }
}
