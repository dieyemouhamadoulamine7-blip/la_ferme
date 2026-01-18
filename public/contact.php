<?php
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message, created_at) 
                               VALUES (:name, :email, :message, NOW())');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ]);
        $success = 'Merci pour votre message, nous vous répondrons rapidement.';
    }
}
?>

<h1>Contact</h1>

<?php if ($error): ?>
    <p class="alert error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="alert success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<form method="post" class="form-contact">
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">

    <label for="message">Message :</label>
    <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>

    <button type="submit" class="btn-primary">Envoyer</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


