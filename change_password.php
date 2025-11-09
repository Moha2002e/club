<?php
// Temporary password change page. Delete after use.
require_once __DIR__ . '/dashboard/DAO/Database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
	$newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

	if ($userId <= 0) {
		$message = "ID utilisateur invalide.";
		$messageType = 'error';
	} elseif (strlen($newPassword) < 8) {
		$message = "Le mot de passe doit contenir au moins 8 caractères.";
		$messageType = 'error';
	} else {
		try {
			$pdo = Database::getInstance()->getConnection();
			$hashed = password_hash($newPassword, PASSWORD_BCRYPT);
			$sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
			$stmt = $pdo->prepare($sql);
			$stmt->bindValue(':password', $hashed, PDO::PARAM_STR);
			$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
			$stmt->execute();

			if ($stmt->rowCount() > 0) {
				$message = "Mot de passe mis à jour pour l'utilisateur ID ${userId}.";
				$messageType = 'success';
			} else {
				$message = "Aucune mise à jour effectuée. Vérifiez que l'utilisateur ID ${userId} existe.";
				$messageType = 'warning';
			}
		} catch (Exception $e) {
			$message = "Erreur: " . htmlspecialchars($e->getMessage());
			$messageType = 'error';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Changer mot de passe (temporaire)</title>
	<style>
		body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background: #f5f5f7; margin: 0; padding: 24px; }
		.container { max-width: 520px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,0.08); }
		h1 { font-size: 20px; margin: 0 0 16px; }
		p.note { color: #666; font-size: 13px; margin-top: 0; }
		.form-row { margin-bottom: 14px; }
		label { display: block; margin-bottom: 6px; font-weight: 600; }
		input[type="number"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #d0d7de; border-radius: 8px; font-size: 14px; }
		button { display: inline-block; padding: 10px 14px; background: #0d6efd; color: #fff; border: 0; border-radius: 8px; cursor: pointer; font-weight: 600; }
		.alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
		.alert.success { background: #e7f6ee; color: #0a6c3e; border: 1px solid #b7e2c8; }
		.alert.error { background: #fde8e8; color: #b42318; border: 1px solid #f5c2c7; }
		.alert.warning { background: #fff4e5; color: #8a5a00; border: 1px solid #ffe0b2; }
		footer { margin-top: 16px; font-size: 12px; color: #888; }
	</style>
</head>
<body>
	<div class="container">
		<h1>Changer le mot de passe (temporaire)</h1>
		<p class="note">Page temporaire pour modifier le mot de passe d'un utilisateur par son ID. Pensez à supprimer ce fichier après usage.</p>
		<?php if (!empty($message)) : ?>
			<div class="alert <?php echo htmlspecialchars($messageType); ?>"><?php echo $message; ?></div>
		<?php endif; ?>
		<form method="post">
			<div class="form-row">
				<label for="user_id">ID utilisateur</label>
				<input type="number" id="user_id" name="user_id" min="1" required />
			</div>
			<div class="form-row">
				<label for="new_password">Nouveau mot de passe</label>
				<input type="password" id="new_password" name="new_password" minlength="8" required />
			</div>
			<button type="submit">Mettre à jour</button>
		</form>
		<footer>Security: mot de passe hashé avec bcrypt via password_hash().</footer>
	</div>
</body>
</html>


