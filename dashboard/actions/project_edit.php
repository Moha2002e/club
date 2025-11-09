<?php
/**
 * Action de modification de projet
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    $_SESSION['flash_message'] = 'Projet invalide.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

$projectDAO = new ProjectDAO();

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

// Vérifier que l'utilisateur est admin ou propriétaire
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$isOwner = $project['owner_id'] == $_SESSION['user_id'];

if (!$isAdmin && !$isOwner) {
    $_SESSION['flash_message'] = 'Accès non autorisé. Seul le propriétaire ou un admin peut modifier ce projet.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=project_detail&id=' . $project_id);
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_project') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? null;
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? 'planning';
    $visibility = $_POST['visibility'] ?? 'public';

    // Validation
    $errors = [];

    if (empty($title)) {
        $errors[] = 'Le nom du projet est requis.';
    }

    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }

    if (!in_array($status, ['planning', 'active', 'on_hold', 'completed', 'cancelled'])) {
        $errors[] = 'Statut invalide.';
    }

    if (!in_array($visibility, ['public', 'private'])) {
        $errors[] = 'Visibilité invalide.';
    }

    // Validation des dates
    if (!empty($start_date) && !empty($due_date)) {
        if (strtotime($due_date) < strtotime($start_date)) {
            $errors[] = 'La date de fin doit être après la date de début.';
        }
    }

    // Si des erreurs, retourner au formulaire
    if (!empty($errors)) {
        $_SESSION['flash_message'] = implode(' ', $errors);
        $_SESSION['flash_type'] = 'error';
    } else {
        // Mettre à jour le projet
        try {
            $updateData = [
                'title' => $title,
                'description' => $description,
                'start_date' => !empty($start_date) ? $start_date : null,
                'due_date' => !empty($due_date) ? $due_date : null,
                'status' => $status,
                'visibility' => $visibility
            ];

            $result = $projectDAO->updateProject($project_id, $updateData);

            if ($result['success']) {
                $_SESSION['flash_message'] = 'Projet modifié avec succès !';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?page=project_detail&id=' . $project_id);
                exit();
            } else {
                $_SESSION['flash_message'] = $result['message'] ?? 'Erreur lors de la modification du projet.';
                $_SESSION['flash_type'] = 'error';
            }
        } catch (Exception $e) {
            error_log("Erreur update project : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur : ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }
    }
}
?>

