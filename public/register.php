<?php
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$name || !$email || !$password || !$password_confirm) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Un compte existe déjà avec cet email.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, created_at) 
                                   VALUES (:name, :email, :password_hash, :role, NOW())');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => $hash,
                'role' => 'client',
            ]);
            $success = 'Compte créé avec succès, vous pouvez maintenant vous connecter.';
        }
    }
}
?>

<h1>Inscription</h1>

<?php if ($error): ?>
    <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="alert success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="post" class="form-auth">
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">

    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required>

    <label for="password_confirm">Confirmer le mot de passe :</label>
    <input type="password" id="password_confirm" name="password_confirm" required>

    <button type="submit" class="btn-primary">Créer mon compte</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


