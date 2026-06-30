<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Adhérents</h1>
        <p class="subtitle">Liste des membres du réseau FitConnect</p>
    </div>
    <a href="index.php?page=adherents&action=create" class="btn">+ Nouvel adhérent</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert--success">Opération effectuée avec succès.</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert--error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<?php if (empty($adherents)): ?>
    <div class="empty-state">Aucun adhérent enregistré pour le moment.</div>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Salle</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adherents as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= htmlspecialchars($a['telephone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($a['nom_salle']) ?> (<?= htmlspecialchars($a['ville']) ?>)</td>
                        <td><?= htmlspecialchars($a['date_inscription']) ?></td>
                        <td class="actions-cell">
                            <a href="index.php?page=adherents&action=edit&id=<?= $a['id_adherent'] ?>" class="btn btn--ghost btn--sm">Modifier</a>
                            <a href="index.php?page=adherents&action=delete&id=<?= $a['id_adherent'] ?>"
                               class="btn btn--danger btn--sm"
                               onclick="return confirm('Supprimer cet adhérent ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
