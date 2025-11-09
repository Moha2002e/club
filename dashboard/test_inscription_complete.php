<?php
/**
 * Test complet de l'inscription et de l'activation
 */

require_once __DIR__ . '/DAO/UserDAO.php';

$userDAO = new UserDAO();

echo "=== Test complet inscription + activation ===\n\n";

// Données de test
$username = 'testuser' . time();
$email = 'test' . time() . '@example.com';
$password = 'TestPassword123';
$firstName = 'Test';
$lastName = 'User';

echo "1. Inscription...\n";
echo "- Username: $username\n";
echo "- Email: $email\n";
echo "- Password: $password\n\n";

$result = $userDAO->register($username, $email, $password, $firstName, $lastName);

if ($result['success']) {
    echo "✅ Inscription réussie !\n";
    echo "- User ID: " . $result['user_id'] . "\n";
    echo "- Token: " . $result['activation_token'] . "\n";
    echo "- Email: " . $result['email'] . "\n\n";
    
    echo "2. Vérification en base de données...\n";
    try {
        $pdo = Database::getInstance()->getConnection();
        
        $sql = "SELECT id, username, email, activation_token, is_verified, is_active 
                FROM users 
                WHERE id = :user_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $result['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Utilisateur trouvé en base:\n";
            echo "- ID: " . $user['id'] . "\n";
            echo "- Username: " . $user['username'] . "\n";
            echo "- Email: " . $user['email'] . "\n";
            echo "- Token: " . $user['activation_token'] . "\n";
            echo "- Is Verified: " . ($user['is_verified'] ? 'OUI' : 'NON') . "\n";
            echo "- Is Active: " . ($user['is_active'] ? 'OUI' : 'NON') . "\n\n";
            
            echo "3. Test d'activation...\n";
            $activationResult = $userDAO->activateAccount($result['activation_token']);
            
            if ($activationResult['success']) {
                echo "✅ Activation réussie !\n";
                echo "- Message: " . $activationResult['message'] . "\n\n";
                
                echo "4. Vérification après activation...\n";
                $stmt->execute();
                $userAfter = $stmt->fetch();
                
                echo "- Is Verified: " . ($userAfter['is_verified'] ? 'OUI' : 'NON') . "\n";
                echo "- Is Active: " . ($userAfter['is_active'] ? 'OUI' : 'NON') . "\n";
                echo "- Token: " . ($userAfter['activation_token'] ?: 'NULL') . "\n";
                
            } else {
                echo "❌ Erreur d'activation: " . $activationResult['message'] . "\n";
            }
            
        } else {
            echo "❌ Utilisateur non trouvé en base de données\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Erreur d'inscription: " . $result['message'] . "\n";
}
?>

