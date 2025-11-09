<?php 
require_once __DIR__ . '/../actions/activate.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation du compte - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans theme-<?php echo $currentTheme; ?> flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 space-y-6 border border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <?php if ($activationResult['success']): ?>
                <i data-feather="check-circle" class="w-16 h-16 text-green-600 dark:text-green-400 mx-auto mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Compte activé !</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Votre compte a été activé avec succès.</p>
            <?php else: ?>
                <i data-feather="x-circle" class="w-16 h-16 text-red-600 dark:text-red-400 mx-auto mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Erreur d'activation</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2"><?php echo htmlspecialchars($activationResult['message']); ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                <?php if ($activationResult['success']): ?>
                    Vous pouvez maintenant vous connecter à votre compte.
                <?php else: ?>
                    Veuillez vérifier le lien d'activation ou demander un nouveau lien.
                <?php endif; ?>
            </p>
        </div>

        <div class="text-center">
            <a href="index.php?page=login" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i data-feather="log-in" class="w-4 h-4 mr-2"></i>
                Se connecter
            </a>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>

