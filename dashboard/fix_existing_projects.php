<?php
/**
 * Script pour ajouter automatiquement les propriétaires comme membres de leurs projets
 */

require_once __DIR__ . '/DAO/Database.php';

echo "<h1>Correction des Projets Existants</h1>";

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Récupérer tous les projets
    $sql = "SELECT id, title, owner_id FROM projects";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Projets trouvés : " . count($projects) . "</h2>";
    
    $fixedCount = 0;
    $alreadyMemberCount = 0;
    
    foreach ($projects as $project) {
        echo "<h3>Projet : " . htmlspecialchars($project['title']) . " (ID: " . $project['id'] . ")</h3>";
        
        // Vérifier si le propriétaire est déjà membre
        $checkSql = "SELECT COUNT(*) as count FROM project_members 
                     WHERE project_id = :project_id AND user_id = :user_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':project_id' => $project['id'],
            ':user_id' => $project['owner_id']
        ]);
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            echo "<p style='color: orange;'>⚠️ Propriétaire déjà membre</p>";
            $alreadyMemberCount++;
        } else {
            // Ajouter le propriétaire comme membre
            $insertSql = "INSERT INTO project_members (project_id, user_id, role) 
                          VALUES (:project_id, :user_id, 'owner')";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':project_id' => $project['id'],
                ':user_id' => $project['owner_id']
            ]);
            
            echo "<p style='color: green;'>✅ Propriétaire ajouté comme membre</p>";
            $fixedCount++;
        }
    }
    
    echo "<h2>Résumé :</h2>";
    echo "<ul>";
    echo "<li style='color: green;'>✅ Projets corrigés : $fixedCount</li>";
    echo "<li style='color: orange;'>⚠️ Déjà membres : $alreadyMemberCount</li>";
    echo "<li>📊 Total : " . count($projects) . "</li>";
    echo "</ul>";
    
    if ($fixedCount > 0) {
        echo "<p style='color: green;'><strong>✅ Correction terminée ! Les propriétaires peuvent maintenant ajouter des tâches.</strong></p>";
    } else {
        echo "<p style='color: blue;'><strong>ℹ️ Aucune correction nécessaire. Tous les propriétaires sont déjà membres.</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Erreur :</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Actions :</h2>";
echo "<p><a href='index.php?page=projects'>Retour aux Projets</a></p>";
echo "<p><a href='index.php?page=dashboard'>Retour au Dashboard</a></p>";
?>
