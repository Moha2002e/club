<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/DashboardMessageDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

$messageDAO = new DashboardMessageDAO();
$userDAO = new UserDAO();

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$userId = $_SESSION['user_id'] ?? null;

// Récupérer tous les messages
$messages = $messageDAO->getAllMessages();

// Dédoublonner par sécurité (au cas où des jointures/erreurs renverraient des doublons)
$messages = array_values(array_reduce($messages, function(array $byId, array $msg) {
    $byId[$msg['id']] = $msg;
    return $byId;
}, []));

// Enrichir les messages avec les informations du créateur
foreach ($messages as &$message) {
    $creator = $userDAO->getUserById($message['created_by']);
    $message['first_name'] = $creator ? $creator['first_name'] : 'Inconnu';
    $message['last_name'] = $creator ? $creator['last_name'] : '';
}
// Libérer la référence laissée par la boucle foreach par référence
unset($message);

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'create_message':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            
            $errors = [];
            
            if (empty($title)) {
                $errors[] = 'Le titre est requis.';
            }
            
            if (empty($message)) {
                $errors[] = 'Le message est requis.';
            }
            
            if (empty($errors)) {
                $result = $messageDAO->createMessage($title, $message, $userId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
                
                if ($result['success']) {
                    header('Location: index.php?page=messages');
                    exit();
                }
            } else {
                $_SESSION['flash_message'] = implode(' ', $errors);
                $_SESSION['flash_type'] = 'error';
            }
            break;
            
        case 'toggle_message':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $messageId = $_POST['message_id'] ?? null;
            if ($messageId) {
                $result = $messageDAO->toggleMessageStatus($messageId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'ID de message invalide.';
                $_SESSION['flash_type'] = 'error';
            }
            break;
            
        case 'delete_message':
            if (!$isAdmin) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
                break;
            }
            
            $messageId = $_POST['message_id'] ?? null;
            if ($messageId) {
                $result = $messageDAO->deleteMessage($messageId);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = 'ID de message invalide.';
                $_SESSION['flash_type'] = 'error';
            }
            break;
    }
    
    header('Location: index.php?page=messages');
    exit();
}
?>

