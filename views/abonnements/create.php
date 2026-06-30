<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Nouvel abonnement</h1>
        <p class="subtitle">Souscrire un abonnement pour un adhérent</p>
    </div>
    <a href="index.php?page=abonnements" class="btn btn--ghost">← Retour à la liste</a>
</div>

<?php if ($erreur): ?>
    <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=abonnements&action=create">
        <div class="form-grid">
            <div class="form-group">
                <label for="id_adherent">Adhérent</label>
                <select id="id_adherent" name="id_adherent" required>
                    <option value="">-- Choisir un adhérent --</option>
                    <?php foreach ($adherents as $adherent): ?>
                        <option value="<?= $adherent->getIdAdherent() ?>"
                            <?= (isset($_POST['id_adherent']) && (int) $_POST['id_adherent'] === $adherent->getIdAdherent()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($adherent->getNomComplet()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type_abonnement">Type d'abonnement</label>
                <select id="type_abonnement" name="type_abonnement" required>
                    <option value="">-- Choisir un type --</option>
                    <?php foreach ($typesDisponibles as $type): ?>
                        <option value="<?= $type ?>" <?= (($_POST['type_abonnement'] ?? '') === $type) ? 'selected' : '' ?>>
                            <?= $type ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" required
                       value="<?= htmlspecialchars($_POST['date_debut'] ?? date('Y-m-d')) ?>">
            </div>
        </div>

        <p class="subtitle" style="margin-top:-4px;">
            La date de fin sera calculée automatiquement selon le type d'abonnement choisi.
        </p>

        <div class="form-actions">
            <button type="submit" class="btn">Créer l'abonnement</button>
            <a href="index.php?page=abonnements" class="btn btn--ghost">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
