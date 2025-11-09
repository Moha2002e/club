<?php 
require_once __DIR__ . '/../actions/projects.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
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
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>

        <!-- Contenu Principal -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 p-6">
                <?php 
                if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-4 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo htmlspecialchars($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-button" class="p-2 rounded-lg hover:bg-gray-100 transition-colors lg:hidden">
                            <i data-feather="menu" class="w-6 h-6 text-gray-600"></i>
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                <?php echo $isAdmin ? 'Tous les Projets' : 'Projets Publics'; ?>
                            </h2>
                            <p class="text-gray-600 mt-1">
                                <?php echo $isAdmin ? 'Gérez tous les projets du club' : 'Découvrez et rejoignez des projets'; ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($isAdmin): ?>
                        <a href="index.php?page=create_project" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition-colors">
                            <i data-feather="plus" class="w-5 h-5"></i>
                            <span>Nouveau projet</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="flex-1 overflow-auto p-6">
                <?php if (empty($projects)): ?>
                    <div class="text-center py-12">
                        <i data-feather="folder" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun projet disponible</h3>
                        <p class="text-gray-500">Il n'y a pas encore de projets publics.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($projects as $project): ?>
                            <div class="group bg-primary rounded-xl shadow-lg border border-color hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden flex flex-col h-full">
                                <!-- Bandeau de statut en haut -->
                                <div class="h-2 <?php 
                                    $statusColors = [
                                        'planning' => 'bg-yellow-500',
                                        'active' => 'bg-green-500',
                                        'completed' => 'bg-blue-500',
                                        'on_hold' => 'bg-orange-500'
                                    ];
                                    echo $statusColors[$project['status']] ?? 'bg-gray-500';
                                ?>"></div>
                                
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-start justify-between mb-4">
                                        <h3 class="text-xl font-bold text-primary group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($project['title']); ?></h3>
                                        <span class="badge-status badge-<?php echo $project['status']; ?> animate-pulse">
                                            <?php 
                                                $statuses = ['planning' => 'Planification', 'active' => 'Actif', 'completed' => 'Terminé', 'on_hold' => 'En pause'];
                                                echo $statuses[$project['status']] ?? $project['status']; 
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-secondary mb-4 line-clamp-3 leading-relaxed"><?php echo htmlspecialchars($project['description'] ?? 'Pas de description'); ?></p>
                                    
                                    <!-- Séparateur décoratif -->
                                    <div class="w-full h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent my-2 opacity-50"></div>
                                    
                                        <!-- Infos (propriétaire, membres, échéance) placées juste au-dessus des boutons -->
                                        <div class="flex items-center justify-between text-xs text-secondary mb-2">
                                            <span class="flex items-center gap-1 bg-secondary px-2 py-1 rounded-full">
                                                <i data-feather="user" class="w-3 h-3"></i>
                                                <?php echo htmlspecialchars($project['owner_first_name'] . ' ' . $project['owner_last_name']); ?>
                                            </span>
                                            <span class="flex items-center gap-1 bg-secondary px-2 py-1 rounded-full">
                                                <i data-feather="users" class="w-3 h-3"></i>
                                                <?php echo $project['member_count']; ?> membres
                                            </span>
                                        </div>

                                        <?php if ($project['due_date']): ?>
                                            <div class="text-xs text-secondary mb-2 flex items-center gap-1 bg-secondary px-2 py-1 rounded-lg inline-block">
                                                <i data-feather="calendar" class="w-3 h-3"></i>
                                                Échéance : <?php echo date('d/m/Y', strtotime($project['due_date'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    
                                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                                        <!-- Bouton Voir détails -->
                                                     <a href="index.php?page=project_detail&id=<?php echo $project['id']; ?>" 
                                                         class="w-full sm:flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                            Détails
                                        </a>
                                        
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <!-- Admin : Boutons Modifier et Supprimer -->
                                                          <a href="index.php?page=project_edit&id=<?php echo $project['id']; ?>" 
                                                              class="w-full sm:flex-1 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-sm font-semibold rounded-lg hover:from-yellow-600 hover:to-orange-600 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                                <i data-feather="edit" class="w-4 h-4"></i>
                                                Modifier
                                            </a>
                                            <form method="POST" class="w-full sm:flex-1" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-semibold rounded-lg hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                        <?php elseif (!$project['is_owner']): ?>
                                            <?php if ($project['is_member']): ?>
                                                <!-- Bouton Quitter -->
                                                <form method="POST" class="w-full sm:flex-1" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter ce projet ?');">
                                                    <input type="hidden" name="action" value="leave">
                                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                    <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-pink-700 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                                        <i data-feather="log-out" class="w-4 h-4"></i>
                                                        Quitter
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <!-- Bouton Rejoindre -->
                                                <form method="POST" class="w-full sm:flex-1">
                                                    <input type="hidden" name="action" value="join">
                                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                    <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                                        <i data-feather="user-plus" class="w-4 h-4"></i>
                                                        Rejoindre
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                        <span class="w-full sm:flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-lg text-center flex items-center justify-center gap-2 shadow-md">
                                                <i data-feather="star" class="w-4 h-4"></i>
                                                Propriétaire
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>
