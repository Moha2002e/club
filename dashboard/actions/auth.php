<?php
/**
 * Actions d'authentification (connexion/inscription)
 */

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../DAO/UserDAO.php';

// Créer une instance de UserDAO
$userDAO = new UserDAO();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Protection CSRF basique - vérifier le referer
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (empty($referer) || strpos($referer, $_SERVER['HTTP_HOST']) === false) {
        $_SESSION['flash_message'] = 'Requête invalide.';
        $_SESSION['flash_type'] = 'error';
        header('Location: index.php?page=login');
        exit();
    }

    if ($_POST['action'] === 'register') {
        // Traitement de l'inscription
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $_SESSION['flash_message'] = 'Tous les champs sont obligatoires.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=login');
            exit();
        }
        
        if (!$userDAO->isValidEmail($email)) {
            $_SESSION['flash_message'] = 'Email invalide.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=login');
            exit();
        }
        
        if ($password !== $confirmPassword) {
            $_SESSION['flash_message'] = 'Les mots de passe ne correspondent pas.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=login');
            exit();
        }
        
        if (!$userDAO->isValidPassword($password)) {
            $_SESSION['flash_message'] = 'Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule et 1 chiffre.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=login');
            exit();
        }
        
        // Inscription
        $result = $userDAO->register($username, $email, $password, $firstName, $lastName);
        
        if (!$result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?page=login');
            exit();
        }
        
        // Stocker les informations d'activation dans la session
        $_SESSION['activation_user_id'] = $result['user_id'];
        $_SESSION['activation_token'] = $result['activation_token'];
        $_SESSION['activation_email'] = $result['email'];
        $_SESSION['activation_first_name'] = $result['first_name'];
        $_SESSION['flash_message'] = $result['message'] . ' Veuillez vérifier votre email et cliquer sur le lien d\'activation.';
        $_SESSION['flash_type'] = 'success';
        
        // Rediriger vers la page de login avec un message spécial
        header('Location: index.php?page=login&show_activation=1');
        exit();
        
    } elseif ($_POST['action'] === 'clean_activation') {
        // Nettoyer les données d'activation de la session
        unset($_SESSION['activation_user_id']);
        unset($_SESSION['activation_token']);
        unset($_SESSION['activation_email']);
        unset($_SESSION['activation_first_name']);
        exit('OK');
        
    } elseif ($_POST['action'] === 'login') {
        // Traitement de la connexion
        $username = trim($_POST['login_username']);
        $password = $_POST['login_password'];

        if (empty($username) || empty($password)) {
            $_SESSION['flash_message'] = 'Veuillez remplir tous les champs.';
            $_SESSION['flash_type'] = 'error';
        } else {
            $result = $userDAO->login($username, $password);
            
            if ($result['success']) {
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['email'] = $result['user']['email'];
                $_SESSION['first_name'] = $result['user']['first_name'];
                $_SESSION['last_name'] = $result['user']['last_name'];
                $_SESSION['role'] = $result['user']['role'];
                
                $_SESSION['flash_message'] = 'Connexion réussie !';
                $_SESSION['flash_type'] = 'success';
                header('Location: index.php?page=dashboard');
                exit();
            } elseif (isset($result['needs_verification']) && $result['needs_verification']) {
                // Rediriger vers la page de vérification OTP sans message d'erreur
                header('Location: index.php?page=verify_otp&user_id=' . $result['user_id']);
                exit();
            } else {
                // Afficher le message d'erreur seulement pour les autres cas
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'error';
            }
        }
        
        // Rediriger vers la page de login en cas d'erreur
        header('Location: index.php?page=login');
        exit();
    }
}
?>