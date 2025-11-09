<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../DAO/Database.php';
require_once __DIR__ . '/../DAO/EventsDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Seuls les admins peuvent modifier les événements
if (!$isAdmin) {
    $_SESSION['flash_message'] = 'Accès non autorisé. Seuls les administrateurs peuvent modifier les événements.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=events');
    exit();
}

$eventId = $_GET['id'] ?? null;

if (!$eventId) {
    $_SESSION['flash_message'] = 'ID d\'événement manquant.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=events');
    exit();
}

try {
    $eventsDAO = new EventsDAO();
    $userDAO = new UserDAO();
    $projectDAO = new ProjectDAO();
    
    // Récupérer l'événement
    $event = $eventsDAO->getEventById($eventId);
    
    if (!$event) {
        $_SESSION['flash_message'] = 'Événement non trouvé.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=events');
        exit();
    }
    
    // Récupérer les participants actuels
    $participants = $eventsDAO->getEventParticipants($eventId);
    
    // Récupérer les données pour les formulaires
    $allProjects = $projectDAO->getAllProjects();
    $allUsers = $userDAO->getAllMembers();
    
    // Types d'événements
    $eventTypes = [
        'meeting' => 'Réunion',
        'deadline' => 'Échéance',
        'milestone' => 'Jalon',
        'presentation' => 'Présentation',
        'training' => 'Formation',
        'social' => 'Événement social',
        'general' => 'Général'
    ];
    
    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_event') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventType = $_POST['event_type'] ?? '';
        $eventDate = $_POST['event_date'] ?? '';
        $eventTime = $_POST['event_time'] ?? '';
        $projectId = $_POST['project_id'] ?? null;
        $targetType = $_POST['target_type'] ?? 'all';
        $participantsIds = $_POST['participants'] ?? [];
        
        // Validation
        if (empty($title) || empty($eventType) || empty($eventDate)) {
            $_SESSION['flash_message'] = 'Veuillez remplir tous les champs obligatoires.';
            $_SESSION['flash_type'] = 'error';
        } else {
            // Combiner date et heure
            $startDate = $eventDate . ' ' . ($eventTime ?: '00:00:00');
            
            // Préparer les données
            $eventData = [
                'title' => $title,
                'description' => $description,
                'event_type' => $eventType,
                'start_date' => $startDate,
                'project_id' => $projectId ?: null,
                'target_type' => $targetType,
                'participants' => $participantsIds
            ];
            
            // Mettre à jour l'événement
            $result = $eventsDAO->updateEvent($eventId, $eventData);
            
            if ($result['success']) {
                $_SESSION['flash_message'] = 'Événement modifié avec succès.';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?page=event_detail&id=' . $eventId);
                exit();
            } else {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'error';
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur event_edit: " . $e->getMessage());
    $_SESSION['flash_message'] = 'Une erreur est survenue.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=events');
    exit();
}
?>
