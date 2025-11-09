<?php
/**
 * Action de modification de membre
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=dashboard');
    exit();
}

$userId = $_GET['id'] ?? null;

if (!$userId) {
    $_SESSION['flash_message'] = 'Utilisateur invalide.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=members');
    exit();
}

$userDAO = new UserDAO();

// Récupérer l'utilisateur
try {
    $member = $userDAO->getUserById($userId);
    if (!$member) {
        $_SESSION['flash_message'] = 'Utilisateur introuvable.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=members');
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur get member : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors de la récupération de l\'utilisateur.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=members');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_member') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'student';

    // Validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = 'Le prénom est requis.';
    }

    if (empty($last_name)) {
        $errors[] = 'Le nom est requis.';
    }

    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format d\'email invalide.';
    }

    if (!in_array($role, ['student', 'admin'])) {
        $errors[] = 'Rôle invalide.';
    }

    if (!empty($errors)) {
        $_SESSION['flash_message'] = implode(' ', $errors);
        $_SESSION['flash_type'] = 'error';
    } else {
        try {
            $updateData = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'role' => $role
            ];

            $result = $userDAO->updateUserByAdmin($userId, $updateData);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';

            if ($result['success']) {
                header('Location: index.php?page=members');
                exit();
            }
        } catch (Exception $e) {
            error_log("Erreur update member : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur lors de la mise à jour.';
            $_SESSION['flash_type'] = 'error';
        }
    }
}
?>

