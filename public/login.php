<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

$error = "";

// ---- PROCESSAMENTO DO LOGIN ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validações servidor
    if ($email === '' || $password === '') {
        $error = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Digite um e-mail válido.";
    } else {

        $stmt = $pdo->prepare("SELECT id, password, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            flash_set('success', 'Bem-vindo, ' . $user['name']);
            header('Location: /index.php');
            exit;
        } else {
            $error = "E-mail ou senha incorretos.";
        }
    }
}

require __DIR__ . '/header.php';
?>

<h2>Login</h2>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" id="loginForm" class="mt-3 needs-validation" novalidate>
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required>
        <div class="invalid-feedback">Digite um e-mail válido.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Senha</label>
        <input name="password" type="password" minlength="6" class="form-control" required>
        <div class="invalid-feedback">A senha deve conter no mínimo 6 caracteres.</div>
    </div>

    <button class="btn btn-primary w-100">Entrar</button>
</form>

<script>
// ---- Validação Front-End com Bootstrap ----
document.getElementById("loginForm").addEventListener("submit", function(event) {

    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    this.classList.add("was-validated");
});
</script>

<?php require __DIR__ . '/footer.php'; ?>
