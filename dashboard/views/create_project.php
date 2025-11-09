<?php
/**
 * Page de création de projet
 */

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = 'Vous devez être connecté pour accéder à cette page.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=login');
    exit();
}

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = 'Accès non autorisé. Seuls les administrateurs peuvent créer des projets.';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?page=projects');
    exit();
}

require_once __DIR__ . '/../includes/theme.php';

// Traiter le formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_project') {
    require_once __DIR__ . '/../actions/create_project.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Projet - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 font-sans theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen overflow-hidden">
        <?php include __DIR__ . '/../includes/nav.php'; ?>

        <!-- Contenu principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 p-4">
                <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <!-- Messages flash -->
                <?php 
                if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo htmlspecialchars($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de création -->
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-800 mb-2">Créer un nouveau projet</h1>
                            <p class="text-gray-600">Remplissez les informations pour créer un projet</p>
                        </div>

                        <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                            <input type="hidden" name="action" value="create_project">

                            <!-- Nom du projet -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom du projet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="title" name="title" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ex: Application mobile du club">
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" name="description" rows="5" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Décrivez l'objectif et les détails du projet..."></textarea>
                            </div>

                            <!-- Date de début -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de début
                                </label>
                                <input type="date" id="start_date" name="start_date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Date de fin -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de fin
                                </label>
                                <input type="date" id="due_date" name="due_date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Statut <span class="text-red-500">*</span>
                                </label>
                                <select id="status" name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="planning">En planification</option>
                                    <option value="active" selected>Actif</option>
                                    <option value="on_hold">En pause</option>
                                    <option value="completed">Terminé</option>
                                    <option value="cancelled">Annulé</option>
                                </select>
                            </div>

                            <!-- Visibilité -->
                            <div>
                                <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                                    Visibilité <span class="text-red-500">*</span>
                                </label>
                                <select id="visibility" name="visibility" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="public" selected>Public (visible par tous les membres)</option>
                                    <option value="private">Privé (visible uniquement par les membres du projet)</option>
                                </select>
                            </div>

                            <!-- Fichier PDF -->
                            <div>
                                <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">
                                    Document PDF (optionnel)
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="pdf_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Télécharger un fichier</span>
                                                <input id="pdf_file" name="pdf_file" type="file" accept=".pdf" class="sr-only">
                                            </label>
                                            <p class="pl-1">ou glisser-déposer</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF jusqu'à 10MB</p>
                                    </div>
                                </div>
                                <div id="file-info" class="mt-2 text-sm text-gray-600 hidden">
                                    <span id="file-name"></span>
                                    <span id="file-size"></span>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                                <a href="index.php?page=projects" 
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    Annuler
                                </a>
                                <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                                    <i data-feather="check" class="w-4 h-4"></i>
                                    <span>Créer le projet</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="views/assets/js/dashboard.js"></script>
    <script>
        // Gestion de l'upload de fichier PDF
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('pdf_file');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const dropZone = fileInput.closest('.border-dashed');

            // Gestion du changement de fichier
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.type !== 'application/pdf') {
                        alert('Veuillez sélectionner un fichier PDF valide.');
                        fileInput.value = '';
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) { // 10MB
                        alert('Le fichier est trop volumineux. Taille maximale : 10MB');
                        fileInput.value = '';
                        return;
                    }
                    
                    fileName.textContent = file.name;
                    fileSize.textContent = ` (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                    fileInfo.classList.remove('hidden');
                } else {
                    fileInfo.classList.add('hidden');
                }
            });

            // Gestion du drag & drop
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('border-blue-400', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type === 'application/pdf') {
                        fileInput.files = files;
                        fileInput.dispatchEvent(new Event('change'));
                    } else {
                        alert('Veuillez déposer un fichier PDF valide.');
                    }
                }
            });
        });
    </script>
</body>
</html>

