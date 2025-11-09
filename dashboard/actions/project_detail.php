<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$userId = $_SESSION['user_id'];
$projectId = $_GET['id'] ?? null;

if (!$projectId) {
    $_SESSION['flash_message'] = 'Projet introuvable.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

$projectDAO = new ProjectDAO();

// Traiter l'action de suppression de tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_task') {
    $task_id = $_POST['task_id'] ?? null;
    
    if (!$task_id) {
        $_SESSION['flash_message'] = 'Tâche invalide.';
        $_SESSION['flash_type'] = 'error';
    } else {
        try {
            require_once __DIR__ . '/../DAO/TaskDAO.php';
            $taskDAO = new TaskDAO();
            
            // Vérifier que l'utilisateur actuel est admin, propriétaire ou membre
            $tempProject = $projectDAO->getProjectById($projectId);
            $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
            $isOwner = $tempProject['owner_id'] == $_SESSION['user_id'];
            $isMember = $projectDAO->isUserMemberOfProject($_SESSION['user_id'], $projectId);
            
            if (!$isAdmin && !$isOwner && !$isMember) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = $taskDAO->deleteTask($task_id);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            }
        } catch (Exception $e) {
            error_log("Erreur delete task : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur lors de la suppression de la tâche.';
            $_SESSION['flash_type'] = 'error';
        }
    }
    
    // Rediriger pour éviter la resoumission du formulaire
    header('Location: index.php?page=project_detail&id=' . $projectId);
    exit();
}

// Traiter l'action de suppression de membre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_member') {
    $user_id = $_POST['user_id'] ?? null;
    
    if (!$user_id) {
        $_SESSION['flash_message'] = 'Utilisateur invalide.';
        $_SESSION['flash_type'] = 'error';
    } else {
        try {
            // Vérifier que l'utilisateur actuel est admin ou propriétaire
            $tempProject = $projectDAO->getProjectById($projectId);
            $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
            $isOwner = $tempProject['owner_id'] == $_SESSION['user_id'];
            
            if (!$isAdmin && !$isOwner) {
                $_SESSION['flash_message'] = 'Accès non autorisé.';
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = $projectDAO->removeMemberFromProject($projectId, $user_id);
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';
            }
        } catch (Exception $e) {
            error_log("Erreur remove member : " . $e->getMessage());
            $_SESSION['flash_message'] = 'Erreur lors de la suppression du membre.';
            $_SESSION['flash_type'] = 'error';
        }
    }
    
    // Rediriger pour éviter la resoumission du formulaire
    header('Location: index.php?page=project_detail&id=' . $projectId);
    exit();
}

try {
    // Récupérer les détails du projet
    $project = $projectDAO->getProjectById($projectId);
    
    if (!$project) {
        // Si on est en debug, ne pas rediriger : rester sur la page et exposer des informations utiles
        if (isset($_GET['debug']) && $_GET['debug'] === '1') {
            $debug_not_found = true;
            $debug_missing_project_id = $projectId;
            // Préparer un placeholder minimal pour éviter des erreurs dans la vue
            $project = [
                'id' => $projectId,
                'title' => '(Projet introuvable)',
                'status' => 'unknown',
                'visibility' => 'private',
                'description' => 'Le projet demandé est introuvable en base de données.',
                'pdf_file' => null,
                'owner_first_name' => '',
                'owner_last_name' => '',
                'owner_id' => 0,
                'start_date' => null,
                'due_date' => null
            ];
            // members et tasks vides pour éviter erreurs
            $members = [];
            $tasks = [];
        } else {
            $_SESSION['flash_message'] = 'Projet introuvable.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=projects');
            exit();
        }
    }
    
    // Vérifier les permissions (si privé et pas membre et pas admin)
    $isMember = $projectDAO->isUserMemberOfProject($userId, $projectId);
    $isOwner = ($project['owner_id'] == $userId);
    $isAdmin = ($_SESSION['role'] === 'admin');
    
    if ($project['visibility'] === 'private' && !$isMember && !$isAdmin) {
        $_SESSION['flash_message'] = 'Vous n\'avez pas accès à ce projet.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=projects');
        exit();
    }
    
    // Récupérer les membres et tâches
    $members = $projectDAO->getProjectMembers($projectId);
    $tasks = $projectDAO->getProjectTasks($projectId);
    
    // DEBUG: si demandé via ?debug=1, logger les tailles brutes et les IDs dupliqués
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        try {
            $rawMemberIds = array_map(function($m) { return $m['user_id'] ?? null; }, $members);
            $rawTaskIds = array_map(function($t) { return $t['id'] ?? null; }, $tasks);

            $uniqueMemberIds = array_unique($rawMemberIds);
            $uniqueTaskIds = array_unique($rawTaskIds);

            // IDs qui apparaissent plus d'une fois
            $dupeMemberIds = array_values(array_diff_assoc($rawMemberIds, $uniqueMemberIds));
            $dupeTaskIds = array_values(array_diff_assoc($rawTaskIds, $uniqueTaskIds));

            error_log("[DEBUG project_detail] project_id={$projectId} raw_members=" . count($members) . " unique_member_ids=" . count($uniqueMemberIds) . " dupes=" . json_encode($dupeMemberIds));
            error_log("[DEBUG project_detail] project_id={$projectId} raw_tasks=" . count($tasks) . " unique_task_ids=" . count($uniqueTaskIds) . " dupes=" . json_encode($dupeTaskIds));
            
            // Préparer aussi un payload JSON utilisable côté client (console navigateur)
            $debugData = [
                'project_id' => $projectId,
                'raw_members_count' => count($members),
                'unique_member_count' => count($uniqueMemberIds),
                'dupe_member_ids' => array_values(array_filter($dupeMemberIds, function($v) { return $v !== null; })),
                'raw_task_count' => count($tasks),
                'unique_task_count' => count($uniqueTaskIds),
                'dupe_task_ids' => array_values(array_filter($dupeTaskIds, function($v) { return $v !== null; })),
                // fournir aussi quelques exemples complets (tronqués) pour inspection rapide
                'members_sample' => array_slice($members, 0, 10),
                'tasks_sample' => array_slice($tasks, 0, 10)
            ];

            // Encodage JSON sûr pour inclusion dans le HTML
            $debugDataJson = json_encode($debugData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        } catch (Exception $e) {
            error_log("[DEBUG project_detail] erreur debug: " . $e->getMessage());
        }
    }
    
    // Dédoublonner par sécurité
    $members = array_values(array_reduce($members, function(array $byUser, array $m) {
        $byUser[$m['user_id']] = $m;
        return $byUser;
    }, []));
    $tasks = array_values(array_reduce($tasks, function(array $byTask, array $t) {
        $byTask[$t['id']] = $t;
        return $byTask;
    }, []));
    
} catch (Exception $e) {
    error_log("Erreur project_detail : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors du chargement du projet.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}
?>

