<?php
/**
 * Action de gestion des membres (Admin uniquement)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = 'Accès non autorisé. Seuls les administrateurs peuvent accéder à cette page.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=dashboard');
    exit();
}

$userDAO = new UserDAO();
$projectDAO = new ProjectDAO();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = $_POST['user_id'] ?? null;

    switch ($action) {
        case 'toggle_status':
            if ($userId && $userId != $_SESSION['user_id']) {
                $result = $userDAO->toggleUserStatus($userId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'Vous ne pouvez pas désactiver votre propre compte.';
                $_SESSION['flash_type'] = 'error';
            }
            break;

        case 'delete':
            if ($userId && $userId != $_SESSION['user_id']) {
                $result = $userDAO->deleteUser($userId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'Vous ne pouvez pas supprimer votre propre compte.';
                $_SESSION['flash_type'] = 'error';
            }
            break;
    }

    header('Location: index.php?page=members');
    exit();
}

// Récupérer les filtres
$filterRole = $_GET['filter_role'] ?? ''; // Par défaut : tous
$filterStatus = $_GET['filter_status'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'name'; // Par défaut : tri par nom

// Récupérer les membres avec filtres
try {
    $members = $userDAO->getAllMembers($filterRole, '', '', $filterStatus, $sortBy);
    
    // Récupérer les projets pour le filtre
    $projects = $projectDAO->getAllProjects();
    
    // Compter les participations aux projets pour chaque membre
    foreach ($members as $key => $member) {
        $members[$key]['project_count'] = $userDAO->countUserProjects($member['id']);
        $members[$key]['task_count'] = $userDAO->countUserTasks($member['id']);
    }
    
} catch (Exception $e) {
    error_log("Erreur members : " . $e->getMessage());
    $members = [];
    $projects = [];
}

// Statistiques
$totalMembers = count($members);
$activeMembers = count(array_filter($members, function($m) { return $m['is_active'] == 1; }));
$adminCount = count(array_filter($members, function($m) { return $m['role'] === 'admin'; }));
$studentCount = count(array_filter($members, function($m) { return $m['role'] === 'student'; }));
?>

