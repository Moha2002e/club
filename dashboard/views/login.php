<?php
// Si l'utilisateur est déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

// Traiter l'authentification seulement si c'est une soumission de formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once __DIR__ . '/../actions/auth.php';
}

// Récupérer les messages flash
$message = '';
$messageType = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../../images/logo.png">
    <link rel="apple-touch-icon" href="../../images/logo.png">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="views/assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-forms">
            <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <div class="form-container active" id="loginForm">
                <div class="form-logo">
                    <img src="../images/logo.png" alt="Tech Lab Logo">
                    <span>Tech Lab</span>
                </div>
                <h2 class="form-title">Connexion</h2>
                <p class="form-subtitle">Accédez à votre espace HEPL Tech Lab</p>
                
                <form method="POST" action="index.php?page=login">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="login_username">Nom d'utilisateur ou Email</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="login_username" name="login_username" required autocomplete="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="login_password" name="login_password" required autocomplete="current-password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>
                
                <div class="switch-form">
                    <p>Pas encore de compte ? <a href="#" onclick="switchToRegister(); return false;">S'inscrire</a></p>
                </div>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="form-container" id="registerForm">
                <div class="form-logo">
                    <img src="../images/logo.png" alt="Tech Lab Logo">
                    <span>Tech Lab</span>
                </div>
                <h2 class="form-title">Inscription</h2>
                <p class="form-subtitle">Rejoignez la communauté HEPL Tech Lab</p>
                
                <form method="POST" action="index.php?page=login">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="first_name" name="first_name" required autocomplete="given-name">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="last_name" name="last_name" required autocomplete="family-name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                            <input type="text" id="username" name="username" required autocomplete="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" required autocomplete="email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required autocomplete="new-password">
                        </div>
                        <small class="password-hint">Min. 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </button>
                </form>
                
                <div class="switch-form">
                    <p>Déjà un compte ? <a href="#" onclick="switchToLogin(); return false;">Se connecter</a></p>
                </div>
            </div>
            
            <!-- Section Activation -->
            <?php if (isset($_GET['show_activation']) && isset($_SESSION['activation_user_id'])): ?>
            <div class="auth-form" id="activation-form" style="display: block;">
                <h1>Activation du compte</h1>
                <p class="form-subtitle">Un email d'activation a été envoyé</p>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="simple-message">
                    <p>Veuillez vérifier votre email <strong><?php echo $_SESSION['activation_email']; ?></strong> et cliquer sur le lien d'activation pour activer votre compte.</p>
                </div>
                
            <div style="text-align: center; margin: 20px 0;">
                <button onclick="sendActivationEmail()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    Envoyer l'email d'activation
                </button>
            </div>
                
                <div class="switch-form">
                    <p><a href="index.php?page=login">Retour à la connexion</a></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- EmailJS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    
    <script src="views/assets/js/login.js"></script>

    <script>
        // Initialiser EmailJS
        (function() {
            emailjs.init("6WNepAHVTUsPccRF3");
            console.log('EmailJS initialisé');
        })();
        
        // Vérifier si EmailJS est disponible
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé');
            console.log('EmailJS disponible:', typeof emailjs !== 'undefined');
        });
        
        // Variables d'activation
        const activationData = {
            userId: '<?php echo $_SESSION['activation_user_id'] ?? ''; ?>',
            token: '<?php echo $_SESSION['activation_token'] ?? ''; ?>',
            email: '<?php echo $_SESSION['activation_email'] ?? ''; ?>',
            firstName: '<?php echo $_SESSION['activation_first_name'] ?? ''; ?>'
        };
        
        // Debug: Afficher les données d'activation
        console.log('Données d\'activation:', activationData);
        console.log('Page chargée, données disponibles:', activationData.email ? 'OUI' : 'NON');
        
        // Fonction pour envoyer l'email d'activation
        function sendActivationEmail() {
            if (!activationData.token) {
                console.log('Données d\'activation manquantes');
                return;
            }
            
            // Vérifier que EmailJS est initialisé
            if (typeof emailjs === 'undefined') {
                console.error('EmailJS n\'est pas initialisé');
                return;
            }
            
            const activationLink = `${window.location.origin}/dashboard/index.php?page=activate&token=${activationData.token}`;
            
            const templateParams = {
                to_email: activationData.email,
                to_name: activationData.firstName,
                activation_link: activationLink,
                site_name: 'HEPL Tech Lab'
            };
            
            // Debug: Afficher les paramètres envoyés
            console.log('Paramètres EmailJS:', templateParams);
            console.log('Lien d\'activation:', activationLink);
            console.log('Service ID:', 'service_dobtwbv');
            console.log('Template ID:', 'template_pumi06p');
            
            // Vérifier que tous les paramètres requis sont présents
            if (!templateParams.to_email || !templateParams.to_name || !templateParams.activation_link) {
                console.error('Paramètres manquants pour l\'envoi d\'email');
                return;
            }
            
            emailjs.send('service_dobtwbv', 'template_pumi06p', templateParams)
                .then(function(response) {
                    console.log('Email d\'activation envoyé avec succès !');
                    console.log('Réponse EmailJS:', response);
                    console.log('Status:', response.status);
                    console.log('Text:', response.text);
                }, function(error) {
                    console.error('Erreur envoi email:', error);
                    console.error('Status:', error.status);
                    console.error('Text:', error.text);
                    console.error('Details:', error);
                    
                    // Afficher un message d'erreur plus détaillé
                    if (error.status === 412) {
                        console.error('Erreur 412: Vérifiez la configuration EmailJS (Service ID, Template ID, paramètres)');
                    } else if (error.status === 400) {
                        console.error('Erreur 400: Paramètres invalides');
                    } else if (error.status === 401) {
                        console.error('Erreur 401: Clé publique invalide');
                    }
                });
        }
        
        
        // Envoyer automatiquement l'email d'activation si on est sur la page d'activation
        <?php if (isset($_GET['show_activation']) && isset($_SESSION['activation_user_id'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Envoyer l'email automatiquement
            setTimeout(sendActivationEmail, 1000);
            
            // Nettoyer les données de session après l'envoi
            setTimeout(function() {
                // Faire une requête pour nettoyer la session
                fetch('index.php?page=login&clean_activation=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clean_activation'
                });
            }, 2000);
        });
        <?php endif; ?>
    </script>
</body>
</html>
