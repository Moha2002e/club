<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/TaskDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/EventsDAO.php';
require_once __DIR__ . '/../DAO/DashboardMessageDAO.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // Initialiser les DAO
    $taskDAO = new TaskDAO();
    $projectDAO = new ProjectDAO();
    $eventsDAO = new EventsDAO();
    $messageDAO = new DashboardMessageDAO();

    // Récupérer les données
    $tasks = $taskDAO->getTasksByUserId($userId, 5);
    $projects = $projectDAO->getProjectsByUserId($userId, 5);
    $events = $eventsDAO->getEventsByUserId($userId, 5);
    $dashboardMessage = $messageDAO->getActiveMessage();

    // Récupérer les statistiques
    $stats = [
        'projects_count' => $projectDAO->countUserProjects($userId),
        'tasks_count' => $taskDAO->countPendingTasksByUserId($userId),
        'events_count' => $eventsDAO->countUpcomingEventsByUserId($userId)
    ];

    // Retourner les données en JSON si demandé
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'tasks' => $tasks,
            'projects' => $projects,
            'events' => $events
        ]);
        exit();
    }

} catch (Exception $e) {
    error_log("Erreur dashboard : " . $e->getMessage());
    
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Erreur lors du chargement des données']);
        exit();
    }
    
    // Initialiser des tableaux vides en cas d'erreur
    $tasks = [];
    $projects = [];
    $events = [];
    $stats = [
        'projects_count' => 0,
        'tasks_count' => 0,
        'events_count' => 0
    ];
}
?>

