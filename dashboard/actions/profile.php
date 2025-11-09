<?php
/**
 * Action de gestion du profil utilisateur
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';
require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/TaskDAO.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$userDAO = new UserDAO();
$projectDAO = new ProjectDAO();
$taskDAO = new TaskDAO();

// Récupérer les données de l'utilisateur
try {
    $user = $userDAO->getUserById($_SESSION['user_id']);
    if (!$user) {
        $_SESSION['flash_message'] = 'Utilisateur non trouvé.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=login');
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur profile : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors du chargement du profil.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=dashboard');
    exit();
}

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'update_profile':
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            $errors = [];
            if (empty($firstName)) $errors[] = 'Le prénom est requis.';
            if (empty($lastName)) $errors[] = 'Le nom est requis.';
            if (empty($email)) $errors[] = 'L\'email est requis.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'L\'email n\'est pas valide.';

            if (empty($errors)) {
                // Vérifier si l'email est déjà utilisé par un autre utilisateur
                $existingUser = $userDAO->getUserByEmail($email);
                if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
                    $errors[] = 'Cet email est déjà utilisé par un autre utilisateur.';
                }
            }

            if (empty($errors)) {
                $data = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email
                ];
                $result = $userDAO->updateUser($_SESSION['user_id'], $data);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
                
                if ($result['success']) {
                    // Mettre à jour les données de session
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;
                    $_SESSION['email'] = $email;
                }
            } else {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
            }
            break;

        case 'update_password':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];
            if (empty($currentPassword)) $errors[] = 'Le mot de passe actuel est requis.';
            if (empty($newPassword)) $errors[] = 'Le nouveau mot de passe est requis.';
            if (empty($confirmPassword)) $errors[] = 'La confirmation du mot de passe est requise.';

            if (empty($errors)) {
                // Vérifier le mot de passe actuel
                if (!password_verify($currentPassword, $user['password'])) {
                    $errors[] = 'Le mot de passe actuel est incorrect.';
                }
                
                if (strlen($newPassword) < 8) {
                    $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
                }
                
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'Les mots de passe ne correspondent pas.';
                }
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $data = ['password' => $hashedPassword];
                $result = $userDAO->updateUser($_SESSION['user_id'], $data);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['flash_message'] = implode('<br>', $errors);
                $_SESSION['flash_type'] = 'error';
            }
            break;
    }

    header('Location: index.php?page=profile');
    exit();
}

// Récupérer les statistiques de l'utilisateur
try {
    $userProjects = $projectDAO->countUserProjects($_SESSION['user_id']);
    $userTasks = $taskDAO->countPendingTasksByUserId($_SESSION['user_id']);
    $userEvents = 0; // TODO: Implémenter countUserEvents dans EventsDAO
    
    // Récupérer les projets de l'utilisateur
    $projects = $projectDAO->getProjectsByUserId($_SESSION['user_id']);
    $projectsCount = count($projects);
    
    // Récupérer les tâches de l'utilisateur
    $tasks = $taskDAO->getTasksByUserId($_SESSION['user_id'], 5);
    $completedTasks = 0;
    foreach ($tasks as $task) {
        if ($task['status'] === 'completed') {
            $completedTasks++;
        }
    }
    
} catch (Exception $e) {
    error_log("Erreur statistiques profile : " . $e->getMessage());
    $projectsCount = 0;
    $completedTasks = 0;
    $userEvents = 0;
}
?>

