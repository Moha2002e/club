<?php 
require_once __DIR__ . '/../actions/members.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membres - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen overflow-hidden">
        <?php include __DIR__ . '/../includes/nav.php'; ?>

        <!-- Contenu principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 p-4">
                <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <!-- Messages flash -->
                <?php if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo htmlspecialchars($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestion des Membres</h1>
                    <p class="text-gray-600">Gérez tous les membres du club</p>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-100 font-medium">Total Membres</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $totalMembers; ?></p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="users" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-100 font-medium">Actifs</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $activeMembers; ?></p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="user-check" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-100 font-medium">Admins</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $adminCount; ?></p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="shield" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-indigo-100 font-medium">Étudiants</p>
                                <p class="text-4xl font-bold text-white mt-2"><?php echo $studentCount; ?></p>
                            </div>
                            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i data-feather="user" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="bg-primary rounded-xl shadow-lg border border-color p-6 mb-6">
                    <form method="GET" action="" id="membersFilterForm" class="flex flex-wrap gap-4">
                        <input type="hidden" name="page" value="members">
                        
                        <!-- Filtre Rôle -->
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                            <select name="filter_role" id="roleSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tous</option>
                                <option value="admin" <?php echo $filterRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="student" <?php echo $filterRole === 'student' ? 'selected' : ''; ?>>Étudiant</option>
                            </select>
                        </div>

                        <!-- Filtre Statut -->
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="filter_status" id="statusSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Tous</option>
                                <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Actifs</option>
                                <option value="inactive" <?php echo $filterStatus === 'inactive' ? 'selected' : ''; ?>>Inactifs</option>
                            </select>
                        </div>

                        <!-- Tri -->
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                            <select name="sort_by" id="sortSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Nom</option>
                                <option value="created" <?php echo $sortBy === 'created' ? 'selected' : ''; ?>>Date d'inscription</option>
                                <option value="role" <?php echo $sortBy === 'role' ? 'selected' : ''; ?>>Rôle</option>
                            </select>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i data-feather="filter" class="w-4 h-4"></i>
                                Appliquer
                            </button>
                            <a href="index.php?page=members" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table des membres -->
                <div class="bg-primary rounded-xl shadow-lg border border-color overflow-hidden">
                    <div class="overflow-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-blue-600 to-indigo-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Membre</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Rôle</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider hidden md:table-cell">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider hidden lg:table-cell">Activité</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-primary divide-y divide-color">
                                <?php if (empty($members)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-secondary">
                                            Aucun membre trouvé
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($members as $member): ?>
                                        <tr class="group">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-md text-xs">
                                                        <?php echo strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)); ?>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-xs md:text-sm font-bold text-primary">
                                                            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                                        </div>
                                                        <div class="text-xs text-secondary hidden sm:flex items-center gap-1">
                                                            <span class="text-blue-500">@</span><?php echo htmlspecialchars($member['username']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full role-badge <?php echo $member['role'] === 'admin' ? 'role-admin' : 'role-student'; ?> shadow-sm">
                                                    <?php echo $member['role'] === 'admin' ? '👑 Admin' : '🎓 Étudiant'; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 hidden md:table-cell">
                                                <div class="text-xs text-primary font-medium"><?php echo htmlspecialchars($member['email']); ?></div>
                                                <div class="text-xs text-secondary">📅 Depuis <?php echo htmlspecialchars($member['joined_year']); ?></div>
                                            </td>
                                            <td class="px-4 py-4 hidden lg:table-cell">
                                                <div class="text-xs text-primary font-medium">📁 <?php echo $member['project_count']; ?> projet(s)</div>
                                                <div class="text-xs text-secondary">✓ <?php echo $member['task_count']; ?> tâche(s)</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full status-badge <?php echo $member['is_active'] ? 'status-active' : 'status-inactive'; ?> shadow-sm">
                                                    <?php echo $member['is_active'] ? '✓ Actif' : '✗ Inactif'; ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center space-x-1">
                                                    <!-- Modifier -->
                                                    <a href="index.php?page=edit_member&id=<?php echo $member['id']; ?>" 
                                                       class="p-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-md" title="Modifier">
                                                        <i data-feather="edit" class="w-4 h-4"></i>
                                                    </a>
                                                    
                                                    <!-- Activer/Désactiver -->
                                                    <?php if ($member['id'] != $_SESSION['user_id']): ?>
                                                        <form method="POST" action="" class="inline">
                                                            <input type="hidden" name="action" value="toggle_status">
                                                            <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                                                            <button type="submit" class="p-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg shadow-md" 
                                                                    title="<?php echo $member['is_active'] ? 'Désactiver' : 'Activer'; ?>">
                                                                <i data-feather="<?php echo $member['is_active'] ? 'user-x' : 'user-check'; ?>" class="w-4 h-4"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Supprimer -->
                                                        <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ? Cette action est irréversible.');" class="inline">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                                                            <button type="submit" class="p-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg shadow-md" title="Supprimer">
                                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        feather.replace();
        
        // Filtres automatiques
        document.getElementById('roleSelect').addEventListener('change', function() {
            document.getElementById('membersFilterForm').submit();
        });
        
        document.getElementById('statusSelect').addEventListener('change', function() {
            document.getElementById('membersFilterForm').submit();
        });
        
        document.getElementById('sortSelect').addEventListener('change', function() {
            document.getElementById('membersFilterForm').submit();
        });
    </script>
</body>
</html>
