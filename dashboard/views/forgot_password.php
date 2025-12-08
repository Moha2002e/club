<?php
// Page de demande de réinitialisation de mot de passe
// Pas besoin d'authentification pour cette page
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <link rel="stylesheet" href="views/assets/css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
        (function(){
            emailjs.init("8gWxlHHGZgfE-x7fq"); // Votre clé publique EmailJS
        })();
    </script>
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
            <h2 class="form-title">Mot de passe oublié ?</h2>
            <p class="form-subtitle">Entrez votre email pour recevoir un lien de réinitialisation</p>

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

            <form id="forgotPasswordForm" method="POST">
                <input type="hidden" name="action" value="request_reset">
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            required 
                            placeholder="votre.email@example.com"
                        >
                    </div>
                </div>

                <!-- Bouton Submit -->
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    <span>Envoyer le lien de réinitialisation</span>
                </button>
            </form>

            <!-- Lien de retour -->
            <div class="switch-form">
                <p><a href="index.php?page=login">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a></p>
            </div>
        </div>
    </div>

    <script>
        // Gestion du formulaire avec EmailJS
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalContent = submitBtn.innerHTML;
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Envoi en cours...</span>';
            
            const formData = new FormData(this);
            
            try {
                // Envoyer la requête au serveur pour générer le token
                const response = await fetch('actions/forgot_password.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Envoyer l'email via EmailJS
                    const templateParams = {
                        to_email: result.email,
                        to_name: result.first_name || 'Utilisateur',
                        reset_link: result.reset_link,
                        from_name: 'HEPL Tech Lab'
                    };
                    
                    emailjs.send('service_3n1a5tn', 'template_ibr8dik', templateParams)
                        .then(function(emailResponse) {
                            console.log('Email envoyé avec succès!', emailResponse.status, emailResponse.text);
                            // Rediriger avec message de succès
                            window.location.href = 'index.php?page=forgot_password&success=1';
                        }, function(error) {
                            console.error('Erreur lors de l\'envoi de l\'email:', error);
                            alert('Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                        });
                } else {
                    alert(result.message || 'Erreur lors de la demande de réinitialisation.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            }
        });

        // Vérifier si on revient après succès
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            // Afficher un message de succès
            const form = document.querySelector('.auth-forms');
            form.insertAdjacentHTML('afterbegin', `
                <div class="message success" style="margin-bottom: 20px;">
                    Email envoyé avec succès ! Vérifiez votre boîte de réception.
                </div>
            `);
        }
    </script>
</body>
</html>
