<?php 
require_once __DIR__ . '/../actions/event_edit.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'événement - HEPL Tech Lab</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="shortcut icon" type="image/png" href="../images/logo.png">
    <link rel="apple-touch-icon" href="../images/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="views/assets/css/styles.css">
</head>
<body class="bg-gray-50 font-sans theme-<?php echo $currentTheme; ?>">
    <div class="flex h-screen">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
        
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">Modifier l'événement</h1>
                            <p class="text-gray-600">Modifiez les informations de l'événement</p>
                        </div>
                        <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2">
                            <i data-feather="arrow-left" class="w-4 h-4"></i>
                            <span>Retour</span>
                        </a>
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

                <?php if ($event): ?>
                <!-- Formulaire de modification -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <form method="POST" action="index.php?page=event_edit&id=<?php echo $event['id']; ?>" class="space-y-6">
                        <input type="hidden" name="action" value="update_event">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        
                        <!-- Titre et Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($event['title']); ?>" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                <select name="event_type" id="event_type" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Sélectionner un type</option>
                                    <?php foreach ($eventTypes as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $event['event_type'] === $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo htmlspecialchars($event['description']); ?></textarea>
                        </div>

                        <!-- Date et Heure -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                                <input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d', strtotime($event['start_date'])); ?>" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="event_time" class="block text-sm font-medium text-gray-700 mb-2">Heure</label>
                                <input type="time" name="event_time" id="event_time" value="<?php echo date('H:i', strtotime($event['start_date'])); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Projet et Visibilité -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Projet</label>
                                <select name="project_id" id="project_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Événement général</option>
                                    <?php foreach ($allProjects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>" <?php echo $event['project_id'] == $project['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">Visibilité</label>
                                <select name="target_type" id="target_type" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all" <?php echo $event['target_type'] === 'all' ? 'selected' : ''; ?>>Tout le monde</option>
                                    <option value="specific" <?php echo $event['target_type'] === 'specific' ? 'selected' : ''; ?>>Participants spécifiques</option>
                                </select>
                            </div>
                        </div>

                        <!-- Participants (si spécifique) -->
                        <div id="participants-section" class="<?php echo $event['target_type'] === 'specific' ? '' : 'hidden'; ?>">
                            <label for="participants" class="block text-sm font-medium text-gray-700 mb-2">Participants</label>
                            <select name="participants[]" id="participants" multiple 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                                <?php 
                                $currentParticipants = array_column($participants, 'user_id');
                                foreach ($allUsers as $user): 
                                ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo in_array($user['id'], $currentParticipants) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="mt-2 text-xs text-gray-500">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs participants.</p>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                                <i data-feather="save" class="w-4 h-4 inline-block mr-2"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Événement non trouvé -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <i data-feather="alert-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Événement non trouvé</h3>
                    <p class="text-gray-500 mb-6">L'événement que vous souhaitez modifier n'existe pas ou a été supprimé.</p>
                    <a href="index.php?page=events" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Retour aux événements
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Initialiser Feather Icons
        feather.replace();
        
        // Gestion de la visibilité des participants
        const targetTypeSelect = document.getElementById('target_type');
        const participantsSection = document.getElementById('participants-section');
        
        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', function() {
                if (this.value === 'specific') {
                    participantsSection.classList.remove('hidden');
                } else {
                    participantsSection.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
