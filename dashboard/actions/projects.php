<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'student';
$isAdmin = ($userRole === 'admin');
$projectDAO = new ProjectDAO();

// Gérer les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $projectId = $_POST['project_id'] ?? null;

    if (!$projectId) {
        $_SESSION['flash_message'] = 'Projet invalide.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=projects');
        exit();
    }

    switch ($action) {
        case 'join':
            $result = $projectDAO->addMemberToProject($projectId, $userId);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            break;

        case 'leave':
            $result = $projectDAO->removeMemberFromProject($projectId, $userId);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            break;

        case 'delete':
            // Vérifier si l'utilisateur est admin
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Action non autorisée.';
                $_SESSION['flash_type'] = 'error';
                header('Location: index.php?page=projects');
                exit();
            }
            $result = $projectDAO->deleteProject($projectId);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            break;
    }

    header('Location: index.php?page=projects');
    exit();
}

// Récupérer les projets selon le rôle
try {
    // Admin voit TOUS les projets, Student voit seulement les publics
    if ($isAdmin) {
        $projects = $projectDAO->getAllProjects();
    } else {
        $projects = $projectDAO->getAllPublicProjects();
    }
    // Dédoublonner strictement par id (sécurité contre les doublons sans masquer d'autres projets)
    $projects = array_values(array_reduce($projects, function(array $byId, array $proj) {
        if (isset($proj['id'])) {
            $byId[$proj['id']] = $proj;
        } else {
            $byId[] = $proj; // cas rare sans id
        }
        return $byId;
    }, []));
    
    // Pour chaque projet, vérifier si l'utilisateur est membre
    foreach ($projects as &$project) {
        $project['is_member'] = $projectDAO->isUserMemberOfProject($userId, $project['id']);
        $project['is_owner'] = ($project['owner_id'] == $userId);
    }
    // Important : libérer la référence laissée par foreach par référence
    unset($project);
    
} catch (Exception $e) {
    error_log("Erreur projects : " . $e->getMessage());
    $projects = [];
}
?>

