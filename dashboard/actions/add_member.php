<?php
/**
 * Action d'ajout de membre à un projet
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    $_SESSION['flash_message'] = 'Projet invalide.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

$projectDAO = new ProjectDAO();
$userDAO = new UserDAO();

// Récupérer le projet
try {
    $project = $projectDAO->getProjectById($project_id);
    if (!$project) {
        $_SESSION['flash_message'] = 'Projet introuvable.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=projects');
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur get project : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors de la récupération du projet.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

// Vérifier que l'utilisateur est admin ou propriétaire
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$isOwner = $project['owner_id'] == $_SESSION['user_id'];

if (!$isAdmin && !$isOwner) {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=project_detail&id=' . $project_id);
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_member') {
    $user_id = $_POST['user_id'] ?? null;
    $role = $_POST['role'] ?? 'member';

    if (!$user_id) {
        $_SESSION['flash_message'] = 'Veuillez sélectionner un utilisateur.';
        $_SESSION['flash_type'] = 'error';
    } else {
        try {
            $result = $projectDAO->addMemberToProject($project_id, $user_id, $role);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            
            if ($result['success']) {
                header('Location: index.php?page=project_detail&id=' . $project_id);
                exit();
            }
        } catch (Exception $e) {
            error_log("Erreur add member : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur lors de l\'ajout du membre.';
            $_SESSION['flash_type'] = 'error';
        }
    }
}

// Récupérer les utilisateurs disponibles (qui ne sont pas déjà membres)
try {
    $pdo = Database::getInstance()->getConnection();
    $sql = "SELECT u.* FROM users u
            WHERE u.id NOT IN (
                SELECT user_id FROM project_members WHERE project_id = :project_id
            )
            AND u.is_active = 1
            ORDER BY u.first_name, u.last_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':project_id' => $project_id]);
    $availableUsers = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Erreur get available users : " . $e->getMessage());
    $availableUsers = [];
}
?>

