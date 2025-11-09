<?php
/**
 * Action de vérification OTP
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

$userDAO = new UserDAO();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'verify_otp':
            $userId = $_POST['user_id'] ?? null;
            $otpCode = trim($_POST['otp_code'] ?? '');

            if (!$userId || !$otpCode) {
                $_SESSION['flash_message'] = 'Données manquantes.';
                $_SESSION['flash_type'] = 'error';
                header('Location: index.php?page=verify_otp&user_id=' . $userId);
                exit();
            }

            if (strlen($otpCode) !== 6 || !ctype_digit($otpCode)) {
                $_SESSION['flash_message'] = 'Le code OTP doit contenir exactement 6 chiffres.';
                $_SESSION['flash_type'] = 'error';
                header('Location: index.php?page=verify_otp&user_id=' . $userId);
                exit();
            }

            $result = $userDAO->verifyOTP($userId, $otpCode);
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = $result['success'] ? 'success' : 'error';

            if ($result['success']) {
                header('Location: index.php?page=login');
            } else {
                header('Location: index.php?page=verify_otp&user_id=' . $userId);
            }
            exit();

        case 'resend_otp':
            $userId = $_POST['user_id'] ?? null;
            
            if (!$userId) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
                exit();
            }

            $result = $userDAO->regenerateOTP($userId);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
    }
}

// Vérifier que l'ID utilisateur est fourni
$userId = $_GET['user_id'] ?? null;
if (!$userId) {
    $_SESSION['flash_message'] = 'ID utilisateur manquant.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=login');
    exit();
}

// Récupérer les informations de l'utilisateur pour affichage
try {
    $user = $userDAO->getUserById($userId);
    if (!$user) {
        $_SESSION['flash_message'] = 'Utilisateur non trouvé.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=login');
        exit();
    }

    // Vérifier si le compte est déjà vérifié
    if ($user['is_verified']) {
        $_SESSION['flash_message'] = 'Ce compte est déjà vérifié.';
        $_SESSION['flash_type'] = 'info';
        header('Location: index.php?page=login');
        exit();
    }

} catch (Exception $e) {
    error_log("Erreur verify_otp : " . $e->getMessage());
    $_SESSION['flash_message'] = 'Erreur lors du chargement des informations.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=login');
    exit();
}
?>

