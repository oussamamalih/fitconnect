<?php

require_once __DIR__ . '/../Entities/Seance.php';

/**
 * SeanceRepository
 * Gère l'accès aux données de la table `seance`.
 */
class SeanceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Seance[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM seance ORDER BY date_seance DESC");
        $rows = $stmt->fetchAll();

        // On transforme chaque ligne du tableau (issue de la BDD) en objet Seance
        $seances = [];
        foreach ($rows as $row) {
            $seances[] = Seance::fromArray($row);
        }

        return $seances;
    }

    /**
     * Récupère toutes les séances avec les infos adhérent + salle (JOIN) - pour les vues / dashboard.
     */
    public function findAllWithDetails(): array
    {
        $sql = "SELECT se.*, a.nom, a.prenom, s.nom_salle
                FROM seance se
                INNER JOIN adherent a ON se.id_adherent = a.id_adherent
                INNER JOIN salle s ON se.id_salle = s.id_salle
                ORDER BY se.date_seance DESC";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Seance
    {
        $stmt = $this->pdo->prepare("SELECT * FROM seance WHERE id_seance = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Seance::fromArray($row) : null;
    }

    public function create(Seance $seance): int
    {
        $sql = "INSERT INTO seance (date_seance, type_activite, duree, equipement_utilise, id_adherent, id_salle)
                VALUES (:date_seance, :type_activite, :duree, :equipement_utilise, :id_adherent, :id_salle)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'date_seance'         => $seance->getDateSeance(),
            'type_activite'       => $seance->getTypeActivite(),
            'duree'               => $seance->getDuree(),
            'equipement_utilise'  => $seance->getEquipementUtilise(),
            'id_adherent'         => $seance->getIdAdherent(),
            'id_salle'            => $seance->getIdSalle(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM seance WHERE id_seance = :id");

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compte le nombre total de séances (pour le dashboard).
     */
    public function countAll(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM seance")->fetchColumn();
    }

    /**
     * Répartition du nombre de séances par salle (pour le dashboard / vue d'ensemble réseau).
     */
    public function countBySalle(): array
    {
        $sql = "SELECT s.nom_salle, COUNT(se.id_seance) AS total_seances
                FROM salle s
                LEFT JOIN seance se ON s.id_salle = se.id_salle
                GROUP BY s.id_salle, s.nom_salle
                ORDER BY total_seances DESC";

        return $this->pdo->query($sql)->fetchAll();
    }
}
