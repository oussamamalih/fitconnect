<?php

/**
 * public/index.php
 * Point d'entrée unique de l'application FitConnect.
 * Route les requêtes vers le bon Controller selon les paramètres GET
 * "page" (module) et "action" (opération CRUD).
 */

declare(strict_types=1);

// --- Chargement de la connexion PDO ---
require_once __DIR__ . '/../config/Database.php';

// --- Chargement des Entities ---
require_once __DIR__ . '/../app/Entities/Salle.php';
require_once __DIR__ . '/../app/Entities/Adherent.php';
require_once __DIR__ . '/../app/Entities/Abonnement.php';
require_once __DIR__ . '/../app/Entities/Seance.php';

// --- Chargement des Repositories ---
require_once __DIR__ . '/../app/Repositories/SalleRepository.php';
require_once __DIR__ . '/../app/Repositories/AdherentRepository.php';
require_once __DIR__ . '/../app/Repositories/AbonnementRepository.php';
require_once __DIR__ . '/../app/Repositories/SeanceRepository.php';

// --- Chargement des Services ---
require_once __DIR__ . '/../app/Services/AdherentService.php';
require_once __DIR__ . '/../app/Services/AbonnementService.php';
require_once __DIR__ . '/../app/Services/SeanceService.php';

// --- Chargement des Controllers ---
require_once __DIR__ . '/../app/Controllers/AdherentController.php';
require_once __DIR__ . '/../app/Controllers/AbonnementController.php';
require_once __DIR__ . '/../app/Controllers/SeanceController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';

// --- Injection de dépendances (instanciation manuelle, sans framework) ---
$pdo = Database::getConnection();

$salleRepository      = new SalleRepository($pdo);
$adherentRepository   = new AdherentRepository($pdo);
$abonnementRepository = new AbonnementRepository($pdo);
$seanceRepository     = new SeanceRepository($pdo);

$adherentService   = new AdherentService($adherentRepository);
$abonnementService = new AbonnementService($abonnementRepository);
$seanceService      = new SeanceService($seanceRepository, $abonnementService);

$adherentController   = new AdherentController($adherentService, $salleRepository);
$abonnementController = new AbonnementController($abonnementService, $adherentRepository);
$seanceController      = new SeanceController($seanceService, $adherentRepository, $salleRepository);
$dashboardController   = new DashboardController($adherentRepository, $abonnementRepository, $seanceRepository, $salleRepository);

// --- Routage simple basé sur ?page= et ?action= ---
$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($page) {
    case 'adherents':
        match ($action) {
            'create' => $adherentController->create(),
            'edit'   => $adherentController->edit($id ?? 0),
            'delete' => $adherentController->delete($id ?? 0),
            default  => $adherentController->index(),
        };
        break;

    case 'abonnements':
        match ($action) {
            'create'     => $abonnementController->create(),
            'resilier'   => $abonnementController->resilier($id ?? 0),
            'supprimer'  => $abonnementController->supprimer($id ?? 0),
            'historique' => $abonnementController->historique((int) ($_GET['id_adherent'] ?? 0)),
            default      => $abonnementController->index(),
        };
        break;

    case 'seances':
        match ($action) {
            'create' => $seanceController->create(),
            'delete' => $seanceController->delete($id ?? 0),
            default  => $seanceController->index(),
        };
        break;

    case 'dashboard':
    default:
        $dashboardController->index();
        break;
}
