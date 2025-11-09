<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../DAO/Database.php';
require_once __DIR__ . '/../DAO/EventsDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
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
    
    // Récupérer l'événement
    $event = $eventsDAO->getEventById($eventId);
    
    if (!$event) {
        $_SESSION['flash_message'] = 'Événement non trouvé.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=events');
        exit();
    }
    
    // Vérifier les permissions
    if (!$isAdmin) {
        // Vérifier si l'utilisateur est concerné par cet événement
        $isConcerned = false;
        
        if ($event['target_type'] === 'all') {
            $isConcerned = true;
        } else {
            // Vérifier si l'utilisateur est participant
            $participants = $eventsDAO->getEventParticipants($eventId);
            foreach ($participants as $participant) {
                if ($participant['user_id'] == $_SESSION['user_id']) {
                    $isConcerned = true;
                    break;
                }
            }
            
            // Vérifier si l'utilisateur est membre du projet
            if (!$isConcerned && $event['project_id']) {
                $projectDAO = new ProjectDAO();
                $isConcerned = $projectDAO->isUserMemberOfProject($_SESSION['user_id'], $event['project_id']);
            }
        }
        
        if (!$isConcerned) {
            $_SESSION['flash_message'] = 'Accès non autorisé à cet événement.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=events');
            exit();
        }
    }
    
    // Récupérer les participants
    $participants = $eventsDAO->getEventParticipants($eventId);
    
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
    
    // Traitement des actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'delete_event' && $isAdmin) {
            $result = $eventsDAO->deleteEvent($eventId);
            if ($result['success']) {
                $_SESSION['flash_message'] = 'Événement supprimé avec succès.';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?page=events');
                exit();
            } else {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'error';
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur event_detail: " . $e->getMessage());
    $_SESSION['flash_message'] = 'Une erreur est survenue.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=events');
    exit();
}
?>
