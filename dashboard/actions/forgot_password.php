<?php
/**
 * Action de demande de réinitialisation de mot de passe
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../DAO/UserDAO.php';

header('Content-Type: application/json');

$userDAO = new UserDAO();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'request_reset':
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email requis.'
                ]);
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email invalide.'
                ]);
                exit();
            }

            // Générer le token de réinitialisation
            $result = $userDAO->generatePasswordResetToken($email);

            if ($result['success']) {
                // Construire le lien de réinitialisation
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $baseUrl = $protocol . '://' . $host . dirname($_SERVER['PHP_SELF'], 2);
                $resetLink = $baseUrl . '/index.php?page=reset_password&token=' . $result['token'];

                echo json_encode([
                    'success' => true,
                    'message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.',
                    'email' => $result['email'],
                    'first_name' => $result['first_name'],
                    'reset_link' => $resetLink,
                    'token' => $result['token']
                ]);
            } else {
                // Pour des raisons de sécurité, on retourne toujours un message de succès
                // même si l'email n'existe pas (pour éviter l'énumération des utilisateurs)
                echo json_encode([
                    'success' => true,
                    'message' => 'Si cet email existe, un lien de réinitialisation a été envoyé.'
                ]);
            }
            break;

        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Action non reconnue.'
            ]);
            break;
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Méthode non autorisée.'
    ]);
}
?>
