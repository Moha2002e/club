<?php
require_once __DIR__ . '/Database.php';

class EventsDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Récupérer les événements qui concernent un utilisateur
    public function getEventsByUserId($userId, $limit = 5) {
        $sql = "SELECT e.*, 
                       CASE 
                           WHEN e.target_type = 'all' THEN 1
                           WHEN ep.user_id IS NOT NULL THEN 1
                           ELSE 0
                       END as is_concerned
                FROM events e
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id
                WHERE e.start_date >= CURDATE()
                AND (e.target_type = 'all' OR ep.user_id IS NOT NULL)
                ORDER BY e.start_date ASC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Compter les événements à venir pour un utilisateur
    public function countUpcomingEventsByUserId($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM events e
                LEFT JOIN event_participants ep ON e.id = ep.event_id
                WHERE e.start_date >= CURDATE()
                AND (e.target_type = 'all' OR ep.user_id = :user_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    // Récupérer tous les événements avec filtres (pour admin)
    public function getAllEventsWithFilters($project = '', $type = '', $date = '') {
        $conditions = [];
        $params = [];
        
        if (!empty($project)) {
            $conditions[] = "e.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($type)) {
            $conditions[] = "e.event_type = :event_type";
            $params[':event_type'] = $type;
        }
        
        if (!empty($date)) {
            $conditions[] = "DATE(e.start_date) = :event_date";
            $params[':event_date'] = $date;
        }
        
        $sql = "SELECT e.*, p.title as project_title 
                FROM events e
                LEFT JOIN projects p ON e.project_id = p.id";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY e.start_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer les événements d'un utilisateur avec filtres
    public function getEventsByUserIdWithFilters($userId, $project = '', $type = '', $date = '') {
        $conditions = [
            "e.start_date >= CURDATE()",
            "(e.target_type = 'all' OR ep.user_id = :user_id)"
        ];
        $params = [':user_id' => $userId];
        
        if (!empty($project)) {
            $conditions[] = "e.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($type)) {
            $conditions[] = "e.event_type = :event_type";
            $params[':event_type'] = $type;
        }
        
        if (!empty($date)) {
            $conditions[] = "DATE(e.start_date) = :event_date";
            $params[':event_date'] = $date;
        }
        
        $sql = "SELECT e.*, p.title as project_title,
                       CASE 
                           WHEN e.target_type = 'all' THEN 1
                           WHEN ep.user_id IS NOT NULL THEN 1
                           ELSE 0
                       END as is_concerned
                FROM events e
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id
                LEFT JOIN projects p ON e.project_id = p.id
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY e.start_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer les événements des projets dont l'utilisateur est membre avec filtres
    public function getEventsByUserProjectsWithFilters($userId, $project = '', $type = '', $date = '') {
        $conditions = [
            "e.start_date >= CURDATE()",
            "(e.target_type = 'all' OR ep.user_id = :user_id_where OR pm.user_id = :user_id2_where)"
        ];
        $params = [
            ':user_id_join' => $userId,
            ':user_id2_join' => $userId,
            ':user_id_where' => $userId,
            ':user_id2_where' => $userId
        ];
        
        if (!empty($project)) {
            $conditions[] = "e.project_id = :project_id";
            $params[':project_id'] = $project;
        }
        
        if (!empty($type)) {
            $conditions[] = "e.event_type = :event_type";
            $params[':event_type'] = $type;
        }
        
        if (!empty($date)) {
            $conditions[] = "DATE(e.start_date) = :event_date";
            $params[':event_date'] = $date;
        }
        
        $sql = "SELECT DISTINCT e.*, p.title as project_title,
                       CASE 
                           WHEN e.target_type = 'all' THEN 1
                           WHEN ep.user_id IS NOT NULL THEN 1
                           WHEN pm.user_id IS NOT NULL THEN 1
                           ELSE 0
                       END as is_concerned
                FROM events e
                LEFT JOIN event_participants ep ON e.id = ep.event_id AND ep.user_id = :user_id_join
                LEFT JOIN projects p ON e.project_id = p.id
                LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = :user_id2_join
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY e.start_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Créer un événement
    public function createEvent($data, $participants = []) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO events (title, description, start_date, end_date, event_type, project_id, target_type, created_by, created_at) 
                    VALUES (:title, :description, :start_date, :end_date, :event_type, :project_id, :target_type, :created_by, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindValue(':start_date', $data['event_date'] . ' ' . ($data['event_time'] ?: '00:00:00'), PDO::PARAM_STR);
            $stmt->bindValue(':end_date', $data['event_date'] . ' ' . ($data['event_time'] ?: '23:59:59'), PDO::PARAM_STR);
            $stmt->bindValue(':event_type', $data['event_type'], PDO::PARAM_STR);
            $stmt->bindValue(':project_id', $data['project_id'], PDO::PARAM_INT);
            $stmt->bindValue(':target_type', $data['target_type'], PDO::PARAM_STR);
            $stmt->bindValue(':created_by', $data['created_by'], PDO::PARAM_INT);
            $stmt->execute();
            
            $eventId = $this->pdo->lastInsertId();
            
            // Ajouter les participants si c'est un événement spécifique
            if ($data['target_type'] === 'specific' && !empty($participants)) {
                foreach ($participants as $participantId) {
                    $this->addEventParticipant($eventId, $participantId);
                }
            }
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Événement créé avec succès.', 'event_id' => $eventId];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur createEvent : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création de l\'événement.'];
        }
    }

    // Ajouter un participant à un événement
    public function addEventParticipant($eventId, $userId) {
        $sql = "INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Récupérer les participants d'un événement
    public function getEventParticipants($eventId) {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email 
                FROM event_participants ep
                INNER JOIN users u ON ep.user_id = u.id
                WHERE ep.event_id = :event_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Supprimer un événement
    public function deleteEvent($eventId) {
        try {
            $this->pdo->beginTransaction();
            
            // Supprimer les participants
            $sql = "DELETE FROM event_participants WHERE event_id = :event_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Supprimer l'événement
            $sql = "DELETE FROM events WHERE id = :event_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Événement supprimé avec succès.'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur deleteEvent : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression de l\'événement.'];
        }
    }

    // Récupérer un événement par ID
    public function getEventById($eventId) {
        $sql = "SELECT e.*, p.title as project_title, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as creator_name
                FROM events e
                LEFT JOIN projects p ON e.project_id = p.id
                LEFT JOIN users u ON e.created_by = u.id
                WHERE e.id = :event_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Mettre à jour un événement
    public function updateEvent($eventId, $data) {
        try {
            $this->pdo->beginTransaction();
            
            // Mettre à jour l'événement
            $sql = "UPDATE events 
                    SET title = :title, description = :description, event_type = :event_type,
                        start_date = :start_date, project_id = :project_id, target_type = :target_type,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :event_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':event_type' => $data['event_type'],
                ':start_date' => $data['start_date'],
                ':project_id' => $data['project_id'],
                ':target_type' => $data['target_type'],
                ':event_id' => $eventId
            ]);
            
            // Supprimer les anciens participants
            $deleteSql = "DELETE FROM event_participants WHERE event_id = :event_id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->bindValue(':event_id', $eventId, PDO::PARAM_INT);
            $deleteStmt->execute();
            
            // Ajouter les nouveaux participants
            if (!empty($data['participants'])) {
                $insertSql = "INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)";
                $insertStmt = $this->pdo->prepare($insertSql);
                
                foreach ($data['participants'] as $userId) {
                    $insertStmt->execute([
                        ':event_id' => $eventId,
                        ':user_id' => $userId
                    ]);
                }
            }
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Événement modifié avec succès.'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur updateEvent : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la modification de l\'événement.'];
        }
    }
}
?>

