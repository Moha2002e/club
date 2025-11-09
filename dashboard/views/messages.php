<?php 
require_once __DIR__ . '/../actions/messages.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="font-sans bg-secondary theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        
        <div class="flex-1 overflow-y-auto p-6 bg-secondary">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-6 bg-primary rounded-xl shadow-lg border border-color p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-primary mb-2">
                                💬 Messages
                            </h1>
                            <p class="text-secondary">Gestion des messages du dashboard</p>
                        </div>

                    </div>
                </div>

                <!-- Messages flash -->
                <?php if (isset($_SESSION['flash_message'])): 
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                        <?php echo htmlspecialchars($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <!-- Section des messages du dashboard -->
                <?php if ($isAdmin): ?>
                <div class="bg-primary rounded-xl shadow-lg border border-color mb-6">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            📮 Messages du dashboard
                        </h3>
                        <p class="text-secondary mt-1">Gérez les messages affichés sur le tableau de bord</p>
                        </div>
                    <div class="p-6">
                        <!-- Formulaire de création -->
                        <div class="mb-6 p-6 bg-secondary rounded-xl border border-color shadow-sm">
                            <h4 class="text-lg font-bold text-primary mb-4">
                                ✏️ Nouveau message
                            </h4>
                            <form method="POST" action="index.php?page=messages">
                                <input type="hidden" name="action" value="create_message">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-secondary mb-2">Titre</label>
                                        <input type="text" name="title" required class="w-full px-3 py-2 border border-color bg-primary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary mb-2">Message</label>
                                        <textarea name="message" rows="3" required class="w-full px-3 py-2 border border-color bg-primary text-primary rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>
                                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                    <i data-feather="check" class="w-4 h-4"></i>
                                    Créer le message
                                </button>
                            </form>
                        </div>

                        <!-- Liste des messages -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-primary">
                                📋 Messages existants
                            </h4>
                            <?php if (empty($messages)): ?>
                                <div class="bg-secondary rounded-lg p-8 text-center">
                                    <i data-feather="message-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                                    <h3 class="text-lg font-medium text-primary mb-2">Aucun message créé</h3>
                                    <p class="text-secondary mb-6">Créez votre premier message pour le dashboard.</p>
                                    <button onclick="showCreateMessageModal()" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg">
                                        Créer le premier message
                        </button>
                                </div>
                            <?php else: ?>
                                <?php $__printedMessageIds = []; ?>
                                <?php foreach ($messages as $message): ?>
                                    <?php if (isset($__printedMessageIds[$message['id']])) continue; $__printedMessageIds[$message['id']] = true; ?>
                                    <div class="p-4 border border-color rounded-lg <?php echo $message['is_active'] ? 'bg-green-50 border-green-200' : 'bg-secondary'; ?>">
                                        <div class="flex justify-between items-start mb-2">
                        <div>
                                                <h5 class="font-medium text-primary"><?php echo htmlspecialchars($message['title']); ?></h5>
                                                <p class="text-sm text-secondary">Par <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?> - <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <form method="POST" action="index.php?page=messages" class="inline">
                                                    <input type="hidden" name="action" value="toggle_message">
                                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all <?php echo $message['is_active'] ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white hover:from-yellow-600 hover:to-yellow-700' : 'bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700'; ?>">
                                                        <?php echo $message['is_active'] ? '⏸️ Désactiver' : '✓ Activer'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" action="index.php?page=messages" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">
                                                    <input type="hidden" name="action" value="delete_message">
                                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 shadow-sm hover:shadow-md transition-all">
                                                        🗑️ Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="text-primary"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Section messagerie interne (à développer) -->
                <div class="bg-primary rounded-xl shadow-lg border border-color">
                    <div class="p-6 border-b border-color">
                        <h3 class="text-xl font-bold text-primary">
                            📨 Messagerie interne
                        </h3>
                        <p class="text-secondary mt-1">Système de messagerie entre membres du club</p>
                    </div>
                    <div class="p-6">
                        <div class="bg-secondary rounded-lg p-8 text-center">
                            <i data-feather="message-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-primary mb-2">Messagerie en développement</h3>
                            <p class="text-secondary mb-6">Cette fonctionnalité sera bientôt disponible pour permettre la communication entre les membres du club.</p>
                            <div class="flex items-center justify-center space-x-4 text-sm text-secondary">
                                <div class="flex items-center space-x-2">
                                    <i data-feather="users" class="w-4 h-4"></i>
                                    <span>Messages privés</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i data-feather="hash" class="w-4 h-4"></i>
                                    <span>Canaux de discussion</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showCreateMessageModal() {
            // Scroll vers le formulaire de création
            document.querySelector('form[action*="create_message"]').scrollIntoView({ 
                behavior: 'smooth',
                block: 'center'
            });
            
            // Focus sur le premier input
            setTimeout(() => {
                document.querySelector('input[name="title"]').focus();
            }, 500);
        }

        // Initialiser Feather Icons
        feather.replace();
    </script>
</body>
</html>