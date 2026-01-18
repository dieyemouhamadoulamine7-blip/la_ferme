<?php
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            redirect('compte.php');
        } else {
            $error = 'Identifiants incorrects.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}
?>

<h1>Connexion</h1>

<?php if ($error): ?>
    <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" class="form-auth">
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" class="btn-primary">Se connecter</button>
</form>

<p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


