<?php
require_once __DIR__ . '/Database.php';

class DashboardMessageDAO {
    private $pdo;

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Récupérer le message actif du dashboard
     */
    public function getActiveMessage() {
        $sql = "SELECT dm.*, u.first_name, u.last_name 
                FROM dashboard_messages dm
                INNER JOIN users u ON dm.created_by = u.id
                WHERE dm.is_active = 1
                ORDER BY dm.created_at DESC
                LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur getActiveMessage : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer tous les messages (admin)
     */
    public function getAllMessages() {
        $sql = "SELECT dm.*, u.first_name, u.last_name 
                FROM dashboard_messages dm
                INNER JOIN users u ON dm.created_by = u.id
                ORDER BY dm.created_at DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getAllMessages : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer un nouveau message
     */
    public function createMessage($title, $message, $createdBy) {
        // Désactiver tous les autres messages
        $this->deactivateAllMessages();
        
        $sql = "INSERT INTO dashboard_messages (title, message, created_by) 
                VALUES (:title, :message, :created_by)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':title' => $title,
                ':message' => $message,
                ':created_by' => $createdBy
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Message créé avec succès.'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la création du message.'];
        } catch (PDOException $e) {
            error_log("Erreur createMessage : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création du message.'];
        }
    }

    /**
     * Mettre à jour un message
     */
    public function updateMessage($id, $title, $message) {
        $sql = "UPDATE dashboard_messages 
                SET title = :title, message = :message, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':message' => $message
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Message mis à jour avec succès.'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        } catch (PDOException $e) {
            error_log("Erreur updateMessage : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    /**
     * Supprimer un message
     */
    public function deleteMessage($id) {
        $sql = "DELETE FROM dashboard_messages WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Message supprimé avec succès.'];
            }
            return ['success' => false, 'message' => 'Message introuvable.'];
        } catch (PDOException $e) {
            error_log("Erreur deleteMessage : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression.'];
        }
    }

    /**
     * Activer/désactiver un message
     */
    public function toggleMessageStatus($id) {
        $sql = "UPDATE dashboard_messages SET is_active = NOT is_active WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            // Vérifier le nouveau statut
            $sql = "SELECT is_active FROM dashboard_messages WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $message = $stmt->fetch();
            
            $status = $message['is_active'] ? 'activé' : 'désactivé';
            return ['success' => true, 'message' => "Message $status avec succès."];
        } catch (PDOException $e) {
            error_log("Erreur toggleMessageStatus : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la modification du statut.'];
        }
    }

    /**
     * Désactiver tous les messages
     */
    private function deactivateAllMessages() {
        $sql = "UPDATE dashboard_messages SET is_active = 0";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur deactivateAllMessages : " . $e->getMessage());
        }
    }
}
?>
