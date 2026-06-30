<?php

/**
 * public/test.php
 * Script de test rapide pour valider chaque couche (Entities, Repositories, Services)
 * indépendamment de l'interface utilisateur.
 *
 * Usage : exécuter en ligne de commande -> php public/test.php
 * ou via le navigateur -> http://localhost/fitconnect/public/test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

require_once __DIR__ . '/../app/Entities/Salle.php';
require_once __DIR__ . '/../app/Entities/Adherent.php';
require_once __DIR__ . '/../app/Entities/Abonnement.php';
require_once __DIR__ . '/../app/Entities/Seance.php';

require_once __DIR__ . '/../app/Repositories/SalleRepository.php';
require_once __DIR__ . '/../app/Repositories/AdherentRepository.php';
require_once __DIR__ . '/../app/Repositories/AbonnementRepository.php';
require_once __DIR__ . '/../app/Repositories/SeanceRepository.php';

require_once __DIR__ . '/../app/Services/AdherentService.php';
require_once __DIR__ . '/../app/Services/AbonnementService.php';
require_once __DIR__ . '/../app/Services/SeanceService.php';

// Forcer le mode texte brut si exécuté dans un navigateur
if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

function section(string $titre): void
{
    echo "\n=== {$titre} ===\n";
}

function ok(string $message): void
{
    echo "[OK] {$message}\n";
}

function fail(string $message): void
{
    echo "[FAIL] {$message}\n";
}

try {
    $pdo = Database::getConnection();
    section('Connexion PDO');
    ok('Connexion établie à la base ' . 'fitconnect_db');

    // -----------------------------------------------------------
    // 1. Test de la couche Entities
    // -----------------------------------------------------------
    section('Entities');

    $adherentTest = new Adherent(null, 'Test', 'Unitaire', 'test.unitaire@example.com', '0600000000', date('Y-m-d'), 1);
    echo $adherentTest->getNomComplet() === 'Unitaire Test'
        ? ok('Adherent::getNomComplet() fonctionne correctement')
        : fail('Adherent::getNomComplet() a retourné une valeur inattendue');

    $abonnementTest = new Abonnement(null, Abonnement::TYPE_MENSUEL, '2026-01-01', '2026-01-31', Abonnement::STATUT_ACTIF, 1);
    echo $abonnementTest->estValideAujourdhui() === false
        ? ok('Abonnement::estValideAujourdhui() détecte correctement un abonnement passé comme invalide')
        : fail('Abonnement::estValideAujourdhui() devrait retourner false pour une période passée');

    // -----------------------------------------------------------
    // 2. Test de la couche Repositories
    // -----------------------------------------------------------
    section('Repositories');

    $salleRepository      = new SalleRepository($pdo);
    $adherentRepository   = new AdherentRepository($pdo);
    $abonnementRepository = new AbonnementRepository($pdo);
    $seanceRepository     = new SeanceRepository($pdo);

    $salles = $salleRepository->findAll();
    echo count($salles) > 0
        ? ok('SalleRepository::findAll() a retourné ' . count($salles) . ' salle(s)')
        : fail('SalleRepository::findAll() n\'a retourné aucune salle');

    $adherents = $adherentRepository->findAll();
    echo count($adherents) > 0
        ? ok('AdherentRepository::findAll() a retourné ' . count($adherents) . ' adhérent(s)')
        : fail('AdherentRepository::findAll() n\'a retourné aucun adhérent');

    $premierAdherent = $adherents[0] ?? null;
    if ($premierAdherent !== null) {
        $abonnementActif = $abonnementRepository->findActiveByAdherent($premierAdherent->getIdAdherent());
        echo $abonnementActif !== null
            ? ok("AbonnementRepository::findActiveByAdherent() a trouvé l'abonnement #" . $abonnementActif->getIdAbonnement())
            : fail("Aucun abonnement actif trouvé pour l'adhérent #" . $premierAdherent->getIdAdherent());

        $seancesAdherent = $seanceRepository->findByAdherent($premierAdherent->getIdAdherent());
        echo ok('SeanceRepository::findByAdherent() a retourné ' . count($seancesAdherent) . ' séance(s)');
    }

    $totalSeances = $seanceRepository->countAll();
    ok("SeanceRepository::countAll() = {$totalSeances}");

    // -----------------------------------------------------------
    // 3. Test de la couche Services (règles de gestion)
    // -----------------------------------------------------------
    section('Services - règles de gestion');

    $adherentService   = new AdherentService($adherentRepository);
    $abonnementService = new AbonnementService($abonnementRepository);
    $seanceService      = new SeanceService($seanceRepository, $abonnementService);

    if ($premierAdherent !== null) {
        $idTest = $premierAdherent->getIdAdherent();
        $estValide = $abonnementService->estAbonnementValide($idTest);
        echo ok("AbonnementService::estAbonnementValide(#{$idTest}) = " . ($estValide ? 'true' : 'false'));

        // Test de la règle : impossible de créer un 2e abonnement actif
        try {
            $abonnementService->creerAbonnement($idTest, Abonnement::TYPE_MENSUEL, date('Y-m-d'));
            fail('La création d\'un second abonnement actif aurait dû être bloquée (si un abonnement actif existe déjà)');
        } catch (RuntimeException $e) {
            ok('Règle respectée : ' . $e->getMessage());
        }

        // Test de la règle : suppression d'un adhérent avec séances/abonnement doit échouer
        try {
            $adherentService->deleteAdherent($idTest);
            fail('La suppression aurait dû être bloquée (adhérent avec séances ou abonnement)');
        } catch (RuntimeException $e) {
            ok('Règle respectée : ' . $e->getMessage());
        }

        // Test de la règle : enregistrement de séance si abonnement invalide
        $adherentSansAbonnementValide = null;
        foreach ($adherents as $a) {
            if (!$abonnementService->estAbonnementValide($a->getIdAdherent())) {
                $adherentSansAbonnementValide = $a;
                break;
            }
        }

        if ($adherentSansAbonnementValide !== null) {
            try {
                $seanceService->enregistrerSeance(
                    date('Y-m-d'),
                    'Test',
                    30,
                    null,
                    $adherentSansAbonnementValide->getIdAdherent(),
                    $adherentSansAbonnementValide->getIdSalle()
                );
                fail('L\'enregistrement de séance aurait dû être bloqué (abonnement non valide)');
            } catch (RuntimeException $e) {
                ok('Règle respectée : ' . $e->getMessage());
            }
        } else {
            echo "[INFO] Tous les adhérents testés ont un abonnement valide, test de blocage de séance ignoré.\n";
        }
    }

    section('Résumé');
    echo "Tests terminés. Vérifiez les lignes [FAIL] ci-dessus s'il y en a.\n";
} catch (Throwable $e) {
    fail('Erreur inattendue : ' . $e->getMessage());
}
