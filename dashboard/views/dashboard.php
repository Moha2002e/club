<?php 
require_once __DIR__ . '/../actions/dashboard.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <link rel="stylesheet" href="views/assets/css/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#F1F5F9',
                        accent: '#10B981'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans theme-<?php echo $currentTheme; ?>">
    <!-- Container Principal -->
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>

        <!-- Contenu Principal -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Header Principal -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-6">
                <?php 
                // Afficher le message flash s'il existe
                if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-4 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo htmlspecialchars($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <?php 
                // Afficher le message du dashboard s'il existe
                if ($dashboardMessage): 
                ?>
                    <div class="mb-6 message-banner theme-<?php echo $currentTheme; ?>">
                        <div class="message-container">
                            <div class="message-content">
                                <div class="message-icon">
                                    <i data-feather="megaphone" class="w-6 h-6"></i>
                                </div>
                                <div class="scrolling-message">
                                    <span class="message-title"><?php echo htmlspecialchars($dashboardMessage['title']); ?></span>
                                    <span class="message-separator">:</span>
                                    <span class="message-text"><?php echo htmlspecialchars($dashboardMessage['message']); ?></span>
                                </div>
                            </div>
                            <div class="message-decoration">
                                <div class="sparkle sparkle-1"></div>
                                <div class="sparkle sparkle-2"></div>
                                <div class="sparkle sparkle-3"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Bouton menu pour réouvrir la sidebar quand elle est cachée -->
                        <button id="mobile-menu-button" class="p-2 rounded-lg lg:hidden">
                            <i data-feather="menu" class="w-6 h-6 text-gray-600"></i>
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Tableau de bord</h2>
                            <p class="text-gray-600 mt-1">Vue globale du club - projets, événements et membres</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Recherche -->
                        <div class="relative">
                            <input type="text" placeholder="Rechercher..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <i data-feather="search" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenu -->
            <div class="flex-1 overflow-auto p-6">
                <!-- Cartes de statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <a href="index.php?page=projects" class="rounded-xl shadow-lg p-6 block hover:shadow-2xl transition-transform transform hover:-translate-y-1" style="background: linear-gradient(135deg, #9CD1D0 0%, #7BBCBB 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white opacity-90">📁 Mes Projets</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $stats['projects_count']; ?></p>
                                <p class="text-sm text-white opacity-90 mt-1">Inscrits</p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="folder" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </a>
                    <a href="index.php?page=tasks" class="rounded-xl shadow-lg p-6 block hover:shadow-2xl transition-transform transform hover:-translate-y-1" style="background: linear-gradient(135deg, #5558B3 0%, #3D409A 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white opacity-90">✓ Mes Tâches</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $stats['tasks_count']; ?></p>
                                <p class="text-sm text-white opacity-90 mt-1">À faire</p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="check-square" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </a>
                    <a href="index.php?page=events" class="rounded-xl shadow-lg p-6 block hover:shadow-2xl transition-transform transform hover:-translate-y-1" style="background: linear-gradient(135deg, #DF886F 0%, #D5745B 100%);">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white opacity-90">📅 Événements</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $stats['events_count']; ?></p>
                                <p class="text-sm text-white opacity-90 mt-1">À venir</p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="calendar" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Aperçu rapide -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Mes tâches -->
                    <div class="bg-primary rounded-xl shadow-lg p-6 border border-color">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-primary">
                                <a href="index.php?page=tasks" class="inline-block text-primary hover:underline">✅ Mes tâches</a>
                            </h3>

                        </div>
                        <div class="space-y-3">
                            <?php if (empty($tasks)): ?>
                                <p class="text-secondary text-center py-8">✨ Aucune tâche en cours</p>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <div class="p-4 border border-color rounded-lg group">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="font-bold text-primary"><?php echo htmlspecialchars($task['title']); ?></p>
                                                <p class="text-sm text-secondary mt-1 flex items-center gap-1">
                                                    <i data-feather="folder" class="w-3 h-3"></i>
                                                    <?php echo htmlspecialchars($task['project_title']); ?>
                                                </p>
                                            </div>
                                            <span class="badge-priority badge-<?php echo $task['priority']; ?> ml-2 shadow-sm">
                                                <?php 
                                                    $priorities = [
                                                        'low' => '✓ Basse',
                                                        'medium' => '⚠️ Moyenne',
                                                        'high' => '⚡ Haute',
                                                        'urgent' => '🔥 Urgent'
                                                    ];
                                                    echo $priorities[$task['priority']]; 
                                                ?>
                                            </span>
                                        </div>
                                        <?php if ($task['due_date']): ?>
                                            <p class="text-xs text-secondary mt-2 flex items-center gap-1 bg-secondary px-2 py-1 rounded-md inline-block">
                                                <i data-feather="clock" class="w-3 h-3"></i>
                                                <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Prochains événements -->
                    <div class="bg-primary rounded-xl shadow-lg p-6 border border-color">
                        <h3 class="text-xl font-bold text-primary mb-4">
                            <a href="index.php?page=events" class="inline-block text-primary hover:underline">📅 Prochains événements</a>
                        </h3>
                        <div class="space-y-3">
                            <?php if (empty($events)): ?>
                                <p class="text-secondary text-center py-8">✨ Aucun événement à venir</p>
                            <?php else: ?>
                                <?php foreach ($events as $event): ?>
                                    <div class="flex items-center justify-between p-4 bg-secondary rounded-lg border border-color group">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center shadow-md">
                                                <i data-feather="calendar" class="w-6 h-6 text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-primary"><?php echo htmlspecialchars($event['title']); ?></p>
                                                <p class="text-sm text-secondary flex items-center gap-1">
                                                    <i data-feather="map-pin" class="w-3 h-3"></i>
                                                    <?php echo htmlspecialchars($event['location'] ?? 'Lieu non spécifié'); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-white bg-gradient-to-r from-purple-500 to-pink-600 px-3 py-1.5 rounded-full shadow-sm">
                                                📅 <?php echo date('d M', strtotime($event['start_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Mes projets -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div class="bg-primary rounded-xl shadow-lg p-6 border border-color">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-primary">
                                <a href="index.php?page=projects" class="inline-block text-primary hover:underline">📁 Mes projets</a>
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if (empty($projects)): ?>
                                <p class="text-secondary text-center py-8 col-span-full">✨ Vous n'êtes inscrit à aucun projet</p>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <div class="group p-5 border border-color rounded-xl flex flex-col h-full">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="font-bold text-primary"><?php echo htmlspecialchars($project['title']); ?></h4>
                                            <span class="badge-status badge-<?php echo $project['status']; ?> shadow-sm">
                                                <?php 
                                                    $statuses = [
                                                        'planning' => '📋 Planification',
                                                        'active' => '✓ Actif',
                                                        'completed' => '✅ Terminé',
                                                        'on_hold' => '⏸️ En pause'
                                                    ];
                                                    echo $statuses[$project['status']] ?? $project['status']; 
                                                ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-secondary mb-3 line-clamp-2 leading-relaxed"><?php echo htmlspecialchars($project['description']); ?></p>
                                        
                                        <!-- Séparateur décoratif -->
                                        <div class="w-full h-px bg-gradient-to-r from-transparent via-green-500 to-transparent my-3 opacity-50"></div>
                                        
                                        <div class="flex items-center justify-between text-xs text-secondary mt-auto">
                                            <span class="flex items-center gap-1 bg-secondary px-2 py-1 rounded-full">
                                                <i data-feather="users" class="w-3 h-3"></i>
                                                <?php echo $project['member_count']; ?> membres
                                            </span>
                                            <span class="flex items-center gap-1 bg-secondary px-2 py-1 rounded-full">
                                                <i data-feather="check-square" class="w-3 h-3"></i>
                                                <?php echo $project['task_count']; ?> tâches
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        // Initialiser Feather Icons
        feather.replace();
        
        // Améliorer l'expérience du message banner
        document.addEventListener('DOMContentLoaded', function() {
            const messageBanner = document.querySelector('.message-banner');
            if (messageBanner) {
                // Détecter le thème actuel
                const isDarkTheme = document.body.classList.contains('theme-dark');
                
                // Ajouter un effet de clignotement subtil
                messageBanner.addEventListener('mouseenter', function() {
                    this.style.animation = 'none';
                    setTimeout(() => {
                        const animation = isDarkTheme ? 
                            'slide-in-down 0.8s ease-out, breathe-dark 4s ease-in-out infinite' :
                            'slide-in-down 0.8s ease-out, breathe-light 4s ease-in-out infinite';
                        this.style.animation = animation;
                    }, 10);
                });
                
                // Effet de particules au clic
                messageBanner.addEventListener('click', function(e) {
                    createParticles(e.clientX, e.clientY, isDarkTheme);
                });
            }
        });
        
        // Fonction pour créer des particules
        function createParticles(x, y, isDarkTheme = false) {
            const colors = isDarkTheme ? 
                ['#3b82f6', '#60a5fa', '#93c5fd', '#ffffff'] : 
                ['#ffffff', '#60a5fa', '#667eea', '#764ba2'];
            
            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'fixed';
                particle.style.left = x + 'px';
                particle.style.top = y + 'px';
                particle.style.width = '6px';
                particle.style.height = '6px';
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                particle.style.borderRadius = '50%';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '9999';
                particle.style.boxShadow = isDarkTheme ? 
                    '0 0 10px rgba(59, 130, 246, 0.8)' : 
                    '0 0 8px rgba(255, 255, 255, 0.8)';
                particle.style.animation = `particle-burst 1.2s ease-out forwards`;
                
                document.body.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 1200);
            }
        }
    </script>
    
    <style>
        @keyframes particle-burst {
            0% {
                transform: scale(0) translate(0, 0);
                opacity: 1;
            }
            100% {
                transform: scale(1) translate(
                    ${Math.random() * 200 - 100}px, 
                    ${Math.random() * 200 - 100}px
                );
                opacity: 0;
            }
        }
    </style>
</body>
</html>

