<?php
require_once __DIR__ . '/Database.php';

class ProjectDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Récupérer tous les projets publics
    public function getAllPublicProjects() {
        $sql = "SELECT p.*, 
                       COUNT(DISTINCT pm.user_id) as member_count,
                       COUNT(DISTINCT t.id) as task_count,
                       u.first_name as owner_first_name,
                       u.last_name as owner_last_name
                FROM projects p
                LEFT JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN tasks t ON p.id = t.project_id
                LEFT JOIN users u ON p.owner_id = u.id
                WHERE p.visibility = 'public'
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer TOUS les projets (pour admin)
    public function getAllProjects() {
        $sql = "SELECT p.*, 
                       COUNT(DISTINCT pm.user_id) as member_count,
                       COUNT(DISTINCT t.id) as task_count,
                       u.first_name as owner_first_name,
                       u.last_name as owner_last_name
                FROM projects p
                LEFT JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN tasks t ON p.id = t.project_id
                LEFT JOIN users u ON p.owner_id = u.id
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Créer un nouveau projet
    public function createProject($data) {
        try {
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO projects (title, description, start_date, due_date, status, visibility, owner_id, pdf_file) 
                    VALUES (:title, :description, :start_date, :due_date, :status, :visibility, :owner_id, :pdf_file)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':start_date' => $data['start_date'],
                ':due_date' => $data['due_date'],
                ':status' => $data['status'],
                ':visibility' => $data['visibility'],
                ':owner_id' => $data['owner_id'],
                ':pdf_file' => $data['pdf_file'] ?? null
            ]);
            
            if ($result) {
                $projectId = $this->pdo->lastInsertId();
                
                // Ajouter automatiquement le propriétaire comme membre du projet
                $memberSql = "INSERT INTO project_members (project_id, user_id, role) VALUES (:project_id, :user_id, 'owner')";
                $memberStmt = $this->pdo->prepare($memberSql);
                $memberStmt->execute([
                    ':project_id' => $projectId,
                    ':user_id' => $data['owner_id']
                ]);
                
                $this->pdo->commit();
                return $projectId;
            }
            
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur SQL createProject : " . $e->getMessage());
            throw $e;
        }
    }

    // Vérifier si un utilisateur est membre d'un projet
    public function isUserMemberOfProject($userId, $projectId) {
        $sql = "SELECT COUNT(*) as count FROM project_members 
                WHERE user_id = :user_id AND project_id = :project_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    // Récupérer les projets d'un utilisateur
    public function getProjectsByUserId($userId, $limit = 5) {
        $sql = "SELECT p.*, 
                       COUNT(DISTINCT pm.user_id) as member_count,
                       COUNT(DISTINCT t.id) as task_count
                FROM projects p
                INNER JOIN project_members pm_user ON p.id = pm_user.project_id
                LEFT JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN tasks t ON p.id = t.project_id
                WHERE pm_user.user_id = :user_id
                GROUP BY p.id
                ORDER BY p.updated_at DESC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Compter les projets d'un utilisateur
    public function countUserProjects($userId) {
        $sql = "SELECT COUNT(DISTINCT p.id) as count
                FROM projects p
                INNER JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    // Récupérer un projet par ID
    public function getProjectById($projectId) {
        $sql = "SELECT p.*, 
                       u.first_name as owner_first_name,
                       u.last_name as owner_last_name,
                       u.email as owner_email
                FROM projects p
                LEFT JOIN users u ON p.owner_id = u.id
                WHERE p.id = :project_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Ajouter un membre à un projet
    public function addMemberToProject($projectId, $userId, $role = 'member') {
        $sql = "INSERT INTO project_members (project_id, user_id, role) 
                VALUES (:project_id, :user_id, :role)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
            $stmt->execute();
            return ['success' => true, 'message' => 'Vous avez rejoint le projet avec succès !'];
        } catch (PDOException $e) {
            error_log("Erreur ajout membre : " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Vous êtes déjà membre de ce projet.'];
            }
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription au projet.'];
        }
    }

    // Retirer un membre d'un projet
    public function removeMemberFromProject($projectId, $userId) {
        $sql = "DELETE FROM project_members 
                WHERE project_id = :project_id AND user_id = :user_id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Membre retiré du projet avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Ce membre ne fait pas partie du projet.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur retrait membre : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors du retrait du membre.'];
        }
    }

    // Supprimer un projet (admin uniquement)
    public function deleteProject($projectId) {
        try {
            // D'abord, récupérer les informations du projet pour supprimer le PDF
            $selectSql = "SELECT pdf_file FROM projects WHERE id = :project_id";
            $selectStmt = $this->pdo->prepare($selectSql);
            $selectStmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $selectStmt->execute();
            $project = $selectStmt->fetch();
            
            // Supprimer le projet de la base de données
            $deleteSql = "DELETE FROM projects WHERE id = :project_id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $deleteStmt->execute();
            
            // Supprimer le fichier PDF s'il existe
            if ($project && !empty($project['pdf_file'])) {
                $pdfPath = __DIR__ . '/../' . $project['pdf_file'];
                if (file_exists($pdfPath)) {
                    if (unlink($pdfPath)) {
                        error_log("Fichier PDF supprimé : " . $pdfPath);
                    } else {
                        error_log("Erreur lors de la suppression du fichier PDF : " . $pdfPath);
                    }
                }
            }
            
            return ['success' => true, 'message' => 'Projet supprimé avec succès.'];
        } catch (PDOException $e) {
            error_log("Erreur suppression projet : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression.'];
        }
    }

    // Mettre à jour un projet (admin uniquement)
    public function updateProject($projectId, $data) {
        $sql = "UPDATE projects SET 
                title = :title,
                description = :description,
                start_date = :start_date,
                due_date = :due_date,
                status = :status,
                visibility = :visibility
                WHERE id = :project_id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindValue(':start_date', $data['start_date'], PDO::PARAM_STR);
            $stmt->bindValue(':due_date', $data['due_date'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindValue(':visibility', $data['visibility'], PDO::PARAM_STR);
            $stmt->execute();
            return ['success' => true, 'message' => 'Projet mis à jour avec succès.'];
        } catch (PDOException $e) {
            error_log("Erreur mise à jour projet : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    // Nettoyer les fichiers PDF orphelins (maintenance)
    public function cleanupOrphanedPdfs() {
        $uploadDir = __DIR__ . '/../uploads/pdfs/';
        $cleanedFiles = [];
        $errors = [];
        
        try {
            // Récupérer tous les chemins PDF référencés en base
            $sql = "SELECT pdf_file FROM projects WHERE pdf_file IS NOT NULL AND pdf_file != ''";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $referencedFiles = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Convertir en chemins complets
            $referencedPaths = array_map(function($file) use ($uploadDir) {
                return $uploadDir . basename($file);
            }, $referencedFiles);
            
            // Parcourir tous les fichiers PDF du dossier
            if (is_dir($uploadDir)) {
                $files = glob($uploadDir . '*.pdf');
                foreach ($files as $file) {
                    if (!in_array($file, $referencedPaths)) {
                        if (unlink($file)) {
                            $cleanedFiles[] = basename($file);
                        } else {
                            $errors[] = "Impossible de supprimer : " . basename($file);
                        }
                    }
                }
            }
            
            return [
                'success' => true,
                'cleaned_files' => $cleanedFiles,
                'errors' => $errors,
                'message' => count($cleanedFiles) . ' fichier(s) PDF orphelin(s) supprimé(s).'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur nettoyage PDF orphelins : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors du nettoyage des fichiers PDF orphelins.'
            ];
        }
    }

    // Récupérer les membres d'un projet
    public function getProjectMembers($projectId) {
        $sql = "SELECT u.id as user_id, u.username, u.email, u.first_name, u.last_name, 
                       pm.role, pm.joined_at
                FROM project_members pm
                INNER JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :project_id
                ORDER BY pm.joined_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer les tâches d'un projet
    public function getProjectTasks($projectId) {
        $sql = "SELECT t.*, 
                       COUNT(DISTINCT ta.user_id) as assigned_count
                FROM tasks t
                LEFT JOIN task_assignments ta ON t.id = ta.task_id
                WHERE t.project_id = :project_id
                GROUP BY t.id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

