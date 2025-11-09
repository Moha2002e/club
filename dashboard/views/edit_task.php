<?php 
require_once __DIR__ . '/../actions/edit_task.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la tâche - HEPL Tech Lab</title>
    
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
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Bouton retour -->
                        <a href="index.php?page=tasks" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <i data-feather="arrow-left" class="w-5 h-5 mr-2"></i>
                            Retour aux tâches
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Modifier la tâche</h1>
                            <p class="text-gray-600"><?php echo htmlspecialchars($task['title']); ?></p>
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

                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Formulaire de modification -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informations de la tâche</h2>
                                
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_task">
                                    
                                    <div class="space-y-6">
                                        <!-- Titre -->
                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        </div>
                                        
                                        <!-- Description -->
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                            <textarea id="description" name="description" rows="4" 
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><?php echo htmlspecialchars($task['description']); ?></textarea>
                                        </div>
                                        
                                        <!-- Priorité et Statut -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priorité *</label>
                                                <select id="priority" name="priority" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Faible</option>
                                                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Moyenne</option>
                                                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>Élevée</option>
                                                    <option value="urgent" <?php echo $task['priority'] === 'urgent' ? 'selected' : ''; ?>>Urgente</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                                                <select id="status" name="status" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                                    <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>En cours</option>
                                                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Terminée</option>
                                                    <option value="cancelled" <?php echo $task['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Date d'échéance -->
                                        <div>
                                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance</label>
                                            <input type="date" id="due_date" name="due_date" value="<?php echo $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : ''; ?>" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        
                                        <!-- Boutons -->
                                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                                            <a href="index.php?page=tasks" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                                                Annuler
                                            </a>
                                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                                <i data-feather="save" class="w-4 h-4"></i>
                                                <span>Enregistrer</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Informations de la tâche -->
                        <div class="space-y-6">
                            <!-- Détails de la tâche -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Détails</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Projet :</span>
                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($task['project_name']); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Créateur :</span>
                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($task['creator_name']); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Créée le :</span>
                                        <p class="text-sm text-gray-900"><?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assignés -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignés</h3>
                                <?php if (!empty($task['assignees'])): ?>
                                    <div class="space-y-2">
                                        <?php foreach ($task['assignees'] as $assignee): ?>
                                            <div class="flex items-center space-x-3">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-600">
                                                        <?php echo strtoupper(substr($assignee['first_name'], 0, 1) . substr($assignee['last_name'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <span class="text-sm text-gray-900">
                                                    <?php echo htmlspecialchars($assignee['first_name'] . ' ' . $assignee['last_name']); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500">Aucun assigné</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>

