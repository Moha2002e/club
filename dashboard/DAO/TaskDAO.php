<?php
require_once __DIR__ . '/Database.php';

class TaskDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Récupérer toutes les tâches (pour admin)
    public function getAllTasks() {
        $sql = "SELECT DISTINCT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN projects p ON t.project_id = p.id
                ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer toutes les tâches avec filtres (pour admin)
    public function getAllTasksWithFilters($status = '', $priority = '', $project = '', $creator = '') {
        $conditions = [];
        $params = [];
        
        if (!empty($status)) {
            $conditions[] = "t.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($priority)) {
            $conditions[] = "t.priority = :priority";
            $params[':priority'] = $priority;
        }
        
        if (!empty($project)) {
            $conditions[] = "t.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($creator)) {
            $conditions[] = "t.created_by = :created_by";
            $params[':created_by'] = $creator;
        }
        
        $sql = "SELECT DISTINCT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN projects p ON t.project_id = p.id";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer les tâches assignées à un utilisateur avec filtres
    public function getTasksByUserIdWithFilters($userId, $status = '', $priority = '', $project = '', $creator = '', $limit = 5) {
        $conditions = ["ta.user_id = :user_id"];
        $params = [':user_id' => $userId];
        
        if (!empty($status)) {
            $conditions[] = "t.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($priority)) {
            $conditions[] = "t.priority = :priority";
            $params[':priority'] = $priority;
        }
        
        if (!empty($project)) {
            $conditions[] = "t.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($creator)) {
            $conditions[] = "t.created_by = :created_by";
            $params[':created_by'] = $creator;
        }
        
        $sql = "SELECT DISTINCT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN task_assignments ta ON t.id = ta.task_id
                INNER JOIN projects p ON t.project_id = p.id
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer les tâches des projets dont l'utilisateur est membre avec filtres
    public function getTasksByUserProjectsWithFilters($userId, $status = '', $priority = '', $project = '', $creator = '') {
        $conditions = ["pm.user_id = :user_id"];
        $params = [':user_id' => $userId];
        
        if (!empty($status)) {
            $conditions[] = "t.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($priority)) {
            $conditions[] = "t.priority = :priority";
            $params[':priority'] = $priority;
        }
        
        if (!empty($project)) {
            $conditions[] = "t.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($creator)) {
            $conditions[] = "t.created_by = :created_by";
            $params[':created_by'] = $creator;
        }
        
        $sql = "SELECT DISTINCT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN projects p ON t.project_id = p.id
                INNER JOIN project_members pm ON p.id = pm.project_id
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer les tâches assignées à un utilisateur
    public function getTasksByUserId($userId, $limit = 5) {
        $sql = "SELECT DISTINCT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN task_assignments ta ON t.id = ta.task_id
                INNER JOIN projects p ON t.project_id = p.id
                WHERE ta.user_id = :user_id 
                AND t.status != 'completed'
                ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Compter les tâches non terminées d'un utilisateur
    public function countPendingTasksByUserId($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM tasks t
                INNER JOIN task_assignments ta ON t.id = ta.task_id
                WHERE ta.user_id = :user_id 
                AND t.status != 'completed'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    // Créer une nouvelle tâche
    public function createTask($data) {
        try {
            $sql = "INSERT INTO tasks (title, description, project_id, priority, status, due_date, created_by) 
                    VALUES (:title, :description, :project_id, :priority, :status, :due_date, :created_by)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':project_id' => $data['project_id'],
                ':priority' => $data['priority'],
                ':status' => $data['status'],
                ':due_date' => $data['due_date'],
                ':created_by' => $data['created_by']
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur SQL createTask : " . $e->getMessage());
            throw $e;
        }
    }

    // Assigner une tâche à un utilisateur
    public function assignTaskToUser($taskId, $userId) {
        try {
            $sql = "INSERT INTO task_assignments (task_id, user_id) 
                    VALUES (:task_id, :user_id)
                    ON DUPLICATE KEY UPDATE assigned_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':task_id' => $taskId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur SQL assignTaskToUser : " . $e->getMessage());
            return false;
        }
    }

    // Retirer une assignation de tâche
    public function unassignTaskFromUser($taskId, $userId) {
        try {
            $sql = "DELETE FROM task_assignments WHERE task_id = :task_id AND user_id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':task_id' => $taskId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur SQL unassignTaskFromUser : " . $e->getMessage());
            return false;
        }
    }

    // Récupérer une tâche par son ID
    public function getTaskById($taskId) {
        $sql = "SELECT t.*, p.title as project_title 
                FROM tasks t
                INNER JOIN projects p ON t.project_id = p.id
                WHERE t.id = :task_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Récupérer les utilisateurs assignés à une tâche
    public function getTaskAssignees($taskId) {
        $sql = "SELECT u.* FROM users u
                INNER JOIN task_assignments ta ON u.id = ta.user_id
                WHERE ta.task_id = :task_id
                ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    // Mettre à jour une tâche
    public function updateTask($taskId, $data) {
        try {
            $sql = "UPDATE tasks SET 
                    title = :title,
                    description = :description,
                    priority = :priority,
                    status = :status,
                    due_date = :due_date
                    WHERE id = :task_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindValue(':priority', $data['priority'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindValue(':due_date', $data['due_date'] ?: null, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Tâche mise à jour avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Aucune modification effectuée.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur updateTask : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la tâche.'];
        }
    }

    // Supprimer une tâche
    public function deleteTask($taskId) {
        try {
            $sql = "DELETE FROM tasks WHERE id = :task_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Tâche supprimée avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Tâche introuvable.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur suppression tâche : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression de la tâche.'];
        }
    }
}
?>

