<?php
require_once __DIR__ . '/../../app/Entities/Abonnement.php';
require __DIR__ . '/../layouts/header.php';

/**
 * Détermine la classe CSS du badge selon le statut de l'abonnement.
 */
function badgeClassForStatut(string $statut): string
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
        <h1>Abonnements</h1>
        <p class="subtitle">Suivi des abonnements des adhérents</p>
    </div>
    <a href="index.php?page=abonnements&action=create" class="btn">+ Nouvel abonnement</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert--success">Abonnement créé avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['resilie'])): ?>
    <div class="alert alert--success">Abonnement résilié avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['supprime'])): ?>
    <div class="alert alert--success">Abonnement supprimé avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert--error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (empty($abonnements)): ?>
    <div class="empty-state">Aucun abonnement enregistré pour le moment.</div>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Adhérent</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($abonnements as $ab): ?>
                    <tr>
                        <td><?= htmlspecialchars($ab['prenom'] . ' ' . $ab['nom']) ?></td>
                        <td><?= htmlspecialchars($ab['type_abonnement']) ?></td>
                        <td><?= htmlspecialchars($ab['date_debut']) ?></td>
                        <td><?= htmlspecialchars($ab['date_fin']) ?></td>
                        <td><span class="badge <?= badgeClassForStatut($ab['statut']) ?>"><?= htmlspecialchars($ab['statut']) ?></span></td>
                        <td class="actions-cell">
                            <a href="index.php?page=abonnements&action=historique&id_adherent=<?= $ab['id_adherent'] ?>" class="btn btn--ghost btn--sm">Historique</a>
                            <?php if ($ab['statut'] === Abonnement::STATUT_ACTIF): ?>
                                <a href="index.php?page=abonnements&action=resilier&id=<?= $ab['id_abonnement'] ?>"
                                   class="btn btn--danger btn--sm"
                                   onclick="return confirm('Résilier cet abonnement ?');">Résilier</a>
                            <?php endif; ?>
                            <a href="index.php?page=abonnements&action=supprimer&id=<?= $ab['id_abonnement'] ?>"
                               class="btn btn--danger btn--sm"
                               onclick="return confirm('Supprimer définitivement cet abonnement ? Cette action est irréversible.');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
