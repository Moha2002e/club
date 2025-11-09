<?php
require_once __DIR__ . '/conf/database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Vérifier si la colonne existe déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM projects LIKE 'pdf_file'");
    if ($stmt->rowCount() == 0) {
        // Ajouter la colonne pdf_file
        $sql = "ALTER TABLE projects ADD COLUMN pdf_file VARCHAR(255) NULL AFTER due_date";
        $pdo->exec($sql);
        echo "✅ Colonne 'pdf_file' ajoutée avec succès à la table 'projects'.\n";
    } else {
        echo "ℹ️ La colonne 'pdf_file' existe déjà dans la table 'projects'.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur lors de l'ajout de la colonne : " . $e->getMessage() . "\n";
}
?>
