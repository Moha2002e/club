<?php 
require_once __DIR__ . '/../actions/tasks.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tâches - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
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
    <style>
        /* Espacement supplémentaire pour les cellules */
        tbody td {
            vertical-align: middle;
        }
        
        /* Style pour les boutons d'actions */
        .action-button {
            padding: 4px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-secondary font-sans theme-<?php echo $currentTheme; ?>">
    <!-- Container Principal -->
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>

        <!-- Contenu Principal -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Header Principal -->
            <header class="bg-primary shadow-lg border-b border-color p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Bouton menu pour réouvrir la sidebar quand elle est cachée -->
                        <button id="mobile-menu-button" class="p-2 rounded-lg hover:bg-secondary transition-colors lg:hidden">
                            <i data-feather="menu" class="w-6 h-6 text-primary"></i>
                        </button>
                        <div>
                            <h1 class="text-3xl font-bold text-primary">
                                ✅ Gestion des Tâches
                            </h1>
                            <p class="text-secondary mt-1"><?php echo $isAdmin ? '📋 Toutes les tâches du système' : '✓ Mes tâches assignées'; ?></p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenu -->
            <div class="flex-1 overflow-y-auto p-6">
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

                <!-- Barre de filtres -->
                <div class="bg-primary rounded-xl shadow-lg border border-color p-6 mb-6">
                    <h3 class="text-xl font-bold text-primary mb-4">
                        🔍 Filtres
                    </h3>
                    <form method="GET" action="index.php" id="filters-form">
                        <input type="hidden" name="page" value="tasks">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Filtre par statut -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tous les statuts</option>
                                    <?php foreach ($statusOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $filterStatus === $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filtre par priorité -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                                <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Toutes les priorités</option>
                                    <?php foreach ($priorityOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $filterPriority === $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filtre par projet -->
                            <div>
                                <label for="project" class="block text-sm font-medium text-gray-700 mb-2">Projet</label>
                                <select id="project" name="project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tous les projets</option>
                                    <?php foreach ($allProjects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>" <?php echo $filterProject == $project['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filtre par créateur -->
                            <div>
                                <label for="creator" class="block text-sm font-medium text-gray-700 mb-2">Créateur</label>
                                <select id="creator" name="creator" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tous les créateurs</option>
                                    <?php foreach ($allUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $filterCreator == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-color">
                            <div class="flex items-center space-x-2">
                                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg flex items-center space-x-2">
                                    <i data-feather="filter" class="w-4 h-4"></i>
                                    <span>Filtrer</span>
                                </button>
                                <a href="index.php?page=tasks" class="px-5 py-2.5 bg-secondary text-primary font-medium rounded-lg hover:bg-opacity-80 transition-all shadow-sm hover:shadow-md flex items-center space-x-2">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                    <span>Effacer</span>
                                </a>
                            </div>
                            <div class="text-sm font-medium text-primary bg-secondary px-3 py-1.5 rounded-full">
                                📊 <?php echo count($tasks); ?> tâche(s) trouvée(s)
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tableau des tâches -->
                <div class="bg-primary rounded-xl shadow-lg border border-color min-h-96 overflow-hidden">
                    <div class="overflow-auto">
                        <table class="w-full divide-y divide-color">
                            <thead class="bg-gradient-to-r from-blue-600 to-indigo-600">
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider">✓ Tâche</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider hidden lg:table-cell">👤 Créateur</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider hidden md:table-cell">📁 Projet</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider">⚡ Priorité</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider">📊 Statut</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider hidden xl:table-cell">📅 Échéance</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider hidden lg:table-cell">👥 Assignés</th>
                                    <th class="px-4 py-4 text-left text-xs md:text-sm font-bold text-white uppercase tracking-wider">⚙️ Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-primary divide-y divide-color">
                                <?php if (empty($tasks)): ?>
                                    <tr>
                                        <td colspan="8" class="px-8 py-16 text-center text-secondary">
                                            <i data-feather="check-square" class="w-12 h-12 mx-auto mb-4 text-secondary"></i>
                                            <p class="text-lg font-medium text-primary">Aucune tâche trouvée</p>
                                            <p class="text-sm text-secondary"><?php echo $isAdmin ? 'Aucune tâche n\'a été créée dans le système.' : 'Vous n\'avez aucune tâche assignée.'; ?></p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr class="group">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-md">
                                                            <i data-feather="check-square" class="w-5 h-5 text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-xs md:text-sm font-bold text-primary"><?php echo htmlspecialchars($task['title']); ?></div>
                                                        <div class="text-xs text-secondary hidden md:block"><?php echo htmlspecialchars(substr($task['description'], 0, 30)) . (strlen($task['description']) > 30 ? '...' : ''); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 hidden lg:table-cell">
                                                <div class="text-xs md:text-sm font-medium text-primary"><?php echo htmlspecialchars($task['creator_name']); ?></div>
                                            </td>
                                            <td class="px-4 py-4 hidden md:table-cell">
                                                <div class="text-xs md:text-sm font-medium text-primary"><?php echo htmlspecialchars($task['project_name']); ?></div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <?php
                                                $priorityColors = [
                                                    'urgent' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                                                    'high' => 'bg-gradient-to-r from-orange-500 to-orange-600 text-white',
                                                    'medium' => 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white',
                                                    'low' => 'bg-gradient-to-r from-green-500 to-green-600 text-white'
                                                ];
                                                $priorityLabels = [
                                                    'urgent' => '🔥 Urgente',
                                                    'high' => '⚡ Élevée',
                                                    'medium' => '⚠️ Moyenne',
                                                    'low' => '✓ Faible'
                                                ];
                                                ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full shadow-sm <?php echo $priorityColors[$task['priority']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                                    <?php echo $priorityLabels[$task['priority']] ?? ucfirst($task['priority']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white',
                                                    'in_progress' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
                                                    'completed' => 'bg-gradient-to-r from-green-500 to-green-600 text-white',
                                                    'cancelled' => 'bg-gradient-to-r from-gray-500 to-gray-600 text-white'
                                                ];
                                                $statusLabels = [
                                                    'pending' => '⏳ En attente',
                                                    'in_progress' => '🔄 En cours',
                                                    'completed' => '✅ Terminée',
                                                    'cancelled' => '❌ Annulée'
                                                ];
                                                ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full shadow-sm <?php echo $statusColors[$task['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                                    <?php echo $statusLabels[$task['status']] ?? ucfirst($task['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-xs md:text-sm text-gray-900 hidden xl:table-cell">
                                                <?php if ($task['due_date']): ?>
                                                    <?php 
                                                    $dueDate = new DateTime($task['due_date']);
                                                    $now = new DateTime();
                                                    $isOverdue = $dueDate < $now && $task['status'] !== 'completed';
                                                    ?>
                                                    <span class="<?php echo $isOverdue ? 'text-red-600 font-medium' : ''; ?>">
                                                        <?php echo $dueDate->format('d/m/Y'); ?>
                                                    </span>
                                                    <?php if ($isOverdue): ?>
                                                        <span class="ml-1 text-xs text-red-500">(En retard)</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400">Non définie</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 hidden lg:table-cell">
                                                <div class="flex items-center">
                                                    <div class="flex -space-x-2">
                                                        <?php foreach (array_slice($task['assignees'], 0, 2) as $assignee): ?>
                                                            <div class="h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center border-2 border-white task-assignee-avatar" title="<?php echo htmlspecialchars($assignee['first_name'] . ' ' . $assignee['last_name']); ?>">
                                                                <span class="text-xs font-medium text-white"><?php echo strtoupper(substr($assignee['first_name'], 0, 1) . substr($assignee['last_name'], 0, 1)); ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <?php if ($task['assignees_count'] > 2): ?>
                                                            <div class="h-7 w-7 rounded-full bg-blue-500 flex items-center justify-center border-2 border-white task-assignee-avatar">
                                                                <span class="text-xs font-medium text-white">+<?php echo $task['assignees_count'] - 2; ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="ml-2 text-xs text-gray-500"><?php echo $task['assignees_count']; ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <!-- Bouton Détails -->
                                                    <button onclick="showTaskDetails(<?php echo htmlspecialchars(json_encode($task)); ?>)" class="text-blue-600" title="Voir les détails">
                                                        <i data-feather="eye" class="w-4 h-4"></i>
                                                    </button>
                                                    
                                                    <?php if ($isAdmin): ?>
                                                        <!-- Boutons Actions Admin -->
                                                        <div class="flex items-center space-x-1">
                                                            <button onclick="editTask(<?php echo $task['id']; ?>)" class="action-button text-green-600" title="Modifier">
                                                                <i data-feather="edit" class="w-4 h-4"></i>
                                                            </button>
                                                            <button onclick="deleteTask(<?php echo $task['id']; ?>)" class="action-button text-red-600" title="Supprimer">
                                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                                            </button>
                                                        </div>
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
            </div>
        </main>
    </div>

    <!-- Modal Détails Tâche -->
    <div id="task-details-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Détails de la tâche</h3>
                    <button onclick="closeTaskDetails()" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div id="modal-content" class="space-y-4">
                    <!-- Le contenu sera rempli par JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        // Fonction pour afficher les détails d'une tâche
        function showTaskDetails(task) {
            const modal = document.getElementById('task-details-modal');
            const title = document.getElementById('modal-title');
            const content = document.getElementById('modal-content');
            
            title.textContent = task.title;
            
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'in_progress': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            
            const statusLabels = {
                'pending': 'En attente',
                'in_progress': 'En cours',
                'completed': 'Terminée',
                'cancelled': 'Annulée'
            };
            
            const priorityColors = {
                'urgent': 'bg-red-100 text-red-800',
                'high': 'bg-orange-100 text-orange-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'low': 'bg-green-100 text-green-800'
            };
            
            const priorityLabels = {
                'urgent': 'Urgente',
                'high': 'Élevée',
                'medium': 'Moyenne',
                'low': 'Faible'
            };
            
            let assigneesHtml = '';
            if (task.assignees && task.assignees.length > 0) {
                assigneesHtml = task.assignees.map(assignee => 
                    `<div class="flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-600">${assignee.first_name.charAt(0).toUpperCase()}${assignee.last_name.charAt(0).toUpperCase()}</span>
                        </div>
                        <span class="text-sm text-gray-900">${assignee.first_name} ${assignee.last_name}</span>
                    </div>`
                ).join('');
            } else {
                assigneesHtml = '<p class="text-gray-500">Aucun assigné</p>';
            }
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">${task.description || 'Aucune description'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Projet</label>
                        <p class="mt-1 text-sm text-gray-900">${task.project_name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Créateur</label>
                        <p class="mt-1 text-sm text-gray-900">${task.creator_name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priorité</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${priorityColors[task.priority] || 'bg-gray-100 text-gray-800'}">
                            ${priorityLabels[task.priority] || task.priority}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusColors[task.status] || 'bg-gray-100 text-gray-800'}">
                            ${statusLabels[task.status] || task.status}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Échéance</label>
                        <p class="mt-1 text-sm text-gray-900">${task.due_date ? new Date(task.due_date).toLocaleDateString('fr-FR') : 'Non définie'}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assignés à</label>
                    <div class="mt-2 space-y-2">
                        ${assigneesHtml}
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            feather.replace();
        }
        
        // Fonction pour fermer le modal
        function closeTaskDetails() {
            document.getElementById('task-details-modal').classList.add('hidden');
        }
        
        
        // Fonction pour supprimer une tâche
        function deleteTask(taskId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_task">
                    <input type="hidden" name="task_id" value="${taskId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Fonction pour modifier une tâche
        function editTask(taskId) {
            window.location.href = 'index.php?page=edit_task&id=' + taskId;
        }
        
        
        // Filtres en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');
            
            // Auto-submit quand on change un filtre
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    form.submit();
                });
            });
        });
        
        // Initialiser Feather Icons
        feather.replace();
    </script>
</body>
</html>