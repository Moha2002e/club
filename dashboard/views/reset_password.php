<?php
// Page de réinitialisation de mot de passe
// Pas besoin d'authentification pour cette page

require_once __DIR__ . '/../DAO/UserDAO.php';

$token = $_GET['token'] ?? '';
$userDAO = new UserDAO();

// Vérifier si le token est valide
$validationResult = $userDAO->validateResetToken($token);
$isValidToken = $validationResult['success'];
$userData = $validationResult['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <link rel="stylesheet" href="views/assets/css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Container Principal -->
    <div class="auth-container">
        <div class="auth-forms" style="width: 100%;">
            <!-- Logo et Titre -->
            <div class="form-logo">
                <img src="../images/logo.png" alt="Tech Lab Logo">
                <span>Tech Lab</span>
            </div>
            <h2 class="form-title">Nouveau mot de passe</h2>
            <?php if ($isValidToken && $userData): ?>
                <p class="form-subtitle">Bonjour <?php echo htmlspecialchars($userData['first_name']); ?>, créez votre nouveau mot de passe</p>
            <?php else: ?>
                <p class="form-subtitle" style="color: #ef4444;">Lien invalide ou expiré</p>
            <?php endif; ?>

            <!-- Formulaire -->
            <?php 
            // Afficher les messages flash
            if (isset($_SESSION['flash_message'])): 
                $flashMessage = $_SESSION['flash_message'];
                $flashType = $_SESSION['flash_type'] ?? 'info';
                unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            ?>
                <div class="message <?php echo $flashType; ?>">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($isValidToken): ?>
                <form id="resetPasswordForm" action="actions/reset_password.php" method="POST">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <!-- Nouveau mot de passe -->
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                required 
                                minlength="8"
                                placeholder="Minimum 8 caractères"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password')"
                                class="password-toggle"
                            >
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <small style="color: #a3a3a3; font-size: 0.75rem;">Min. 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre</small>
                    </div>

                    <!-- Confirmer le mot de passe -->
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                name="confirm_password" 
                                id="confirm_password"
                                required 
                                minlength="8"
                                placeholder="Confirmez votre mot de passe"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('confirm_password')"
                                class="password-toggle"
                            >
                                <i class="fas fa-eye" id="confirm_password-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Message d'erreur -->
                    <div id="error-message" class="message error" style="display: none;">
                        <span id="error-text"></span>
                    </div>

                    <!-- Bouton Submit -->
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        <span>Réinitialiser le mot de passe</span>
                    </button>
                </form>
            <?php else: ?>
                <!-- Message d'erreur si le token est invalide -->
                <div style="text-align: center;">
                    <div class="message error">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; display: block; margin-bottom: 15px;"></i>
                        <p style="font-weight: 600; margin-bottom: 10px;">Lien invalide ou expiré</p>
                        <p style="font-size: 0.875rem;">Ce lien de réinitialisation n'est pas valide ou a expiré. Les liens sont valables 1 heure.</p>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="index.php?page=forgot_password" style="color: #3B82F6; text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Demander un nouveau lien
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lien de retour -->
            <div class="switch-form">
                <p><a href="index.php?page=login">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a></p>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour afficher/masquer le mot de passe
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validation du formulaire
        document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            
            // Vérifier que les mots de passe correspondent
            if (password !== confirmPassword) {
                e.preventDefault();
                errorText.textContent = 'Les mots de passe ne correspondent pas.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Vérifier la complexité du mot de passe
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(password)) {
                e.preventDefault();
                errorText.textContent = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Désactiver le bouton
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Réinitialisation...</span>';
        });

        // Masquer le message d'erreur lors de la saisie
        document.getElementById('password')?.addEventListener('input', function() {
            document.getElementById('error-message').style.display = 'none';
        });
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            document.getElementById('error-message').style.display = 'none';
        });
    </script>

    <style>
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a3a3a3;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 5px;
        }
        
        .password-toggle:hover {
            color: #ffffff;
        }
        
        .input-icon {
            position: relative;
        }
    </style>
</body>
</html>
