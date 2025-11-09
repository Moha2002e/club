<?php 
require_once __DIR__ . '/../actions/event_detail.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'événement - HEPL Tech Lab</title>
    
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
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">Détails de l'événement</h1>
                            <p class="text-gray-600">Informations complètes sur l'événement</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="index.php?page=events" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2">
                                <i data-feather="arrow-left" class="w-4 h-4"></i>
                                <span>Retour</span>
                            </a>
                            <?php if ($isAdmin): ?>
                            <a href="index.php?page=event_edit&id=<?php echo $event['id']; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                <i data-feather="edit" class="w-4 h-4"></i>
                                <span>Modifier</span>
                            </a>
                            <?php endif; ?>
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

                <?php if ($event): ?>
                <!-- Détails de l'événement -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Informations principales -->
                        <div class="lg:col-span-2">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($event['title']); ?></h2>
                                    <div class="flex items-center space-x-3 mb-4">
                                        <span class="px-3 py-1 text-sm font-medium rounded-full event-type-badge event-type-<?php echo $event['event_type']; ?>">
                                            <?php echo $eventTypes[$event['event_type']] ?? $event['event_type']; ?>
                                        </span>
                                        <span class="px-3 py-1 text-sm font-medium rounded-full visibility-badge visibility-<?php echo $event['target_type'] === 'specific' ? 'private' : 'public'; ?>">
                                            <?php echo $event['target_type'] === 'specific' ? 'Privé' : 'Public'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            </div>

                            <!-- Informations détaillées -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Informations</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-3">
                                            <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                                            <div>
                                                <p class="text-sm text-gray-500">Date</p>
                                                <p class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <i data-feather="clock" class="w-5 h-5 text-gray-400"></i>
                                            <div>
                                                <p class="text-sm text-gray-500">Heure</p>
                                                <p class="font-medium text-gray-900"><?php echo date('H:i', strtotime($event['start_date'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <i data-feather="folder" class="w-5 h-5 text-gray-400"></i>
                                            <div>
                                                <p class="text-sm text-gray-500">Projet</p>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($event['project_name'] ?? 'Événement général'); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <i data-feather="user" class="w-5 h-5 text-gray-400"></i>
                                            <div>
                                                <p class="text-sm text-gray-500">Créé par</p>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($event['creator_name']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <i data-feather="clock" class="w-5 h-5 text-gray-400"></i>
                                            <div>
                                                <p class="text-sm text-gray-500">Créé le</p>
                                                <p class="font-medium text-gray-900"><?php echo date('d/m/Y à H:i', strtotime($event['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Participants -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Participants</h3>
                                    <?php if (!empty($participants)): ?>
                                        <div class="space-y-2">
                                            <?php foreach ($participants as $participant): ?>
                                                <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded-lg">
                                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                        <?php echo strtoupper(substr($participant['first_name'], 0, 1) . substr($participant['last_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($participant['first_name'] . ' ' . $participant['last_name']); ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($participant['email']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-gray-500 text-center py-4">Aucun participant spécifique</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Actions et informations supplémentaires -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                                <div class="space-y-3">
                                    <?php if ($isAdmin): ?>
                                    <a href="index.php?page=event_edit&id=<?php echo $event['id']; ?>" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                        <span>Modifier</span>
                                    </a>
                                    <button onclick="deleteEvent(<?php echo $event['id']; ?>)" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                        <span>Supprimer</span>
                                    </button>
                                    <?php endif; ?>
                                    <a href="index.php?page=events" class="w-full flex items-center justify-center space-x-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        <i data-feather="arrow-left" class="w-4 h-4"></i>
                                        <span>Retour aux événements</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Événement non trouvé -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <i data-feather="alert-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Événement non trouvé</h3>
                    <p class="text-gray-500 mb-6">L'événement que vous recherchez n'existe pas ou a été supprimé.</p>
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
        
        function deleteEvent(eventId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?page=event_detail&id=' + eventId;
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_event';
                
                const eventIdInput = document.createElement('input');
                eventIdInput.type = 'hidden';
                eventIdInput.name = 'event_id';
                eventIdInput.value = eventId;
                
                form.appendChild(actionInput);
                form.appendChild(eventIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
