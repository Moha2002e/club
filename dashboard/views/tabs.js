// Variables globales pour les onglets
let sidebarCollapsed = false;
let isMobile = false;
const sidebar = document.getElementById('sidebar');
const toggleButton = document.getElementById('toggle-sidebar');
const mobileMenuButton = document.getElementById('mobile-menu-button');
const sidebarOverlay = document.getElementById('sidebar-overlay');
const logoText = document.getElementById('logo-text');
const navTexts = document.querySelectorAll('.nav-text');

// Configuration des onglets
const tabConfig = {
    dashboard: {
        title: 'Tableau de bord',
        description: 'Vue globale du club - projets, événements et membres'
    },
    projects: {
        title: 'Gestion des projets',
        description: 'Créez et suivez vos projets avec statut et progression'
    },
    members: {
        title: 'Gestion des membres',
        description: 'Gérez les membres du club avec leurs rôles et participations'
    },
    events: {
        title: 'Calendrier des événements',
        description: 'Planifiez et organisez vos événements et réunions'
    },
    tasks: {
        title: 'Gestion des tâches',
        description: 'Assignez et suivez les tâches par projet et par membre'
    },
    messages: {
        title: 'Messages internes',
        description: 'Communications et discussions entre membres du club'
    },
    settings: {
        title: 'Paramètres & Authentification',
        description: 'Configurez les rôles, permissions et profils membres'
    }
};

// Fonction pour détecter si on est sur mobile
function checkIfMobile() {
    isMobile = window.innerWidth < 1024;
    return isMobile;
}

// Fonction pour basculer la sidebar
function toggleSidebar() {
    if (isMobile) {
        toggleMobileSidebar();
    } else {
        toggleDesktopSidebar();
    }
}

// Fonction pour gérer la sidebar sur mobile
function toggleMobileSidebar() {
    const isHidden = sidebar.classList.contains('-translate-x-full');
    
    if (isHidden) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('fixed', 'top-0', 'left-0', 'h-full');
        sidebarOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Fonction pour gérer la sidebar sur desktop
function toggleDesktopSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-40');
        logoText.style.display = 'none';
        navTexts.forEach(text => text.style.display = 'none');
        toggleButton.innerHTML = '<i data-feather="chevron-right" class="w-5 h-5 text-gray-600"></i>';
    } else {
    sidebar.classList.remove('w-40');
        sidebar.classList.add('w-64');
        logoText.style.display = 'block';
        navTexts.forEach(text => text.style.display = 'block');
        toggleButton.innerHTML = '<i data-feather="menu" class="w-5 h-5 text-gray-600"></i>';
    }
    
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
    sidebar.classList.add('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
    sidebar.classList.remove('w-24');
        sidebar.classList.add('w-64');
        logoText.style.display = 'block';
        navTexts.forEach(text => text.style.display = 'block');
        sidebarCollapsed = false;
    } else {
        sidebar.classList.remove('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        if (sidebarCollapsed) {
            toggleDesktopSidebar();
        }
    }
}

// Fonction pour changer d'onglet
function switchTab(tabName) {
    // Cacher tous les onglets
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Afficher l'onglet sélectionné
    const targetTab = document.getElementById(tabName + '-section');
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Mettre à jour la navigation active
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active', 'bg-primary', 'text-white');
        item.classList.add('text-gray-700');
    });
    
    const activeNavItem = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeNavItem) {
        activeNavItem.classList.add('active', 'bg-primary', 'text-white');
        activeNavItem.classList.remove('text-gray-700');
    }
    
    // Mettre à jour le titre et la description
    const config = tabConfig[tabName];
    if (config) {
        document.getElementById('page-title').textContent = config.title;
        document.getElementById('page-description').textContent = config.description;
    }
    
    // Fermer la sidebar mobile après sélection
    if (isMobile) {
        closeMobileSidebar();
    }
    
    // Re-initialiser Feather Icons pour les nouveaux contenus
    feather.replace();
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${getNotificationColor(type)}`;
    notification.textContent = message;
    notification.style.transform = 'translateX(100%)';
    notification.style.opacity = '0';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
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

// Gestionnaires d'événements
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la sidebar
    initializeSidebar();
    
    // Événements pour les onglets
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            if (tabName) {
                switchTab(tabName);
            }
        });
    });
    
    // Événements pour la sidebar
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleSidebar);
    }
    
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            if (isMobile) {
                toggleMobileSidebar();
            }
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeMobileSidebar);
    }
    
    // Gestion du redimensionnement
    window.addEventListener('resize', function() {
        const wasMobile = isMobile;
        checkIfMobile();
        
        if (wasMobile !== isMobile) {
            initializeSidebar();
        }
    });
    
    // Gestion de la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile) {
            closeMobileSidebar();
        }
    });
    
    // Initialiser avec l'onglet dashboard par défaut
    switchTab('dashboard');
    
    // Notification de bienvenue
    setTimeout(() => {
        showNotification('Dashboard HEPL Tech Lab chargé avec succès !', 'success');
    }, 1000);
});

// Animation au chargement des cartes
window.addEventListener('load', () => {
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
});