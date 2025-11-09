<?php
/**
 * Action de création de projet
 */

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = 'Vous devez être connecté pour effectuer cette action.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=login');
    exit();
}

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = 'Accès non autorisé.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

require_once __DIR__ . '/../DAO/ProjectDAO.php';

// Validation des données
$errors = [];

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$start_date = $_POST['start_date'] ?? null;
$due_date = $_POST['due_date'] ?? null;
$status = $_POST['status'] ?? 'planning';
$visibility = $_POST['visibility'] ?? 'public';
$owner_id = $_SESSION['user_id'];

// Validation
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
    header('Location: index.php?page=create_project');
    exit();
}

// Gestion de l'upload de PDF
$pdfFile = null;
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/pdfs/';
    
    // Vérifier que le dossier existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileInfo = $_FILES['pdf_file'];
    $fileName = $fileInfo['name'];
    $fileTmpName = $fileInfo['tmp_name'];
    $fileSize = $fileInfo['size'];
    $fileType = $fileInfo['type'];
    
    // Validation du fichier
    if ($fileType !== 'application/pdf') {
        $errors[] = 'Le fichier doit être un PDF.';
    }
    
    if ($fileSize > 10 * 1024 * 1024) { // 10MB
        $errors[] = 'Le fichier est trop volumineux (max 10MB).';
    }
    
    if (empty($errors)) {
        // Générer un nom de fichier unique
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $uniqueFileName;
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $pdfFile = 'uploads/pdfs/' . $uniqueFileName;
        } else {
            $errors[] = 'Erreur lors de l\'upload du fichier.';
        }
    }
}

// Si des erreurs après validation du fichier, retourner au formulaire
if (!empty($errors)) {
    $_SESSION['flash_message'] = implode(' ', $errors);
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=create_project');
    exit();
}

// Créer le projet
try {
    $projectDAO = new ProjectDAO();
    
    $projectData = [
        'title' => $title,
        'description' => $description,
        'start_date' => !empty($start_date) ? $start_date : null,
        'due_date' => !empty($due_date) ? $due_date : null,
        'status' => $status,
        'visibility' => $visibility,
        'owner_id' => $owner_id,
        'pdf_file' => $pdfFile
    ];
    
    $projectId = $projectDAO->createProject($projectData);
    
    if ($projectId) {
        $_SESSION['flash_message'] = 'Projet créé avec succès !';
        $_SESSION['flash_type'] = 'success';
        header('Location: index.php?page=project_detail&id=' . $projectId);
        exit();
    } else {
        $_SESSION['flash_message'] = 'Erreur lors de la création du projet.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=create_project');
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur création projet : " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    $_SESSION['flash_message'] = 'Erreur lors de la création du projet : ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=create_project');
    exit();
}
?>

