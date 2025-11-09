<?php
require_once __DIR__ . '/../actions/settings.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - HEPL Tech Lab</title>
    
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
    <style>
        /* Thèmes dynamiques */
        .theme-light { --bg-primary: #ffffff; --bg-secondary: #f8fafc; --text-primary: #1f2937; --text-secondary: #6b7280; }
        .theme-dark { --bg-primary: #1f2937; --bg-secondary: #111827; --text-primary: #f9fafb; --text-secondary: #d1d5db; }
        .theme-blue { --bg-primary: #eff6ff; --bg-secondary: #dbeafe; --text-primary: #1e40af; --text-secondary: #3b82f6; }
        .theme-green { --bg-primary: #f0fdf4; --bg-secondary: #dcfce7; --text-primary: #166534; --text-secondary: #16a34a; }
        
        body { background-color: var(--bg-secondary); color: var(--text-primary); }
        .bg-white { background-color: var(--bg-primary) !important; }
        .text-gray-800 { color: var(--text-primary) !important; }
        .text-gray-600 { color: var(--text-secondary) !important; }
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
                            <h2 class="text-3xl font-bold text-primary">
                                ⚙️ Paramètres
                            </h2>
                            <p class="text-secondary mt-1">Configuration et personnalisation</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenu -->
            <div class="flex-1 overflow-auto p-6">
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

                <!-- Paramètres du thème -->
                <div class="bg-primary rounded-xl shadow-lg border border-color mb-6">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            🎨 Préférences d'affichage
                        </h3>
                        <p class="text-secondary mt-1">Personnalisez l'apparence de votre interface</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_theme">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Thème</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="theme" value="light" <?php echo $currentTheme === 'light' ? 'checked' : ''; ?> class="sr-only">
                                        <div class="p-4 border-2 rounded-lg transition-all <?php echo $currentTheme === 'light' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'; ?>">
                                            <div class="w-full h-8 bg-white border border-gray-200 rounded mb-2"></div>
                                            <p class="text-sm font-medium text-gray-700">Clair</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="theme" value="dark" <?php echo $currentTheme === 'dark' ? 'checked' : ''; ?> class="sr-only">
                                        <div class="p-4 border-2 rounded-lg transition-all <?php echo $currentTheme === 'dark' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'; ?>">
                                            <div class="w-full h-8 bg-gray-800 border border-gray-600 rounded mb-2"></div>
                                            <p class="text-sm font-medium text-gray-700">Sombre</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                <i data-feather="check" class="w-4 h-4"></i>
                                Appliquer le thème
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        // Initialiser Feather Icons
        feather.replace();
        
        // Mise à jour automatique du thème
        document.querySelectorAll('input[name="theme"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Mettre à jour la classe du body
                document.body.className = document.body.className.replace(/theme-\w+/, 'theme-' + this.value);
            });
        });
    </script>
</body>
</html>

