<?php
// Déterminer la page active pour la navigation
$currentPage = $_GET['page'] ?? 'dashboard';
?>

<!-- Overlay pour mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="bg-white shadow-lg transition-all duration-300 ease-in-out w-64 min-h-screen relative z-50">
    <div class="flex flex-col h-full">
        <!-- Header Sidebar -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <img src="../images/logo.png" alt="Logo" class="w-10 h-10 object-contain">
                <span id="logo-text" class="text-xl font-bold">Tech Lab</span>
            </div>
            <button id="toggle-sidebar" class="p-2 rounded-lg">
                <i data-feather="menu" class="w-5 h-5 text-gray-600"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <a href="index.php?page=dashboard" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'dashboard' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="home" class="w-5 h-5"></i>
                <span class="nav-text">Tableau de bord</span>
            </a>
            <a href="index.php?page=projects" class="nav-item <?php echo $currentPage === 'projects' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'projects' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="folder" class="w-5 h-5"></i>
                <span class="nav-text">Projets</span>
            </a>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="index.php?page=members" class="nav-item <?php echo $currentPage === 'members' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'members' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="users" class="w-5 h-5"></i>
                <span class="nav-text">Membres</span>
            </a>
            <?php endif; ?>
            <a href="index.php?page=events" class="nav-item <?php echo $currentPage === 'events' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'events' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="calendar" class="w-5 h-5"></i>
                <span class="nav-text">Événements</span>
            </a>
            <a href="index.php?page=tasks" class="nav-item <?php echo $currentPage === 'tasks' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'tasks' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="check-square" class="w-5 h-5"></i>
                <span class="nav-text">Tâches</span>
            </a>
            <a href="index.php?page=messages" class="nav-item <?php echo $currentPage === 'messages' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'messages' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="message-circle" class="w-5 h-5"></i>
                <span class="nav-text">Messages</span>
            </a>
            <a href="index.php?page=settings" class="nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?> flex items-center space-x-3 p-3 rounded-lg <?php echo $currentPage === 'settings' ? 'bg-primary text-white' : 'text-gray-700'; ?>">
                <i data-feather="settings" class="w-5 h-5"></i>
                <span class="nav-text">Paramètres</span>
            </a>
            <a href="index.php?page=logout" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-red-600">
                <i data-feather="log-out" class="w-5 h-5"></i>
                <span class="nav-text">Déconnexion</span>
            </a>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-gray-200">
            <a href="index.php?page=profile" class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer">
                <img src="../images/logo.png" alt="Logo" class="w-8 h-8 object-contain">
                <div class="nav-text">
                    <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . substr($_SESSION['last_name'] ?? '', 0, 1) . '.'); ?></p>
                    <p class="text-xs text-gray-500"><?php echo ($_SESSION['role'] ?? 'student') == 'admin' ? 'Administrateur' : 'Étudiant'; ?></p>
                </div>
            </a>
        </div>
    </div>
</aside>
