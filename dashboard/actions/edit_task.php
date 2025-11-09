<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/TaskDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

$taskDAO = new TaskDAO();
$projectDAO = new ProjectDAO();
$userDAO = new UserDAO();

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$userId = $_SESSION['user_id'] ?? null;

// Récupérer l'ID de la tâche
$taskId = $_GET['id'] ?? null;

if (!$taskId) {
    $_SESSION['flash_message'] = 'ID de tâche manquant.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=tasks');
    exit();
}

// Récupérer la tâche
$task = $taskDAO->getTaskById($taskId);

if (!$task) {
    $_SESSION['flash_message'] = 'Tâche introuvable.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=tasks');
    exit();
}

// Vérifier les permissions
if (!$isAdmin) {
    // Vérifier si l'utilisateur est assigné à cette tâche
    $assignees = $taskDAO->getTaskAssignees($taskId);
    $isAssigned = false;
    foreach ($assignees as $assignee) {
        if ($assignee['id'] == $userId) {
            $isAssigned = true;
            break;
        }
    }
    
    if (!$isAssigned) {
        $_SESSION['flash_message'] = 'Accès non autorisé.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=tasks');
        exit();
    }
}

// Récupérer les informations supplémentaires
$project = $projectDAO->getProjectById($task['project_id']);
$creator = $userDAO->getUserById($task['created_by']);
$assignees = $taskDAO->getTaskAssignees($taskId);

$task['project_name'] = $project ? $project['title'] : 'Projet supprimé';
$task['creator_name'] = $creator ? $creator['first_name'] . ' ' . $creator['last_name'] : 'Inconnu';
$task['assignees'] = $assignees;

// Traiter le formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_task') {
    // Validation des données
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Le titre est requis.';
    }
    
    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }
    
    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
        $errors[] = 'Priorité invalide.';
    }
    
    if (!in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'])) {
        $errors[] = 'Statut invalide.';
    }
    
    if (empty($errors)) {
        $data = [
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $due_date ?: null
        ];
        
        $result = $taskDAO->updateTask($taskId, $data);
        $_SESSION['flash_message'] = $result['message'];
        $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            header('Location: index.php?page=tasks');
            exit();
        }
    } else {
        $_SESSION['flash_message'] = implode(' ', $errors);
        $_SESSION['flash_type'] = 'error';
    }
}
?>
