<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Nouvelle séance</h1>
        <p class="subtitle">Enregistrer une séance d'activité physique</p>
    </div>
    <a href="index.php?page=seances" class="btn btn--ghost">← Retour à la liste</a>
</div>

<?php if ($erreur): ?>
    <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=seances&action=create">
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
                <label for="id_salle">Salle</label>
                <select id="id_salle" name="id_salle" required>
                    <option value="">-- Choisir une salle --</option>
                    <?php foreach ($salles as $salle): ?>
                        <option value="<?= $salle->getIdSalle() ?>"
                            <?= (isset($_POST['id_salle']) && (int) $_POST['id_salle'] === $salle->getIdSalle()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($salle->getNomSalle()) ?> — <?= htmlspecialchars($salle->getVille()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_seance">Date de la séance</label>
                <input type="date" id="date_seance" name="date_seance" required
                       value="<?= htmlspecialchars($_POST['date_seance'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group">
                <label for="type_activite">Type d'activité</label>
                <select id="type_activite" name="type_activite" required>
                    <option value="">-- Choisir un type --</option>
                    <?php foreach (Seance::TYPES_ACTIVITE as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"
                            <?= (isset($_POST['type_activite']) && $_POST['type_activite'] === $type) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="duree">Durée (minutes)</label>
                <input type="number" id="duree" name="duree" min="1" required
                       value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="equipement_utilise">Équipement utilisé (optionnel)</label>
                <input type="text" id="equipement_utilise" name="equipement_utilise"
                       value="<?= htmlspecialchars($_POST['equipement_utilise'] ?? '') ?>">
            </div>
        </div>

        <p class="subtitle" style="margin-top:-4px;">
            Note : la séance ne pourra être enregistrée que si l'abonnement de l'adhérent est valide aujourd'hui.
        </p>

        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer la séance</button>
            <a href="index.php?page=seances" class="btn btn--ghost">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
