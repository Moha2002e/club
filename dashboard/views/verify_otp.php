<?php 
require_once __DIR__ . '/../actions/verify_otp.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du compte - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 font-sans theme-<?php echo $currentTheme; ?>">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-blue-600 rounded-full flex items-center justify-center">
                    <i data-feather="shield-check" class="h-6 w-6 text-white"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                    Vérification du compte
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Entrez le code de vérification à 6 chiffres envoyé à votre email
                </p>
            </div>

            <!-- Messages flash -->
            <?php if (isset($_SESSION['flash_message'])): 
                $flashMessage = $_SESSION['flash_message'];
                $flashType = $_SESSION['flash_type'] ?? 'info';
                unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            ?>
                <div class="p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                    <?php echo $flashMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire OTP -->
            <form class="mt-8 space-y-6" method="POST" action="index.php?page=verify_otp" id="otp-form">
                <input type="hidden" name="action" value="verify_otp">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="otp_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code de vérification
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="otp_code" 
                                   name="otp_code" 
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   placeholder="123456"
                                   class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <i data-feather="key" class="absolute right-3 top-3.5 w-5 h-5 text-gray-400"></i>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Code à 6 chiffres reçu par email
                        </p>
                    </div>
                </div>

                <div class="flex flex-col space-y-3">
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i data-feather="check-circle" class="h-5 w-5 text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Vérifier le code
                    </button>

                    <button type="button" 
                            onclick="resendOTP()"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i data-feather="refresh-cw" class="h-5 w-5 text-gray-400 group-hover:text-gray-500"></i>
                        </span>
                        Renvoyer le code
                    </button>
                </div>

                <div class="text-center">
                    <a href="index.php?page=login" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        Retour à la connexion
                    </a>
                </div>
            </form>

            <!-- Affichage du code OTP pour le développement -->
            <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
                <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                        Mode développement - Code OTP
                    </h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Code OTP: <span class="font-mono font-bold"><?php echo htmlspecialchars($_GET['otp'] ?? 'N/A'); ?></span>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Initialiser Feather Icons
        feather.replace();

        // Auto-focus sur le champ OTP
        document.getElementById('otp_code').focus();

        // Validation en temps réel
        document.getElementById('otp_code').addEventListener('input', function(e) {
            // Ne garder que les chiffres
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit quand 6 chiffres sont entrés
            if (this.value.length === 6) {
                setTimeout(() => {
                    document.getElementById('otp-form').submit();
                }, 500);
            }
        });

        // Fonction pour renvoyer l'OTP
        function resendOTP() {
            const userId = document.querySelector('input[name="user_id"]').value;
            if (!userId) {
                alert('ID utilisateur manquant');
                return;
            }

            fetch('index.php?page=verify_otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=resend_otp&user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Nouveau code OTP envoyé !');
                    // Optionnel: afficher le nouveau code en mode debug
                    if (data.otp_code) {
                        console.log('Nouveau code OTP:', data.otp_code);
                    }
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi du code');
            });
        }
    </script>
</body>
</html>

