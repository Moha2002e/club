// Fonctions pour la page de connexion/inscription

// Basculer vers le formulaire d'inscription
function switchToRegister() {
    document.getElementById('loginForm').classList.remove('active');
    document.getElementById('registerForm').classList.add('active');
}

// Basculer vers le formulaire de connexion
function switchToLogin() {
    document.getElementById('registerForm').classList.remove('active');
    document.getElementById('loginForm').classList.add('active');
}

// Validation du mot de passe en temps réel
function validatePassword() {
    const password = document.getElementById('password');
    if (!password) return;

    password.addEventListener('input', function() {
        const value = this.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        
        if (value.length === 0) {
            this.classList.remove('valid', 'invalid');
        } else if (regex.test(value)) {
            this.classList.remove('invalid');
            this.classList.add('valid');
        } else {
            this.classList.remove('valid');
            this.classList.add('invalid');
        }
    });
}

// Validation de la confirmation du mot de passe
function validateConfirmPassword() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (!password || !confirmPassword) return;

    confirmPassword.addEventListener('input', function() {
        const passwordValue = password.value;
        const confirmValue = this.value;
        
        if (confirmValue.length === 0) {
            this.classList.remove('valid', 'invalid');
        } else if (passwordValue === confirmValue && confirmValue.length > 0) {
            this.classList.remove('invalid');
            this.classList.add('valid');
        } else {
            this.classList.remove('valid');
            this.classList.add('invalid');
        }
    });
}

// Validation du formulaire d'inscription
function validateRegisterForm(form) {
    const password = form.querySelector('#password').value;
    const confirmPassword = form.querySelector('#confirm_password').value;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    // Vérifier le mot de passe
    if (!passwordRegex.test(password)) {
        showError('Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule et 1 chiffre.');
        return false;
    }

    // Vérifier la confirmation
    if (password !== confirmPassword) {
        showError('Les mots de passe ne correspondent pas.');
        return false;
    }

    return true;
}

// Validation du formulaire de connexion
function validateLoginForm(form) {
    const username = form.querySelector('#login_username').value.trim();
    const password = form.querySelector('#login_password').value;

    if (!username || !password) {
        showError('Veuillez remplir tous les champs.');
        return false;
    }

    return true;
}

// Afficher un message d'erreur
function showError(message) {
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const messageDiv = document.createElement('div');
    messageDiv.className = 'message error';
    messageDiv.textContent = message;

    const authForms = document.querySelector('.auth-forms');
    authForms.insertBefore(messageDiv, authForms.firstChild);

    // Retirer le message après 5 secondes
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Ajouter un état de chargement au bouton
function addLoadingState(button) {
    button.classList.add('loading');
    button.disabled = true;
}

// Retirer l'état de chargement du bouton
function removeLoadingState(button) {
    button.classList.remove('loading');
    button.disabled = false;
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Valider les mots de passe
    validatePassword();
    validateConfirmPassword();

    // Gérer la soumission du formulaire de connexion
    const loginForm = document.querySelector('#loginForm form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm(this)) {
                e.preventDefault();
                return false;
            }
            addLoadingState(this.querySelector('button[type="submit"]'));
        });
    }

    // Gérer la soumission du formulaire d'inscription
    const registerForm = document.querySelector('#registerForm form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegisterForm(this)) {
                e.preventDefault();
                return false;
            }
            addLoadingState(this.querySelector('button[type="submit"]'));
        });
    }

    // Retirer les messages flash après 5 secondes
    const flashMessage = document.querySelector('.message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 300);
        }, 5000);
    }
});

