<?php 
require_once __DIR__ . '/../actions/project_edit.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le projet - HEPL Tech Lab</title>
    
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
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <a href="index.php?page=project_detail&id=<?php echo $project_id; ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                    Retour au projet
                </a>
            </div>

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

            <!-- Formulaire de modification -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Modifier le projet</h1>
                    <p class="text-gray-600">Modifiez les informations du projet</p>
                </div>

                <form method="POST" action="" class="space-y-6">
                    <input type="hidden" name="action" value="update_project">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                    <!-- Nom du projet -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du projet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                            value="<?php echo htmlspecialchars($project['title']); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo htmlspecialchars($project['description']); ?></textarea>
                    </div>

                    <!-- Date de début -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de début
                        </label>
                        <input type="date" id="start_date" name="start_date"
                            value="<?php echo $project['start_date'] ?? ''; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date de fin -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de fin
                        </label>
                        <input type="date" id="due_date" name="due_date"
                            value="<?php echo $project['due_date'] ?? ''; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="planning" <?php echo $project['status'] === 'planning' ? 'selected' : ''; ?>>En planification</option>
                            <option value="active" <?php echo $project['status'] === 'active' ? 'selected' : ''; ?>>Actif</option>
                            <option value="on_hold" <?php echo $project['status'] === 'on_hold' ? 'selected' : ''; ?>>En pause</option>
                            <option value="completed" <?php echo $project['status'] === 'completed' ? 'selected' : ''; ?>>Terminé</option>
                            <option value="cancelled" <?php echo $project['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                        </select>
                    </div>

                    <!-- Visibilité -->
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                            Visibilité <span class="text-red-500">*</span>
                        </label>
                        <select id="visibility" name="visibility" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="public" <?php echo $project['visibility'] === 'public' ? 'selected' : ''; ?>>Public (visible par tous les membres)</option>
                            <option value="private" <?php echo $project['visibility'] === 'private' ? 'selected' : ''; ?>>Privé (visible uniquement par les membres du projet)</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="index.php?page=project_detail&id=<?php echo $project_id; ?>" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                            <i data-feather="check" class="w-4 h-4"></i>
                            <span>Enregistrer les modifications</span>
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

