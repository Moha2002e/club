<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/EventsDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

$eventsDAO = new EventsDAO();
$projectDAO = new ProjectDAO();
$userDAO = new UserDAO();

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$userId = $_SESSION['user_id'] ?? null;

// Récupérer les paramètres de filtrage
$filterProject = $_GET['project'] ?? '';
$filterType = $_GET['type'] ?? '';
$filterDate = $_GET['date'] ?? '';

// Récupérer les événements selon le rôle et les filtres
if ($isAdmin) {
    // Admin voit tous les événements avec filtres
    $events = $eventsDAO->getAllEventsWithFilters($filterProject, $filterType, $filterDate);
} else {
    // Étudiant voit les événements des projets dont il est membre
    $events = $eventsDAO->getEventsByUserProjectsWithFilters($userId, $filterProject, $filterType, $filterDate);
}

// Enrichir les événements avec les informations supplémentaires
foreach ($events as $key => $event) {
    // Récupérer le créateur
    $creator = $userDAO->getUserById($event['created_by']);
    $events[$key]['creator_name'] = $creator ? $creator['first_name'] . ' ' . $creator['last_name'] : 'Inconnu';
    
    // Récupérer le projet si c'est un événement de projet
    if ($event['project_id']) {
        $project = $projectDAO->getProjectById($event['project_id']);
        $events[$key]['project_name'] = $project ? $project['title'] : 'Projet supprimé';
    } else {
        $events[$key]['project_name'] = 'Événement général';
    }
    
    // Récupérer les participants si c'est un événement spécifique
    if ($event['target_type'] === 'specific') {
        $participants = $eventsDAO->getEventParticipants($event['id']);
        $events[$key]['participants'] = $participants;
        $events[$key]['participants_count'] = count($participants);
    } else {
        $events[$key]['participants'] = [];
        $events[$key]['participants_count'] = 0;
    }
}

// Récupérer les données pour les filtres
if ($isAdmin) {
    $allProjects = $projectDAO->getAllProjects();
} else {
    $allProjects = $projectDAO->getProjectsByUserId($userId, 100); // Limite élevée pour les filtres
}

// Créer les listes pour les filtres
$eventTypes = [
    'meeting' => 'Réunion',
    'deadline' => 'Échéance',
    'milestone' => 'Jalon',
    'presentation' => 'Présentation',
    'training' => 'Formation',
    'social' => 'Événement social',
    'other' => 'Autre'
];

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'create_event':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $event_date = $_POST['event_date'] ?? '';
            $event_time = $_POST['event_time'] ?? '';
            $event_type = $_POST['event_type'] ?? '';
            $project_id = $_POST['project_id'] ?? null;
            $target_type = $_POST['target_type'] ?? 'all';
            $participants = $_POST['participants'] ?? [];
            
            $errors = [];
            
            if (empty($title)) {
                $errors[] = 'Le titre est requis.';
            }
            
            if (empty($event_date)) {
                $errors[] = 'La date est requise.';
            }
            
            if (empty($event_type)) {
                $errors[] = 'Le type d\'événement est requis.';
            }
            
            if (empty($errors)) {
                $data = [
                    'title' => $title,
                    'description' => $description,
                    'event_date' => $event_date,
                    'event_time' => $event_time,
                    'event_type' => $event_type,
                    'project_id' => $project_id ?: null,
                    'target_type' => $target_type,
                    'created_by' => $userId
                ];
                
                $result = $eventsDAO->createEvent($data, $participants);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
                
                if ($result['success']) {
                    // Rediriger sans filtres pour voir tous les événements
                    header('Location: index.php?page=events');
                    exit();
                }
            } else {
                $_SESSION['flash_message'] = implode(' ', $errors);
                $_SESSION['flash_type'] = 'error';
            }
            break;
            
        case 'delete_event':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $eventId = $_POST['event_id'] ?? null;
            if ($eventId) {
                $result = $eventsDAO->deleteEvent($eventId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'ID d\'événement invalide.';
                $_SESSION['flash_type'] = 'error';
            }
            break;
    }
    
    header('Location: index.php?page=events');
    exit();
}
?>

