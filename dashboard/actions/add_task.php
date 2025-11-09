<?php
/**
 * Action d'ajout de tâche à un projet
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';
require_once __DIR__ . '/../DAO/TaskDAO.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    $_SESSION['flash_message'] = 'Projet invalide.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

$projectDAO = new ProjectDAO();
$taskDAO = new TaskDAO();

// Récupérer le projet
try {
    $project = $projectDAO->getProjectById($project_id);
    if (!$project) {
        $_SESSION['flash_message'] = 'Projet introuvable.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=projects');
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur get project : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors de la récupération du projet.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

// Vérifier que l'utilisateur est admin
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Seuls les admins peuvent ajouter des tâches
if (!$isAdmin) {
    $_SESSION['flash_message'] = 'Accès non autorisé. Seuls les administrateurs peuvent ajouter des tâches.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=project_detail&id=' . $project_id);
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_task') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'pending';
    $due_date = $_POST['due_date'] ?? null;
    $assigned_users = $_POST['assigned_users'] ?? [];

    // Validation
    $errors = [];
    if (empty($title)) {
        $errors[] = 'Le titre est requis.';
    }
    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
        $errors[] = 'Priorité invalide.';
    }
    if (!in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'])) {
        $errors[] = 'Statut invalide.';
    }

    if (!empty($errors)) {
        $_SESSION['flash_message'] = implode(' ', $errors);
        $_SESSION['flash_type'] = 'error';
    } else {
        try {
            $taskData = [
                'title' => $title,
                'description' => $description,
                'project_id' => $project_id,
                'priority' => $priority,
                'status' => $status,
                'due_date' => !empty($due_date) ? $due_date : null,
                'created_by' => $_SESSION['user_id']
            ];

            $task_id = $taskDAO->createTask($taskData);

            if ($task_id) {
                // Assigner la tâche aux utilisateurs sélectionnés
                if (!empty($assigned_users)) {
                    foreach ($assigned_users as $user_id) {
                        $taskDAO->assignTaskToUser($task_id, $user_id);
                    }
                }

                $_SESSION['flash_message'] = 'Tâche créée avec succès !';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?page=project_detail&id=' . $project_id);
                exit();
            } else {
                $_SESSION['flash_message'] = 'Erreur lors de la création de la tâche.';
                $_SESSION['flash_type'] = 'error';
            }
        } catch (Exception $e) {
            error_log("Erreur add task : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur : ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }
    }
}

// Récupérer les membres du projet
try {
    $projectMembers = $projectDAO->getProjectMembers($project_id);
} catch (Exception $e) {
    error_log("Erreur get project members : " . $e->getMessage());
    $projectMembers = [];
}
?>

