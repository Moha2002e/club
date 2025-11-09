<?php 
require_once __DIR__ . '/../actions/profile.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="font-sans bg-secondary theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        
        <div class="flex-1 overflow-y-auto p-6 bg-secondary">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6 bg-primary rounded-xl shadow-lg border border-color p-6">
                    <h1 class="text-3xl font-bold text-primary">
                        👤 Mon Profil
                    </h1>
                    <p class="text-secondary mt-1">Gérez vos informations personnelles et votre sécurité</p>
                </div>

                <!-- Messages flash -->
                <?php if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo $flashMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Carte Profil -->
                <div class="bg-primary rounded-xl shadow-lg border border-color mb-6">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            📝 Informations du profil
                        </h3>
                        <p class="text-secondary mt-1">Modifiez vos informations personnelles</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-6 mb-8">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <?php 
                                    $firstName = $user['first_name'] ?? '';
                                    $lastName = $user['last_name'] ?? '';
                                    echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1)); 
                                ?>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-primary">
                                    <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                                </h4>
                                <p class="text-secondary"><?php echo ucfirst($user['role'] ?? 'student'); ?></p>
                                <p class="text-sm text-secondary mt-1">
                                    Membre depuis <?php echo date('Y', strtotime($user['created_at'])); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Formulaire d'édition -->
                        <form method="POST" action="index.php?page=profile" class="space-y-6">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Prénom -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-secondary mb-2">
                                        Prénom
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($user['first_name']); ?>"
                                               class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <i data-feather="user" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-secondary mb-2">
                                        Nom
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($user['last_name']); ?>"
                                               class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <i data-feather="user" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-secondary mb-2">
                                    Email
                                </label>
                                <div class="relative">
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>"
                                           class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <i data-feather="mail" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Bouton sauvegarder -->
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg flex items-center space-x-2">
                                    <i data-feather="save" class="w-5 h-5"></i>
                                    <span>Enregistrer les modifications</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Carte Sécurité -->
                <div class="bg-primary rounded-xl shadow-lg border border-color mb-6">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            🔒 Sécurité
                        </h3>
                        <p class="text-secondary mt-1">Modifiez votre mot de passe</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="index.php?page=profile" class="space-y-6">
                            <input type="hidden" name="action" value="update_password">
                            
                            <!-- Mot de passe actuel -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-secondary mb-2">
                                    Mot de passe actuel
                                </label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password"
                                           class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <i data-feather="lock" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nouveau mot de passe -->
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-secondary mb-2">
                                        Nouveau mot de passe
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="new_password" name="new_password"
                                               class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <i data-feather="lock" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                    </div>
                                    <p class="text-xs text-secondary mt-1">Minimum 8 caractères</p>
                                </div>

                                <!-- Confirmer le nouveau mot de passe -->
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-secondary mb-2">
                                        Confirmer le nouveau mot de passe
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="confirm_password" name="confirm_password"
                                               class="w-full pl-10 pr-4 py-3 border border-color bg-secondary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <i data-feather="lock" class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton changer le mot de passe -->
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md hover:shadow-lg flex items-center space-x-2">
                                    <i data-feather="shield" class="w-5 h-5"></i>
                                    <span>Modifier le mot de passe</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Carte Statistiques -->
                <div class="bg-primary rounded-xl shadow-lg border border-color">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            📊 Mes statistiques
                        </h3>
                        <p class="text-secondary mt-1">Vue d'ensemble de votre activité</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-feather="folder" class="w-6 h-6 text-white"></i>
                                </div>
                                <p class="text-4xl font-bold text-white"><?php echo $projectsCount; ?></p>
                                <p class="text-sm text-blue-100 mt-1">📁 Projets</p>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-feather="check-circle" class="w-6 h-6 text-white"></i>
                                </div>
                                <p class="text-4xl font-bold text-white"><?php echo $completedTasks; ?></p>
                                <p class="text-sm text-green-100 mt-1">✅ Tâches terminées</p>
                            </div>
                            <div class="text-center p-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-feather="calendar" class="w-6 h-6 text-white"></i>
                                </div>
                                <p class="text-4xl font-bold text-white"><?php echo $userEvents; ?></p>
                                <p class="text-sm text-purple-100 mt-1">📅 Événements</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        // Initialiser Feather Icons
        feather.replace();

        // Validation côté client pour le mot de passe
        const passwordForm = document.querySelector('form[action*="update_password"]');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('new_password');
                const confirmPassword = document.getElementById('confirm_password');
                
                if (!newPassword || !confirmPassword) {
                    return;
                }
                
                if (newPassword.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return false;
                }
                
                if (newPassword.value.length < 8) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères.');
                    return false;
                }
            });
        }
    </script>
</body>
</html>