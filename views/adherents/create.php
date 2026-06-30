<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Nouvel adhérent</h1>
        <p class="subtitle">Inscrire un nouveau membre dans une salle du réseau</p>
    </div>
    <a href="index.php?page=adherents" class="btn btn--ghost">← Retour à la liste</a>
</div>

<?php if ($erreur): ?>
    <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=adherents&action=create">
        <div class="form-grid">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="date_inscription">Date d'inscription</label>
                <input type="date" id="date_inscription" name="date_inscription" required
                       value="<?= htmlspecialchars($_POST['date_inscription'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group">
                <label for="id_salle">Salle d'inscription</label>
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
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer l'adhérent</button>
            <a href="index.php?page=adherents" class="btn btn--ghost">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
