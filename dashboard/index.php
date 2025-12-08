<?php
session_start();

// Définir le chemin de base
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');
define('ACTIONS_PATH', BASE_PATH . '/actions');

// Fonction pour vérifier l'authentification
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger
function redirect($page) {
    header("Location: index.php?page=" . $page);
    exit();
}

// Récupérer la page demandée
$page = $_GET['page'] ?? 'dashboard';

// Debug pour la page activate
if ($page === 'activate') {
    error_log("Page activate demandée - Token: " . ($_GET['token'] ?? 'NON FOURNI'));
}

// Pages qui ne nécessitent pas d'authentification
$publicPages = ['login', 'activate', 'forgot_password', 'reset_password'];

// Vérifier l'authentification
if (!in_array($page, $publicPages) && !isAuthenticated()) {
    redirect('login');
}

// Gérer la déconnexion avant tout
if ($page === 'logout') {
    require_once ACTIONS_PATH . '/logout.php';
    exit();
}

// Routes disponibles
$routes = [
    'login' => VIEWS_PATH . '/login.php',
    'activate' => VIEWS_PATH . '/activate.php',
    'verify_otp' => VIEWS_PATH . '/verify_otp.php',
    'forgot_password' => VIEWS_PATH . '/forgot_password.php',
    'reset_password' => VIEWS_PATH . '/reset_password.php',
    'dashboard' => VIEWS_PATH . '/dashboard.php',
    'projects' => VIEWS_PATH . '/projects.php',
    'project_detail' => VIEWS_PATH . '/project_detail.php',
    'create_project' => VIEWS_PATH . '/create_project.php',
    'project_edit' => VIEWS_PATH . '/project_edit.php',
    'add_member' => VIEWS_PATH . '/add_member.php',
    'add_task' => VIEWS_PATH . '/add_task.php',
    'members' => VIEWS_PATH . '/members.php',
    'edit_member' => VIEWS_PATH . '/edit_member.php',
    'events' => VIEWS_PATH . '/events.php',
    'event_detail' => VIEWS_PATH . '/event_detail.php',
    'event_edit' => VIEWS_PATH . '/event_edit.php',
    'profile' => VIEWS_PATH . '/profile.php',
    'tasks' => VIEWS_PATH . '/tasks.php',
    'edit_task' => VIEWS_PATH . '/edit_task.php',
    'settings' => VIEWS_PATH . '/settings.php',
    'messages' => VIEWS_PATH . '/messages.php'
];

// Vérifier si la route existe
if (!array_key_exists($page, $routes)) {
    // Page non trouvée, rediriger vers le dashboard
    if (isAuthenticated()) {
        redirect('dashboard');
    } else {
        redirect('login');
    }
}

// Charger la vue correspondante
$viewFile = $routes[$page];

if (file_exists($viewFile)) {
    // Inclure l'action correspondante si elle existe (sauf pour les pages qui gèrent leurs propres redirections)
    $pagesWithOwnActions = [
        'verify_otp', 'activate', 'create_project', 'login', 'project_detail', 
        'dashboard', 'messages', 'profile', 'settings', 'events', 'tasks', 
        'edit_task', 'project_edit', 'add_member', 'add_task', 'edit_member', 
        'members', 'projects', 'event_detail', 'event_edit', 'forgot_password', 'reset_password'
    ];
    
    if (!in_array($page, $pagesWithOwnActions)) {
        $actionFile = ACTIONS_PATH . '/' . $page . '.php';
        if (file_exists($actionFile)) {
            require_once $actionFile;
        }
    }
    require_once $viewFile;
} else {
    // Si le fichier n'existe pas, afficher une erreur
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Page non disponible</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gray-100 flex items-center justify-center min-h-screen'>
        <div class='bg-white p-8 rounded-lg shadow-lg max-w-md text-center'>
            <h1 class='text-3xl font-bold text-red-600 mb-4'>Page non disponible</h1>
            <p class='text-gray-600 mb-6'>La page <strong>" . htmlspecialchars($page) . "</strong> n'est pas encore implémentée.</p>
            <a href='index.php?page=dashboard' class='inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition'>
                Retour au tableau de bord
            </a>
        </div>
    </body>
    </html>";
}
?>

