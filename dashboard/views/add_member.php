<?php 
require_once __DIR__ . '/../actions/add_member.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un membre - HEPL Tech Lab</title>
    
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
                <h1 class="text-2xl font-bold text-gray-800">Ajouter un membre</h1>
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
                    <input type="hidden" name="action" value="add_member">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                    <div class="mb-6">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sélectionner un utilisateur <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Choisir un utilisateur --</option>
                            <?php foreach ($availableUsers as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="member">Membre</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="index.php?page=project_detail&id=<?php echo $project_id; ?>" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                            Ajouter le membre
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

