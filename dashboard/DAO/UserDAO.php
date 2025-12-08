<?php
require_once __DIR__ . '/Database.php';

class UserDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Valider un email
     */
    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valider un mot de passe
     * Min 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre
     */
    public function isValidPassword($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }

    /**
     * Vérifier si un username existe déjà
     */
    public function usernameExists($username) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifier si un email existe déjà
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register($username, $email, $password, $firstName, $lastName) {
        // Vérifier si le username existe
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà utilisé.'];
        }

        // Vérifier si l'email existe
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Générer un token d'activation unique
        $activationToken = bin2hex(random_bytes(32));
        $activationExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours')); // Expire dans 24 heures

        error_log("Inscription - Token généré: $activationToken");
        error_log("Inscription - Email: $email");
        error_log("Inscription - Expires: $activationExpiresAt");

        // Insérer l'utilisateur avec token d'activation
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, is_active, activation_token, otp_expires_at, is_verified) 
                VALUES (:username, :email, :password, :first_name, :last_name, 'student', 0, :activation_token, :activation_expires_at, 0)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
            $stmt->bindValue(':activation_token', $activationToken, PDO::PARAM_STR);
            $stmt->bindValue(':activation_expires_at', $activationExpiresAt, PDO::PARAM_STR);
            $stmt->execute();

            $userId = $this->pdo->lastInsertId();
            
            error_log("Inscription - Utilisateur créé avec ID: $userId");

            return [
                'success' => true, 
                'message' => 'Inscription réussie ! Un email d\'activation a été envoyé.',
                'user_id' => $userId,
                'activation_token' => $activationToken,
                'email' => $email,
                'first_name' => $firstName
            ];
        } catch (PDOException $e) {
            error_log("Erreur inscription : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'];
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login($usernameOrEmail, $password) {
        // Rechercher par username ou email
        $sql = "SELECT * FROM users 
                WHERE (username = :identifier1 OR email = :identifier2) 
                AND is_active = 1 
                AND is_verified = 1 
                LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':identifier1', $usernameOrEmail, PDO::PARAM_STR);
            $stmt->bindValue(':identifier2', $usernameOrEmail, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            // Debug
            error_log("Login attempt for: " . $usernameOrEmail);
            error_log("User found: " . ($user ? "YES (ID: " . $user['id'] . ")" : "NO"));

            if (!$user) {
                // Vérifier si l'utilisateur existe mais n'est pas vérifié
                $unverifiedSql = "SELECT id, email, first_name FROM users 
                                 WHERE (username = :identifier1 OR email = :identifier2) 
                                 AND is_verified = 0 
                                 LIMIT 1";
                $unverifiedStmt = $this->pdo->prepare($unverifiedSql);
                $unverifiedStmt->bindValue(':identifier1', $usernameOrEmail, PDO::PARAM_STR);
                $unverifiedStmt->bindValue(':identifier2', $usernameOrEmail, PDO::PARAM_STR);
                $unverifiedStmt->execute();
                $unverifiedUser = $unverifiedStmt->fetch();
                
                if ($unverifiedUser) {
                    return [
                        'success' => false, 
                        'message' => 'Votre compte n\'est pas encore vérifié. Veuillez vérifier votre email et entrer le code OTP.',
                        'needs_verification' => true,
                        'user_id' => $unverifiedUser['id']
                    ];
                }
                
                return ['success' => false, 'message' => 'Identifiants incorrects.'];
            }

            // Debug password
            error_log("Password verify: " . (password_verify($password, $user['password']) ? "SUCCESS" : "FAILED"));

            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Mettre à jour la dernière connexion
                $this->updateLastLogin($user['id']);

                return [
                    'success' => true, 
                    'message' => 'Connexion réussie !',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'role' => $user['role']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Identifiants incorrects.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur connexion : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la connexion.'];
        }
    }

    /**
     * Mettre à jour la dernière connexion
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET updated_at = NOW() WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur update last login : " . $e->getMessage());
        }
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getUserById($userId) {
        $sql = "SELECT id, username, email, first_name, last_name, role, theme_preference, created_at, is_active 
                FROM users WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur getUserById : " . $e->getMessage());
            return null;
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT id, username, email, first_name, last_name, role, theme_preference, created_at, is_active 
                FROM users WHERE email = :email LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur getUserByEmail : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier le code OTP et activer le compte
     */
    public function verifyOTP($userId, $otpCode) {
        $sql = "SELECT id, otp_code, otp_expires_at, is_verified 
                FROM users 
                WHERE id = :user_id AND otp_code = :otp_code AND is_verified = 0";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':otp_code', $otpCode, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Code OTP invalide ou compte déjà vérifié.'];
            }

            // Vérifier si le code n'a pas expiré
            if (strtotime($user['otp_expires_at']) < time()) {
                return ['success' => false, 'message' => 'Le code OTP a expiré. Veuillez demander un nouveau code.'];
            }

            // Activer le compte
            $updateSql = "UPDATE users 
                          SET is_verified = 1, is_active = 1, otp_code = NULL, otp_expires_at = NULL 
                          WHERE id = :user_id";
            
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $updateStmt->execute();

            return ['success' => true, 'message' => 'Compte activé avec succès ! Vous pouvez maintenant vous connecter.'];
            
        } catch (PDOException $e) {
            error_log("Erreur verifyOTP : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la vérification. Veuillez réessayer.'];
        }
    }

    /**
     * Régénérer un code OTP
     */
    public function activateAccount($activationToken) {
        error_log("activateAccount appelé avec token: " . $activationToken);
        
        $sql = "SELECT id, first_name, last_name, email, otp_expires_at, is_verified 
                FROM users 
                WHERE activation_token = :activation_token";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':activation_token', $activationToken, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            error_log("Utilisateur trouvé: " . json_encode($user));

            if (!$user) {
                error_log("Aucun utilisateur trouvé avec ce token");
                return ['success' => false, 'message' => 'Token d\'activation invalide.'];
            }

            if ($user['is_verified'] == 1) {
                error_log("Compte déjà vérifié");
                return ['success' => false, 'message' => 'Compte déjà activé.'];
            }

            // Vérifier si le token n'a pas expiré
            if ($user['otp_expires_at'] && strtotime($user['otp_expires_at']) < time()) {
                error_log("Token expiré: " . $user['otp_expires_at']);
                return ['success' => false, 'message' => 'Le lien d\'activation a expiré. Veuillez demander un nouveau lien.'];
            }

            // Activer le compte
            $updateSql = "UPDATE users 
                          SET is_verified = 1, is_active = 1, activation_token = NULL, otp_expires_at = NULL 
                          WHERE id = :user_id";
            
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
            $result = $updateStmt->execute();
            
            error_log("Mise à jour effectuée: " . ($result ? 'OUI' : 'NON') . ", lignes affectées: " . $updateStmt->rowCount());

            return [
                'success' => true, 
                'message' => 'Compte activé avec succès ! Vous pouvez maintenant vous connecter.',
                'user' => $user
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur activateAccount : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de l\'activation. Veuillez réessayer.'];
        }
    }
    // Récupérer tous les membres avec filtres
    public function getAllMembers($roleFilter = '', $projectFilter = '', $searchQuery = '', $statusFilter = '', $sortBy = 'name') {
        $conditions = [];
        $params = [];
        
        // Filtre par projet - utiliser une sous-requête pour éviter les doublons
        if (!empty($projectFilter) && $projectFilter !== '') {
            $conditions[] = "u.id IN (SELECT user_id FROM project_members WHERE project_id = :project_id)";
            $params[':project_id'] = $projectFilter;
        }
        
        // Filtre par rôle
        if (!empty($roleFilter) && $roleFilter !== '') {
            $conditions[] = "u.role = :role";
            $params[':role'] = $roleFilter;
        }
        
        // Filtre par statut
        if (!empty($statusFilter) && $statusFilter !== '') {
            if ($statusFilter === 'active') {
                $conditions[] = "u.is_active = 1";
            } elseif ($statusFilter === 'inactive') {
                $conditions[] = "u.is_active = 0";
            }
        }
        
        // Recherche (insensible à la casse)
        if (!empty($searchQuery) && $searchQuery !== '') {
            $conditions[] = "(LOWER(u.first_name) LIKE :search OR LOWER(u.last_name) LIKE :search OR LOWER(u.email) LIKE :search OR LOWER(u.username) LIKE :search)";
            $params[':search'] = "%" . strtolower($searchQuery) . "%";
        }
        
        $sql = "SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.role, u.created_at, u.is_active, YEAR(u.created_at) as joined_year 
                FROM users u";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        // Tri
        switch ($sortBy) {
            case 'name':
                $sql .= " ORDER BY u.first_name ASC, u.last_name ASC";
                break;
            case 'created':
                $sql .= " ORDER BY u.created_at DESC";
                break;
            case 'role':
                $sql .= " ORDER BY u.role ASC, u.first_name ASC";
                break;
            default:
                $sql .= " ORDER BY u.first_name ASC, u.last_name ASC";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Compter les projets d'un utilisateur
    public function countUserProjects($userId) {
        $sql = "SELECT COUNT(*) as count FROM project_members WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    // Compter les tâches d'un utilisateur
    public function countUserTasks($userId) {
        $sql = "SELECT COUNT(*) as count FROM task_assignments WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    // Activer/Désactiver un utilisateur
    public function toggleUserStatus($userId) {
        try {
            $sql = "UPDATE users SET is_active = NOT is_active WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            // Vérifier le nouveau statut
            $sql = "SELECT is_active FROM users WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $user = $stmt->fetch();
            
            $status = $user['is_active'] ? 'activé' : 'désactivé';
            return ['success' => true, 'message' => "Compte $status avec succès."];
        } catch (PDOException $e) {
            error_log("Erreur toggle status : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la modification du statut.'];
        }
    }

    // Supprimer un utilisateur
    public function deleteUser($userId) {
        try {
            $sql = "DELETE FROM users WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Utilisateur supprimé avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Utilisateur introuvable.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur delete user : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression.'];
        }
    }

    // Mettre à jour un utilisateur (depuis son propre profil - sans modifier le rôle)
    public function updateUser($userId, $data) {
        try {
            $sql = "UPDATE users SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email
                    WHERE id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':email' => $data['email']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Profil mis à jour avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur update user : " . $e->getMessage());
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    // Mettre à jour un utilisateur par un admin (peut modifier le rôle)
    public function updateUserByAdmin($userId, $data) {
        try {
            $sql = "UPDATE users SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    role = :role
                    WHERE id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':email' => $data['email'],
                ':role' => $data['role']
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Membre mis à jour avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur update user by admin : " . $e->getMessage());
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour.'];
        }
    }

    /**
     * Mettre à jour le thème de l'utilisateur
     */
    public function updateUserTheme($userId, $theme) {
        try {
            $sql = "UPDATE users SET theme_preference = :theme WHERE id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':theme' => $theme
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Thème mis à jour avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour du thème.'];
            }
        } catch (PDOException $e) {
            error_log("Erreur updateUserTheme : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour du thème.'];
        }
    }

    /**
     * Générer un token de réinitialisation de mot de passe
     */
    public function generatePasswordResetToken($email) {
        try {
            // Vérifier si l'utilisateur existe
            $sql = "SELECT id, first_name, last_name, email FROM users WHERE email = :email AND is_verified = 1 LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                // Pour des raisons de sécurité, on ne révèle pas si l'email existe ou non
                return ['success' => false, 'message' => 'Utilisateur non trouvé.'];
            }

            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valide 1 heure

            // Enregistrer le token dans la base de données
            $updateSql = "UPDATE users 
                          SET activation_token = :token, otp_expires_at = :expires_at 
                          WHERE id = :user_id";
            
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindValue(':token', $token, PDO::PARAM_STR);
            $updateStmt->bindValue(':expires_at', $expiresAt, PDO::PARAM_STR);
            $updateStmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
            $updateStmt->execute();

            return [
                'success' => true,
                'token' => $token,
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'message' => 'Token généré avec succès.'
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur generatePasswordResetToken : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la génération du token.'];
        }
    }

    /**
     * Valider un token de réinitialisation de mot de passe
     */
    public function validateResetToken($token) {
        try {
            $sql = "SELECT id, first_name, last_name, email, otp_expires_at 
                    FROM users 
                    WHERE activation_token = :token AND is_verified = 1 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Token invalide.'];
            }

            // Vérifier si le token n'a pas expiré
            if ($user['otp_expires_at'] && strtotime($user['otp_expires_at']) < time()) {
                return ['success' => false, 'message' => 'Le token a expiré.'];
            }

            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Erreur validateResetToken : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la validation du token.'];
        }
    }

    /**
     * Réinitialiser le mot de passe avec un token
     */
    public function resetPassword($token, $newPassword) {
        try {
            // Valider le token
            $validation = $this->validateResetToken($token);
            
            if (!$validation['success']) {
                return ['success' => false, 'message' => 'Token invalide ou expiré.'];
            }

            $userId = $validation['user']['id'];

            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe et supprimer le token
            $sql = "UPDATE users 
                    SET password = :password, activation_token = NULL, otp_expires_at = NULL 
                    WHERE id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la réinitialisation du mot de passe.'];
            }
            
        } catch (PDOException $e) {
            error_log("Erreur resetPassword : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la réinitialisation du mot de passe.'];
        }
    }
}
?>

