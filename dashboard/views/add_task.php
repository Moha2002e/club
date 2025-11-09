<?php 
require_once __DIR__ . '/../actions/add_task.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une tâche - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 font-sans theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        
        <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <a href="index.php?page=project_detail&id=<?php echo $project_id; ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                    Retour au projet
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Ajouter une tâche</h1>
                <p class="text-gray-600">Projet : <?php echo htmlspecialchars($project['title']); ?></p>
            </div>

            <!-- Messages flash -->
            <?php if (isset($_SESSION['flash_message'])): 
                $flashMessage = $_SESSION['flash_message'];
                $flashType = $_SESSION['flash_type'] ?? 'info';
                unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_task">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                    <!-- Titre -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Titre de la tâche <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: Créer la maquette du design">
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Décrivez les détails de la tâche..."></textarea>
                    </div>

                    <!-- Priorité -->
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priorité <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low">Basse</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="high">Haute</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>

                    <!-- Statut -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending" selected>En attente</option>
                            <option value="in_progress">En cours</option>
                            <option value="completed">Terminée</option>
                            <option value="cancelled">Annulée</option>
                        </select>
                    </div>

                    <!-- Date d'échéance -->
                    <div class="mb-4">
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date d'échéance
                        </label>
                        <input type="date" name="due_date" id="due_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Assigner à (multi-sélection) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assigner à (sélection multiple)
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                            <?php if (empty($projectMembers)): ?>
                                <p class="text-gray-500 text-sm">Aucun membre dans ce projet</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php foreach ($projectMembers as $member): ?>
                                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <input type="checkbox" name="assigned_users[]" value="<?php echo $member['user_id']; ?>"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                    <?php echo strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">
                                                        <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                                    </p>
                                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['email']); ?></p>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i data-feather="info" class="w-3 h-3 inline"></i>
                            Vous pouvez assigner la tâche à plusieurs membres
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="index.php?page=project_detail&id=<?php echo $project_id; ?>" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2">
                            <i data-feather="plus" class="w-4 h-4"></i>
                            Créer la tâche
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>

