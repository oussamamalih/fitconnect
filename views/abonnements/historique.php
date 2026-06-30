<?php
require_once __DIR__ . '/../../app/Entities/Abonnement.php';
require __DIR__ . '/../layouts/header.php';

function badgeClassForStatutHist(string $statut): string
{
    return match ($statut) {
        Abonnement::STATUT_ACTIF   => 'badge--success',
        Abonnement::STATUT_EXPIRE  => 'badge--warning',
        Abonnement::STATUT_RESILIE => 'badge--danger',
        default => '',
    };
}
?>

<div class="page-header">
    <div>
        <h1>Historique des abonnements</h1>
        <p class="subtitle"><?= $adherent ? htmlspecialchars($adherent->getNomComplet()) : 'Adhérent introuvable' ?></p>
    </div>
    <a href="index.php?page=abonnements" class="btn btn--ghost">← Retour à la liste</a>
</div>

<?php if (empty($historique)): ?>
    <div class="empty-state">Aucun abonnement trouvé pour cet adhérent.</div>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $ab): ?>
                    <tr>
                        <td><?= htmlspecialchars($ab->getTypeAbonnement()) ?></td>
                        <td><?= htmlspecialchars($ab->getDateDebut()) ?></td>
                        <td><?= htmlspecialchars($ab->getDateFin()) ?></td>
                        <td><span class="badge <?= badgeClassForStatutHist($ab->getStatut()) ?>"><?= htmlspecialchars($ab->getStatut()) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
