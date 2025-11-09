<?php 
require_once __DIR__ . '/../actions/edit_member.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un membre - HEPL Tech Lab</title>
    
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
                <a href="index.php?page=members" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                    Retour aux membres
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Modifier un membre</h1>
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
                    <input type="hidden" name="action" value="update_member">
                    <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">

                    <!-- Prénom -->
                    <div class="mb-4">
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" required
                            value="<?php echo htmlspecialchars($member['first_name']); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Nom -->
                    <div class="mb-4">
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" required
                            value="<?php echo htmlspecialchars($member['last_name']); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            value="<?php echo htmlspecialchars($member['email']); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Username (lecture seule) -->
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username
                        </label>
                        <input type="text" id="username" readonly
                            value="<?php echo htmlspecialchars($member['username']); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Le nom d'utilisateur ne peut pas être modifié</p>
                    </div>

                    <!-- Rôle -->
                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="student" <?php echo $member['role'] === 'student' ? 'selected' : ''; ?>>Étudiant</option>
                            <option value="admin" <?php echo $member['role'] === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="index.php?page=members" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <i data-feather="check" class="w-4 h-4"></i>
                            Enregistrer
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

