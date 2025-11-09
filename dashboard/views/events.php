<?php 
require_once __DIR__ . '/../actions/events.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - HEPL Tech Lab</title>
    
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
            <div class="w-full">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">Événements</h1>
                            <p class="text-gray-600">Gestion et calendrier des événements</p>
                        </div>
                        <?php if ($isAdmin): ?>
                        <button onclick="showCreateEventModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                            <i data-feather="plus" class="w-4 h-4"></i>
                            <span>Nouvel événement</span>
                        </button>
                        <?php endif; ?>
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

                <!-- Barre de filtres -->
                <div class="bg-primary rounded-xl shadow-lg border border-color p-6 mb-6">
                    <h3 class="text-lg font-semibold text-primary mb-4">🔍 Filtres</h3>
                    <form method="GET" action="index.php" id="filters-form">
                        <input type="hidden" name="page" value="events">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Filtre par projet -->
                            <div>
                                <label for="project" class="block text-sm font-medium text-gray-700 mb-2">Projet</label>
                                <select id="project" name="project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tous les projets</option>
                                    <?php foreach ($allProjects as $project): ?>
                                        <option value="<?php echo $project['id']; ?>" <?php echo $filterProject == $project['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filtre par type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Tous les types</option>
                                    <?php foreach ($eventTypes as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $filterType === $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </div>
                            
                            <!-- Filtre par date -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                            
                            <!-- Vue -->
                            <div>
                                <label for="view" class="block text-sm font-medium text-gray-700 mb-2">Vue</label>
                                <select id="view" name="view" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="list">Liste</option>
                                    <option value="calendar">Calendrier</option>
                                </select>
                </div>
            </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                    <i data-feather="filter" class="w-4 h-4"></i>
                                    <span>Filtrer</span>
                        </button>
                                <a href="index.php?page=events" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors flex items-center space-x-2">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                    <span>Effacer</span>
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo count($events); ?> événement(s) trouvé(s)
                        </div>
                    </div>
                    </form>
                        </div>

                <!-- Vue Liste des événements -->
                <div id="list-view" class="space-y-6">
                    <?php if (empty($events)): ?>
                        <div class="bg-primary rounded-xl shadow-lg border border-color p-12 text-center">
                            <i data-feather="calendar" class="w-16 h-16 text-secondary mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-primary mb-2">Aucun événement trouvé</h3>
                            <p class="text-secondary mb-6">Il n'y a aucun événement correspondant à vos critères de recherche.</p>
                            <?php if ($isAdmin): ?>
                            <button onclick="showCreateEventModal()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                Créer le premier événement
                        </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <div class="group relative bg-primary rounded-xl shadow-lg border border-color p-6 hover:shadow-2xl transition-all duration-300 overflow-hidden">
                                <!-- Bandeau coloré selon type d'événement -->
                                <div class="absolute top-0 left-0 w-full h-1 <?php 
                                    $eventTypeColors = [
                                        'meeting' => 'bg-blue-500',
                                        'deadline' => 'bg-red-500',
                                        'milestone' => 'bg-purple-500',
                                        'presentation' => 'bg-green-500',
                                        'training' => 'bg-yellow-500',
                                        'social' => 'bg-pink-500',
                                        'other' => 'bg-gray-500'
                                    ];
                                    echo $eventTypeColors[$event['event_type']] ?? 'bg-gray-500';
                                ?>"></div>
                                
                                <div class="flex items-start justify-between mt-2">
                                    <div class="flex-1">
                                        <div class="flex items-center flex-wrap gap-2 mb-3">
                                            <h3 class="text-xl font-bold text-primary group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($event['title']); ?></h3>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full event-type-badge event-type-<?php echo $event['event_type']; ?> shadow-sm">
                                                <?php echo $eventTypes[$event['event_type']] ?? $event['event_type']; ?>
                                            </span>
                                            <?php if ($event['target_type'] === 'specific'): ?>
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-sm">
                                                    🔒 Privé
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white shadow-sm">
                                                    🌍 Public
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="text-secondary mb-4 leading-relaxed"><?php echo htmlspecialchars($event['description']); ?></p>
                                        
                                        <!-- Séparateur décoratif -->
                                        <div class="w-full h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent my-3 opacity-50"></div>
                                        
                                        <div class="flex items-center flex-wrap gap-4 text-sm text-secondary">
                                            <div class="flex items-center gap-1 bg-secondary px-3 py-1.5 rounded-full">
                                                <i data-feather="calendar" class="w-4 h-4"></i>
                                                <span><?php echo date('d/m/Y', strtotime($event['start_date'])); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 bg-secondary px-3 py-1.5 rounded-full">
                                                <i data-feather="clock" class="w-4 h-4"></i>
                                                <span><?php echo date('H:i', strtotime($event['start_date'])); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 bg-secondary px-3 py-1.5 rounded-full">
                                                <i data-feather="folder" class="w-4 h-4"></i>
                                                <span><?php echo htmlspecialchars($event['project_name']); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 bg-secondary px-3 py-1.5 rounded-full">
                                                <i data-feather="user" class="w-4 h-4"></i>
                                                <span><?php echo htmlspecialchars($event['creator_name']); ?></span>
                                            </div>
                                            <?php if ($event['participants_count'] > 0): ?>
                                            <div class="flex items-center gap-1 bg-secondary px-3 py-1.5 rounded-full">
                                                <i data-feather="users" class="w-4 h-4"></i>
                                                <span><?php echo $event['participants_count']; ?> participant(s)</span>
                                            </div>
                                            <?php endif; ?>
                    </div>
                </div>
                                    
                                    <div class="flex items-center gap-2 ml-4">
                                        <button onclick="showEventDetails(<?php echo $event['id']; ?>)" 
                                                class="p-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200" 
                                                title="Voir détails">
                                            <i data-feather="eye" class="w-5 h-5"></i>
                                        </button>
                                        <?php if ($isAdmin): ?>
                                        <button onclick="editEvent(<?php echo $event['id']; ?>)" 
                                                class="p-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200" 
                                                title="Modifier">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <button onclick="deleteEvent(<?php echo $event['id']; ?>)" 
                                                class="p-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200" 
                                                title="Supprimer">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>

                <!-- Vue Calendrier (placeholder) -->
                <div id="calendar-view" class="hidden">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="h-96 bg-gray-50 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <i data-feather="calendar" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-500">Vue calendrier à implémenter</p>
                                <p class="text-sm text-gray-400 mt-2">FullCalendar.js ou similaire</p>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Modal de création d'événement -->
    <div id="create-event-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Nouvel événement</h3>
                </div>
                <form action="index.php?page=events" method="POST" class="p-6">
                    <input type="hidden" name="action" value="create_event">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre *</label>
                            <input type="text" name="title" id="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select name="event_type" id="event_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner un type</option>
                                <?php foreach ($eventTypes as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $value === 'meeting' ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                            <input type="date" name="event_date" id="event_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="event_time" class="block text-sm font-medium text-gray-700 mb-2">Heure</label>
                            <input type="time" name="event_time" id="event_time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Projet</label>
                            <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Événement général</option>
                                <?php foreach ($allProjects as $project): ?>
                                    <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                        <div>
                            <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">Visibilité</label>
                            <select name="target_type" id="target_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">Tout le monde</option>
                                <option value="specific">Participants spécifiques</option>
                            </select>
                        </div>
                    </div>

                    <div id="participants-section" class="mb-6 hidden">
                        <label for="participants" class="block text-sm font-medium text-gray-700 mb-2">Participants</label>
                        <select name="participants[]" id="participants" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                            <?php 
                            $allUsers = $userDAO->getAllMembers();
                            foreach ($allUsers as $user): 
                            ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-gray-500">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs participants.</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideCreateEventModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Annuler</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                            <i data-feather="save" class="w-4 h-4 inline-block mr-2"></i>
                            Créer l'événement
                        </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Gestion des filtres en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');
            
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.id === 'view') {
                        toggleView(this.value);
                    } else {
                        form.submit();
                    }
                });
            });

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
        });

        function toggleView(view) {
            const listView = document.getElementById('list-view');
            const calendarView = document.getElementById('calendar-view');
            
            if (view === 'calendar') {
                listView.classList.add('hidden');
                calendarView.classList.remove('hidden');
            } else {
                listView.classList.remove('hidden');
                calendarView.classList.add('hidden');
            }
        }

        function showCreateEventModal() {
            document.getElementById('create-event-modal').classList.remove('hidden');
        }

        function hideCreateEventModal() {
            document.getElementById('create-event-modal').classList.add('hidden');
        }

        function showEventDetails(eventId) {
            window.location.href = 'index.php?page=event_detail&id=' + eventId;
        }

        function editEvent(eventId) {
            window.location.href = 'index.php?page=event_edit&id=' + eventId;
        }

        function deleteEvent(eventId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?page=events';
                
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

        // Initialiser Feather Icons
        feather.replace();
    </script>
</body>
</html>