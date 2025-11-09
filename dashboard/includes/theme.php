<?php
/**
 * Fichier commun pour gérer les thèmes
 * À inclure dans toutes les pages
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer le thème de l'utilisateur connecté
$currentTheme = 'light'; // Par défaut

if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../DAO/UserDAO.php';
        $userDAO = new UserDAO();
        $user = $userDAO->getUserById($_SESSION['user_id']);
        $currentTheme = $user['theme_preference'] ?? 'light';
    } catch (Exception $e) {
        error_log("Erreur récupération thème : " . $e->getMessage());
        $currentTheme = 'light';
    }
}
?>
