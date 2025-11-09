// Variables globales
let sidebarCollapsed = false;
let isMobile = false;
const sidebar = document.getElementById('sidebar');
const toggleButton = document.getElementById('toggle-sidebar');
const mobileMenuButton = document.getElementById('mobile-menu-button');
const sidebarOverlay = document.getElementById('sidebar-overlay');
const logoText = document.getElementById('logo-text');
const navTexts = document.querySelectorAll('.nav-text');

// Fonction pour détecter si on est sur mobile
function checkIfMobile() {
    isMobile = window.innerWidth < 1024; // lg breakpoint de Tailwind
    return isMobile;
}

// Fonction pour basculer la sidebar
function toggleSidebar() {
    if (isMobile) {
        // Comportement mobile : sidebar en overlay
        toggleMobileSidebar();
    } else {
        // Comportement desktop : sidebar réduite/étendue
        toggleDesktopSidebar();
    }
}

// Fonction pour gérer la sidebar sur mobile
function toggleMobileSidebar() {
    const isHidden = sidebar.classList.contains('-translate-x-full');
    
    if (isHidden) {
        // Afficher la sidebar
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('fixed', 'top-0', 'left-0', 'h-full');
        sidebarOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Empêcher le scroll
    } else {
        // Masquer la sidebar
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restaurer le scroll
    }
}

// Fonction pour gérer la sidebar sur desktop
function toggleDesktopSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
        // Réduire la sidebar
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-24');
        
        // Masquer le texte du logo
        logoText.style.display = 'none';
        
        // Masquer tous les textes de navigation
        navTexts.forEach(text => {
            text.style.display = 'none';
        });
        
        // Changer l'icône du bouton
        toggleButton.innerHTML = '<i data-feather="chevron-right" class="w-5 h-5 text-gray-600"></i>';
        
    } else {
        // Étendre la sidebar
    sidebar.classList.remove('w-24');
        sidebar.classList.add('w-64');
        
        // Afficher le texte du logo
        logoText.style.display = 'block';
        
        // Afficher tous les textes de navigation
        navTexts.forEach(text => {
            text.style.display = 'block';
        });
        
        // Changer l'icône du bouton
        toggleButton.innerHTML = '<i data-feather="menu" class="w-5 h-5 text-gray-600"></i>';
    }
    
    // Re-initialiser les icônes Feather
    feather.replace();
}

// Fonction pour fermer la sidebar mobile
function closeMobileSidebar() {
    if (isMobile) {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Fonction pour initialiser la sidebar selon la taille d'écran
function initializeSidebar() {
    checkIfMobile();
    
    if (isMobile) {
        // Configuration mobile
    sidebar.classList.add('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
    sidebar.classList.remove('w-24');
        sidebar.classList.add('w-64');
        
        // S'assurer que les textes sont visibles sur mobile
        logoText.style.display = 'block';
        navTexts.forEach(text => {
            text.style.display = 'block';
        });
        
        sidebarCollapsed = false;
    } else {
        // Configuration desktop
        sidebar.classList.remove('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Restaurer l'état de la sidebar desktop si elle était réduite
        if (sidebarCollapsed) {
            toggleDesktopSidebar();
        }
    }
}

// Gestion des événements
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la sidebar au chargement
    initializeSidebar();
    
    // Événement pour le bouton de basculement dans la sidebar
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleSidebar);
    }
    
    // Événement pour le bouton menu mobile dans le header
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            if (isMobile) {
                toggleMobileSidebar();
            }
        });
    }
    
    // Événement pour fermer la sidebar mobile en cliquant sur l'overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeMobileSidebar);
    }
    
    // Fermer la sidebar mobile lors du clic sur un lien de navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function() {
            if (isMobile) {
                closeMobileSidebar();
            }
        });
    });
    
    // Gestion du responsive - réinitialiser la sidebar lors du redimensionnement
    function handleResize() {
        const wasMobile = isMobile;
        checkIfMobile();
        
        // Si on change de mobile à desktop ou vice versa
        if (wasMobile !== isMobile) {
            initializeSidebar();
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    // Gestion de la touche Escape pour fermer la sidebar mobile
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile) {
            closeMobileSidebar();
        }
    });
    
    // Initialiser les gestionnaires pour les boutons d'action des projets
    initializeProjectActions();
});

// Fonction pour initialiser les actions des projets
function initializeProjectActions() {
    // Gestionnaires pour les boutons d'action des projets
    document.addEventListener('click', function(e) {
        const button = e.target.closest('button');
        if (!button) return;
        
        const title = button.getAttribute('title');
        
        if (title === 'Terminer') {
            handleCompleteProject(button);
        } else if (title === 'Modifier') {
            handleEditProject(button);
        } else if (title === 'Supprimer') {
            handleDeleteProject(button);
        }
    });
}

// Fonctions pour gérer les actions des projets
function handleCompleteProject(button) {
    const row = button.closest('tr');
    if (!row) return;
    
    const projectName = row.querySelector('td:first-child p:first-child')?.textContent;
    if (!projectName) return;
    
    if (confirm(`Êtes-vous sûr de vouloir marquer le projet "${projectName}" comme terminé ?`)) {
        // Mettre à jour le statut
        const statusBadge = row.querySelector('.bg-green-100, .bg-yellow-100, .bg-blue-100');
        if (statusBadge) {
            statusBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
            statusBadge.textContent = 'Terminé';
        }
        
        // Mettre à jour la barre de progression
        const progressBar = row.querySelector('.bg-green-500, .bg-yellow-500, .bg-red-500');
        if (progressBar) {
            progressBar.style.width = '100%';
            progressBar.className = 'bg-green-500 h-2 rounded-full';
            const progressText = progressBar.parentElement?.nextElementSibling;
            if (progressText) {
                progressText.textContent = '100%';
            }
        }
        
        showNotification('Projet marqué comme terminé !', 'success');
    }
}

function handleEditProject(button) {
    const row = button.closest('tr');
    if (!row) return;
    
    const projectName = row.querySelector('td:first-child p:first-child')?.textContent;
    if (!projectName) return;
    
    showNotification(`Modification du projet "${projectName}" - Fonctionnalité à implémenter`, 'info');
}

function handleDeleteProject(button) {
    const row = button.closest('tr');
    if (!row) return;
    
    const projectName = row.querySelector('td:first-child p:first-child')?.textContent;
    if (!projectName) return;
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer le projet "${projectName}" ?`)) {
        row.remove();
        showNotification('Projet supprimé !', 'warning');
    }
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${getNotificationColor(type)}`;
    notification.textContent = message;
    notification.style.transform = 'translateX(100%)';
    notification.style.opacity = '0';
    
    // Ajouter à la page
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.parentElement.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function getNotificationColor(type) {
    switch(type) {
        case 'success': return 'bg-green-500 text-white';
        case 'warning': return 'bg-yellow-500 text-white';
        case 'error': return 'bg-red-500 text-white';
        default: return 'bg-blue-500 text-white';
    }
}

// Fonction utilitaire pour animer les cartes au chargement
function animateCards() {
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Fonction pour le mode sombre (fonctionnalité bonus)
function toggleDarkMode() {
    document.body.classList.toggle('dark');
    // Logique pour persister le choix de l'utilisateur
    const isDark = document.body.classList.contains('dark');
    localStorage.setItem('darkMode', isDark);
}

// Charger le mode sombre depuis le localStorage
function loadDarkMode() {
    const isDark = localStorage.getItem('darkMode') === 'true';
    if (isDark) {
        document.body.classList.add('dark');
    }
}

// Initialiser les animations au chargement
window.addEventListener('load', () => {
    animateCards();
    loadDarkMode();
});
