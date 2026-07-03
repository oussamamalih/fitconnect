<?php
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitConnect — Réseau de salles de sport</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
      <div class="topbar__brand">
    <img src="assets/images/gymlogo.png" alt="FitConnect Logo" class="topbar__logo">
</div>
        <nav class="topbar__nav">
            <a href="index.php?page=dashboard" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?page=adherents" class="<?= $currentPage === 'adherents' ? 'active' : '' ?>">Adhérents</a>
            <a href="index.php?page=abonnements" class="<?= $currentPage === 'abonnements' ? 'active' : '' ?>">Abonnements</a>
            <a href="index.php?page=seances" class="<?= $currentPage === 'seances' ? 'active' : '' ?>">Séances</a>
        </nav>
    </header>
    <main class="container">
