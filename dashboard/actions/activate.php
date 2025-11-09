<?php
/**
 * Action d'activation du compte
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

$userDAO = new UserDAO();

// Récupérer le token d'activation depuis l'URL
$activationToken = $_GET['token'] ?? null;

// Debug: Logger les informations
error_log("Token reçu: " . ($activationToken ?? 'NULL'));
error_log("URL complète: " . $_SERVER['REQUEST_URI']);

if (!$activationToken) {
    $activationResult = [
        'success' => false,
        'message' => 'Token d\'activation manquant.'
    ];
} else {
    // Activer le compte
    $activationResult = $userDAO->activateAccount($activationToken);
    error_log("Résultat activation: " . json_encode($activationResult));
}
?>
