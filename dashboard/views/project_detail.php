<?php 
require_once __DIR__ . '/../actions/project_detail.php';
require_once __DIR__ . '/../includes/theme.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - HEPL Tech Lab</title>
    
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
        <div class="max-w-6xl mx-auto">
            <?php if (isset($debugDataJson)): ?>
                <div class="mb-6 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-sm text-yellow-800">
                    <strong>DEBUG (server payload)</strong>
                    <pre style="white-space:pre-wrap;word-break:break-word;margin-top:.5rem;"><?php echo htmlspecialchars($debugDataJson); ?></pre>
                </div>
            <?php endif; ?>
            <!-- Header avec retour -->
            <div class="mb-6">
                <a href="index.php?page=projects" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                    Retour aux projets
                </a>
            </div>

            <!-- Messages flash -->
            <?php if (isset($debug_not_found) && $debug_not_found): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 border border-red-200">
                    <strong>Projet introuvable (debug)</strong>
                    <div>ID demandé: <?php echo htmlspecialchars($debug_missing_project_id ?? ''); ?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash_message'])): 
                $flashMessage = $_SESSION['flash_message'];
                $flashType = $_SESSION['flash_type'] ?? 'info';
                unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $flashType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Titre et actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($project['title']); ?></h1>
                            <span class="badge-status badge-<?php echo $project['status']; ?>">
                                <?php 
                                    $statuses = ['planning' => 'Planification', 'active' => 'Actif', 'completed' => 'Terminé', 'on_hold' => 'En pause'];
                                    echo $statuses[$project['status']] ?? $project['status']; 
                                ?>
                            </span>
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $project['visibility'] === 'public' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $project['visibility'] === 'public' ? 'Public' : 'Privé'; ?>
                            </span>
                        </div>
                        <p class="text-gray-600 mb-4"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                        
                        <!-- Document PDF -->
                        <?php if (!empty($project['pdf_file'])): ?>
                            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">Document PDF</h4>
                                        <p class="text-sm text-gray-500">Documentation du projet</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?php echo htmlspecialchars($project['pdf_file']); ?>" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            Ouvrir PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex gap-6 text-sm text-gray-500">
                            <div>
                                <i data-feather="user" class="w-4 h-4 inline"></i>
                                Propriétaire : <strong><?php echo htmlspecialchars($project['owner_first_name'] . ' ' . $project['owner_last_name']); ?></strong>
                            </div>
                            <?php if ($project['start_date']): ?>
                                <div>
                                    <i data-feather="calendar" class="w-4 h-4 inline"></i>
                                    Début : <?php echo date('d/m/Y', strtotime($project['start_date'])); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($project['due_date']): ?>
                                <div>
                                    <i data-feather="flag" class="w-4 h-4 inline"></i>
                                    Échéance : <?php echo date('d/m/Y', strtotime($project['due_date'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <?php if ($isAdmin): ?>
                            <a href="index.php?page=project_edit&id=<?php echo $project['id']; ?>" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                                <i data-feather="edit" class="w-4 h-4"></i>
                                Modifier
                            </a>
                            <form method="POST" action="index.php?page=projects" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                    Supprimer
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if (!$isOwner && !$isAdmin): ?>
                            <?php if ($isMember): ?>
                                <form method="POST" action="index.php?page=projects" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter ce projet ?');">
                                    <input type="hidden" name="action" value="leave">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                                        <i data-feather="log-out" class="w-4 h-4"></i>
                                        Quitter
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="index.php?page=projects">
                                    <input type="hidden" name="action" value="join">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                                        <i data-feather="user-plus" class="w-4 h-4"></i>
                                        Rejoindre
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Membres -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i data-feather="users" class="w-5 h-5"></i>
                            Membres (<?php echo count($members); ?>)
                        </h2>
                        <?php if ($isAdmin || $isOwner): ?>
                            <a href="index.php?page=add_member&project_id=<?php echo $project['id']; ?>" 
                               class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                <i data-feather="user-plus" class="w-4 h-4"></i>
                                Ajouter
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($members)): ?>
                        <p class="text-gray-500 text-center py-8">Aucun membre pour l'instant</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php $__printedMemberIds = []; ?>
                            <?php foreach ($members as $member): ?>
                                <?php if (isset($__printedMemberIds[$member['user_id']])) continue; $__printedMemberIds[$member['user_id']] = true; ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            <?php echo strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($member['email']); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $member['role'] === 'owner' ? 'bg-purple-100 text-purple-800' : 'bg-purple-100 text-purple-700'; ?>">
                                            <?php echo ucfirst($member['role'] ?? 'member'); ?>
                                        </span>
                                        <?php if (($isAdmin || $isOwner) && $member['role'] !== 'owner'): ?>
                                            <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer ce membre du projet ?');" class="inline">
                                                <input type="hidden" name="action" value="remove_member">
                                                <input type="hidden" name="user_id" value="<?php echo $member['user_id']; ?>">
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Retirer du projet">
                                                    <i data-feather="user-minus" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tâches -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i data-feather="check-square" class="w-5 h-5"></i>
                            Tâches (<?php echo count($tasks); ?>)
                        </h2>
                        <?php if ($isAdmin): ?>
                            <a href="index.php?page=add_task&project_id=<?php echo $project['id']; ?>" 
                               class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                                <i data-feather="plus" class="w-4 h-4"></i>
                                Ajouter
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($tasks)): ?>
                        <p class="text-gray-500 text-center py-8">Aucune tâche créée</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php $__printedTaskIds = []; ?>
                            <?php foreach ($tasks as $task): ?>
                                <?php if (isset($__printedTaskIds[$task['id']])) continue; $__printedTaskIds[$task['id']] = true; ?>
                                <div class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="showTaskDetail(<?php echo htmlspecialchars(json_encode($task), ENT_QUOTES, 'UTF-8'); ?>)">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($task['title']); ?></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button onclick="event.stopPropagation(); showTaskDetail(<?php echo htmlspecialchars(json_encode($task), ENT_QUOTES, 'UTF-8'); ?>)" class="p-1 text-blue-600 hover:bg-blue-50 rounded transition" title="Voir détails">
                                                <i data-feather="eye" class="w-4 h-4"></i>
                                            </button>
                                            <span class="badge-priority badge-<?php echo $task['priority']; ?>">
                                                <?php 
                                                    $priorities = ['low' => 'Basse', 'medium' => 'Moyenne', 'high' => 'Haute', 'urgent' => 'Urgent'];
                                                    echo $priorities[$task['priority']]; 
                                                ?>
                                            </span>
                                            <?php if ($isAdmin || $isOwner || $isMember): ?>
                                                <form method="POST" action="" onsubmit="event.stopPropagation(); return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');" class="inline">
                                                    <input type="hidden" name="action" value="delete_task">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Supprimer la tâche" onclick="event.stopPropagation()">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <span class="badge-status badge-<?php echo $task['status']; ?>">
                                            <?php echo ucfirst($task['status']); ?>
                                        </span>
                                        <?php if ($task['assigned_count'] > 0): ?>
                                            <span>
                                                <i data-feather="users" class="w-3 h-3 inline"></i>
                                                <?php echo $task['assigned_count']; ?> assigné(s)
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($task['due_date']): ?>
                                            <span>
                                                <i data-feather="calendar" class="w-3 h-3 inline"></i>
                                                <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal détails tâche -->
    <div id="task-detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-800" id="modal-task-title"></h3>
                    <button onclick="closeTaskDetail()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i data-feather="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Description</label>
                        <p id="modal-task-description" class="text-gray-800 mt-1"></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Priorité</label>
                            <div id="modal-task-priority" class="mt-1"></div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Statut</label>
                            <div id="modal-task-status" class="mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Date d'échéance</label>
                            <p id="modal-task-due-date" class="text-gray-800 mt-1"></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Assignés</label>
                            <p id="modal-task-assigned" class="text-gray-800 mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTaskDetail(task) {
            const modal = document.getElementById('task-detail-modal');
            document.getElementById('modal-task-title').textContent = task.title;
            document.getElementById('modal-task-description').textContent = task.description || 'Aucune description';
            
            const priorities = {
                'low': '<span class="badge-priority badge-low">Basse</span>',
                'medium': '<span class="badge-priority badge-medium">Moyenne</span>',
                'high': '<span class="badge-priority badge-high">Haute</span>',
                'urgent': '<span class="badge-priority badge-urgent">Urgent</span>'
            };
            document.getElementById('modal-task-priority').innerHTML = priorities[task.priority] || task.priority;
            
            const statuses = {
                'pending': '<span class="badge-status badge-pending">En attente</span>',
                'in_progress': '<span class="badge-status badge-in_progress">En cours</span>',
                'completed': '<span class="badge-status badge-completed">Terminé</span>',
                'cancelled': '<span class="badge-status badge-cancelled">Annulé</span>'
            };
            document.getElementById('modal-task-status').innerHTML = statuses[task.status] || task.status;
            
            document.getElementById('modal-task-due-date').textContent = task.due_date ? 
                new Date(task.due_date).toLocaleDateString('fr-FR') : 'Non définie';
            
            document.getElementById('modal-task-assigned').textContent = task.assigned_count > 0 ? 
                task.assigned_count + ' personne(s)' : 'Personne';
            
            modal.classList.remove('hidden');
            feather.replace();
        }
        
        function closeTaskDetail() {
            document.getElementById('task-detail-modal').classList.add('hidden');
        }
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTaskDetail();
            }
        });
        
        feather.replace();
    </script>
    <?php if (isset($debugDataJson)): ?>
    <script>
        try {
            console.group('project_detail debug (server)');
            var debug = <?php echo $debugDataJson; ?>;
            console.log('project_id:', debug.project_id);
            console.log('members: raw=', debug.raw_members_count, ' unique=', debug.unique_member_count);
            console.log('duplicate member ids:', debug.dupe_member_ids);
            console.log('tasks: raw=', debug.raw_task_count, ' unique=', debug.unique_task_count);
            console.log('duplicate task ids:', debug.dupe_task_ids);
            console.log('members_sample:', debug.members_sample);
            console.log('tasks_sample:', debug.tasks_sample);
            console.groupEnd();
        } catch (e) {
            console.error('Erreur debug client:', e);
        }
    </script>
    <?php endif; ?>
</body>
</html>

