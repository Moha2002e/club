<?php
/**
 * Action de gestion des paramètres
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$userDAO = new UserDAO();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'update_theme':
            $theme = $_POST['theme'] ?? '';
            if (in_array($theme, ['light', 'dark'])) {
                $result = $userDAO->updateUserTheme($_SESSION['user_id'], $theme);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'Thème invalide.';
                $_SESSION['flash_type'] = 'error';
            }
            break;


    }

    header('Location: index.php?page=settings');
    exit();
}

// Récupérer les données
try {
    $user = $userDAO->getUserById($_SESSION['user_id']);
    $currentTheme = $user['theme_preference'] ?? 'light';
} catch (Exception $e) {
    error_log("Erreur settings : " . $e->getMessage());
    $user = null;
    $currentTheme = 'light';
}
?>
