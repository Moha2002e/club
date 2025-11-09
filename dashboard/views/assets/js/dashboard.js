// Variables globales
let sidebarCollapsed = false;
let isMobile = false;
let sidebar;
let toggleButton;
let mobileMenuButton;
let sidebarOverlay;
let logoText;
let navTexts;

// Détecter si mobile
function checkIfMobile() {
    isMobile = window.innerWidth < 1024;
    return isMobile;
}

// Basculer la sidebar
function toggleSidebar() {
    if (isMobile) {
        toggleMobileSidebar();
    } else {
        toggleDesktopSidebar();
    }
}

// Sidebar mobile
function toggleMobileSidebar() {
    if (!sidebar || !sidebarOverlay) return;
    
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

// Sidebar desktop
function toggleDesktopSidebar() {
    if (!sidebar || !navTexts || !toggleButton) return;
    
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
            sidebar.classList.remove('w-64');
        sidebar.classList.add('w-24');
        if (logoText) logoText.style.display = 'none';
        navTexts.forEach(text => text.style.display = 'none');
        toggleButton.innerHTML = '<i data-feather="chevron-right" class="w-5 h-5 text-gray-600"></i>';
    } else {
        sidebar.classList.remove('w-24');
        sidebar.classList.add('w-64');
        if (logoText) logoText.style.display = 'block';
        navTexts.forEach(text => text.style.display = 'block');
        toggleButton.innerHTML = '<i data-feather="menu" class="w-5 h-5 text-gray-600"></i>';
    }
    
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Fermer sidebar mobile
function closeMobileSidebar() {
    if (isMobile && sidebar && sidebarOverlay) {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Initialiser sidebar
function initializeSidebar() {
    if (!sidebar) return;
    
    checkIfMobile();
    
    if (isMobile) {
        sidebar.classList.add('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
        sidebar.classList.remove('w-24');
        sidebar.classList.add('w-64');
        if (logoText) logoText.style.display = 'block';
        if (navTexts) navTexts.forEach(text => text.style.display = 'block');
        sidebarCollapsed = false;
    } else {
        sidebar.classList.remove('fixed', 'top-0', 'left-0', 'h-full', '-translate-x-full');
        if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}


// Notification
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white p-4 rounded-lg shadow-lg z-50 transform translate-x-full opacity-0 transition-all duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Dashboard JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les éléments DOM après le chargement de la page
    sidebar = document.getElementById('sidebar');
    toggleButton = document.getElementById('toggle-sidebar');
    mobileMenuButton = document.getElementById('mobile-menu-button');
    sidebarOverlay = document.getElementById('sidebar-overlay');
    logoText = document.getElementById('logo-text');
    navTexts = document.querySelectorAll('.nav-text');
    
    // Vérifier si on est sur une page de dashboard (avec sidebar)
    if (!sidebar) {
        console.log('Dashboard elements not found, skipping dashboard initialization');
        return;
    }
    
    // Initialiser Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    initializeSidebar();
    
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
    
    window.addEventListener('resize', function() {
        const wasMobile = isMobile;
        checkIfMobile();
        
        if (wasMobile !== isMobile) {
            initializeSidebar();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile) {
            closeMobileSidebar();
        }
    });
    
    // Initialiser le dashboard si on est sur la page
    if (sidebar) {
        initDashboard();
        addEventListeners();
    }
});

// Initialiser le dashboard
function initDashboard() {
    if (!sidebar) return;
    
    console.log('Dashboard initialized');
    
    // Ajouter des animations aux cartes
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('card-animation');
        }, index * 50);
    });
    
    // Rafraîchir les icônes Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function addEventListeners() {
    // Gestionnaire pour le bouton "Nouvelle tâche"
    const newTaskBtn = document.querySelector('.btn-new-task');
    if (newTaskBtn) {
        newTaskBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Fonctionnalité à venir', 'info');
        });
    }
    
    // Animation hover pour les cartes de tâches
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Animation hover pour les cartes de projets
    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Rafraîchir les données du dashboard
function refreshDashboardData() {
    fetch('?ajax=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Dashboard data refreshed', data);
                showNotification('Données mises à jour', 'success');
                // Mettre à jour l'interface si nécessaire
            }
        })
        .catch(error => {
            console.error('Erreur de rafraîchissement:', error);
            showNotification('Erreur de rafraîchissement', 'error');
        });
}
