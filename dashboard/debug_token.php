<?php
/**
 * Script de debug pour vérifier le token d'activation
 */

require_once __DIR__ . '/DAO/UserDAO.php';

$userDAO = new UserDAO();

echo "=== Debug du token d'activation ===\n\n";

// Token de test (remplacez par le token que vous avez reçu)
$token = $_GET['token'] ?? '1f6185a2f67f44760d73e68ecb1d7c3389df17878612ccf8144fcc41cb5baa2f';

echo "Token à vérifier: $token\n\n";

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Vérifier si le token existe dans la base
    $sql = "SELECT id, username, email, first_name, last_name, activation_token, otp_expires_at, is_verified, is_active 
            FROM users 
            WHERE activation_token = :token";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ Utilisateur trouvé:\n";
        echo "- ID: " . $user['id'] . "\n";
        echo "- Username: " . $user['username'] . "\n";
        echo "- Email: " . $user['email'] . "\n";
        echo "- First Name: " . $user['first_name'] . "\n";
        echo "- Token: " . $user['activation_token'] . "\n";
        echo "- Expires: " . $user['otp_expires_at'] . "\n";
        echo "- Is Verified: " . ($user['is_verified'] ? 'OUI' : 'NON') . "\n";
        echo "- Is Active: " . ($user['is_active'] ? 'OUI' : 'NON') . "\n\n";
        
        // Tester l'activation
        echo "🧪 Test de l'activation:\n";
        $result = $userDAO->activateAccount($token);
        echo "- Success: " . ($result['success'] ? 'OUI' : 'NON') . "\n";
        echo "- Message: " . $result['message'] . "\n";
        
    } else {
        echo "❌ Aucun utilisateur trouvé avec ce token\n";
        
        // Vérifier tous les utilisateurs non vérifiés
        echo "\n📋 Utilisateurs non vérifiés:\n";
        $sql = "SELECT id, username, email, activation_token, is_verified FROM users WHERE is_verified = 0";
        $stmt = $pdo->query($sql);
        $users = $stmt->fetchAll();
        
        foreach ($users as $u) {
            echo "- ID: {$u['id']}, Email: {$u['email']}, Token: " . substr($u['activation_token'], 0, 20) . "...\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
