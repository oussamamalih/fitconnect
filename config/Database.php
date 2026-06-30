<?php

/**
 * Database.php
 * Connexion centralisée à la base MySQL via PDO.
 * Utilise le pattern Singleton pour éviter d'ouvrir plusieurs connexions inutilement.
 */
class Database
{
    private static ?PDO $instance = null;

    // --- Paramètres de connexion (à adapter selon ton environnement local) ---
    private const HOST    = '127.0.0.1';
    private const DBNAME  = 'fitconnect_db';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';

    // Empêche l'instanciation directe (Singleton)
    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DBNAME . ";charset=" . self::CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                // En production il faudrait logger plutôt qu'afficher l'erreur brute
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    // Empêche le clonage de l'instance (Singleton)
    private function __clone()
    {
    }
}
