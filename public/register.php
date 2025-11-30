<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    // validações server-side
    if (!$name) {
        flash_set('danger', 'O nome é obrigatório.');
        header('Location: /register.php');
        exit;
    }

    if (!$email) {
        flash_set('danger', 'Email inválido.');
        header('Location: /register.php');
        exit;
    }

    if (strlen($password) < 6) {
        flash_set('danger', 'A senha deve ter pelo menos 6 caracteres.');
        header('Location: /register.php');
        exit;
    }

    // checar email existente
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash_set('warning', 'Email já cadastrado.');
        header('Location: /register.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);

    flash_set('success', 'Registro feito com sucesso! Faça login.');
    header('Location: /login.php');
    exit;
}

require __DIR__ . '/header.php';
?>

<h2>Registrar</h2>

<form method="post" class="mt-3 needs-validation" novalidate>
    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input name="name" class="form-control" required>
        <div class="invalid-feedback">O nome é obrigatório.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required>
        <div class="invalid-feedback">Digite um email válido.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Senha</label>
        <input name="password" type="password" class="form-control" required minlength="6">
        <div class="invalid-feedback">A senha deve ter no mínimo 6 caracteres.</div>
    </div>

    <button class="btn btn-primary">Registrar</button>
</form>

<script>
    // validação estilizada do Bootstrap
    (() => {
        const forms = document.querySelectorAll('.needs-validation');
        [...forms].forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    })();
</script>

<?php require __DIR__ . '/footer.php'; ?>

