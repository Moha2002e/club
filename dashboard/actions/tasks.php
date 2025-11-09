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

// Récupérer les paramètres de filtrage
$filterStatus = $_GET['status'] ?? '';
$filterPriority = $_GET['priority'] ?? '';
$filterProject = $_GET['project'] ?? '';
$filterCreator = $_GET['creator'] ?? '';

// Récupérer les tâches selon le rôle et les filtres
if ($isAdmin) {
    // Admin voit toutes les tâches avec filtres
    $tasks = $taskDAO->getAllTasksWithFilters($filterStatus, $filterPriority, $filterProject, $filterCreator);
} else {
    // Étudiant voit les tâches des projets dont il est membre
    $tasks = $taskDAO->getTasksByUserProjectsWithFilters($userId, $filterStatus, $filterPriority, $filterProject, $filterCreator);
}

// Enrichir les tâches avec les informations supplémentaires
foreach ($tasks as $key => $task) {
    // Récupérer le créateur
    $creator = $userDAO->getUserById($task['created_by']);
    $tasks[$key]['creator_name'] = $creator ? $creator['first_name'] . ' ' . $creator['last_name'] : 'Inconnu';
    
    // Récupérer le projet
    $project = $projectDAO->getProjectById($task['project_id']);
    $tasks[$key]['project_name'] = $project ? $project['title'] : 'Projet supprimé';
    
    // Récupérer les utilisateurs assignés
    $assignees = $taskDAO->getTaskAssignees($task['id']);
    $tasks[$key]['assignees'] = $assignees;
    $tasks[$key]['assignees_count'] = count($assignees);
}

// Récupérer les données pour les filtres
if ($isAdmin) {
    $allProjects = $projectDAO->getAllProjects();
    $allUsers = $userDAO->getAllMembers();
} else {
    $allProjects = $projectDAO->getProjectsByUserId($userId, 100); // Limite élevée pour les filtres
    $allUsers = []; // Les étudiants ne voient pas tous les utilisateurs dans les filtres
}

// Créer les listes pour les filtres
$statusOptions = [
    'pending' => 'En attente',
    'in_progress' => 'En cours',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée'
];

$priorityOptions = [
    'low' => 'Faible',
    'medium' => 'Moyenne',
    'high' => 'Élevée',
    'urgent' => 'Urgente'
];

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'delete_task':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $taskId = $_POST['task_id'] ?? null;
            if ($taskId) {
                $result = $taskDAO->deleteTask($taskId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'ID de tâche invalide.';
                $_SESSION['flash_type'] = 'error';
            }
            break;
    }
    
    header('Location: index.php?page=tasks');
    exit();
}
?>
