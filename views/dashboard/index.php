<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Vue d'ensemble du réseau</h1>
        <p class="subtitle">Statistiques globales des 4 salles FitConnect</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-card__label">Adhérents (réseau)</p>
        <p class="stat-card__value"><?= $totalAdherents ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-card__label">Salles</p>
        <p class="stat-card__value"><?= $totalSalles ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-card__label">Abonnements actifs</p>
        <p class="stat-card__value"><?= $totalActifs ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-card__label">Abonnements expirés</p>
        <p class="stat-card__value"><?= $totalExpires ?></p>
    </div>
    <div class="stat-card">
        <p class="stat-card__label">Séances enregistrées</p>
        <p class="stat-card__value"><?= $totalSeances ?></p>
    </div>
</div>

<div class="card">
    <h2>Répartition des séances par salle</h2>

    <?php if (empty($repartitionParSalle)): ?>
        <div class="empty-state">Aucune donnée disponible.</div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Salle</th>
                        <th>Nombre de séances</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repartitionParSalle as $ligne): ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['nom_salle']) ?></td>
                            <td><?= (int) $ligne['total_seances'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
