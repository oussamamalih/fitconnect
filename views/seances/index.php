<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Séances</h1>
        <p class="subtitle">Séances enregistrées dans le réseau FitConnect</p>
    </div>
    <a href="index.php?page=seances&action=create" class="btn">+ Nouvelle séance</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert--success">Séance enregistrée avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert--success">Séance supprimée avec succès.</div>
<?php endif; ?>

<?php if (empty($seances)): ?>
    <div class="empty-state">Aucune séance enregistrée pour le moment.</div>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Adhérent</th>
                    <th>Salle</th>
                    <th>Activité</th>
                    <th>Durée</th>
                    <th>Équipement</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seances as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['date_seance']) ?></td>
                        <td><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                        <td><?= htmlspecialchars($s['nom_salle']) ?></td>
                        <td><?= htmlspecialchars($s['type_activite']) ?></td>
                        <td><?= (int) $s['duree'] ?> min</td>
                        <td><?= htmlspecialchars($s['equipement_utilise'] ?? '—') ?></td>
                        <td class="actions-cell">
                            <a href="index.php?page=seances&action=delete&id=<?= $s['id_seance'] ?>"
                               class="btn btn--danger btn--sm"
                               onclick="return confirm('Supprimer cette séance ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
