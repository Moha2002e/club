<?php
/**
 * Action de réinitialisation de mot de passe
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

$userDAO = new UserDAO();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'reset_password':
            $token = trim($_POST['token'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            // Validation
            if (empty($token) || empty($password) || empty($confirmPassword)) {
                $_SESSION['flash_message'] = 'Tous les champs sont requis.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
                exit();
            }

            // Vérifier que les mots de passe correspondent
            if ($password !== $confirmPassword) {
                $_SESSION['flash_message'] = 'Les mots de passe ne correspondent pas.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
                exit();
            }

            // Vérifier la complexité du mot de passe
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
                $_SESSION['flash_message'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
                $_SESSION['flash_type'] = 'error';
                header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
                exit();
            }

            // Réinitialiser le mot de passe
            $result = $userDAO->resetPassword($token, $password);

            if ($result['success']) {
                $_SESSION['flash_message'] = 'Mot de passe réinitialisé avec succès ! Vous pouvez maintenant vous connecter.';
                $_SESSION['flash_type'] = 'success';
                header('Location: ../index.php?page=login');
            } else {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'error';
                header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
            }
            exit();
            break;

        default:
            $_SESSION['flash_message'] = 'Action non reconnue.';
            $_SESSION['flash_type'] = 'error';
            header('Location: ../index.php?page=login');
            exit();
            break;
    }
} else {
    $_SESSION['flash_message'] = 'Méthode non autorisée.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../index.php?page=login');
    exit();
}
?>
