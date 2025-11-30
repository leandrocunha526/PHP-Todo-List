<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$item = $stmt->fetch();

if (!$item) {
    flash_set('danger', 'Item não encontrado.');
    header('Location:/index.php');
    exit;
}

$error = "";

// ----- PROCESSAMENTO FORM -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify($_POST['csrf'] ?? '')) {
        $error = "Token CSRF inválido.";
    } else {

        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $priority = (int) ($_POST['priority'] ?? 1);
        $status = $_POST['status'] ?? 'pendente';

        $validStatus = ['pendente', 'em progresso', 'concluido'];

        // ---- SERVER SIDE VALIDATION ----
        if ($title === '' || strlen($title) < 3) {
            $error = "O título deve ter pelo menos 3 caracteres.";
        } elseif ($priority < 1 || $priority > 5) {
            $error = "A prioridade deve ser um número entre 1 e 5.";
        } elseif (!in_array($status, $validStatus)) {
            $error = "Status inválido.";
        }

        // ---- SE TUDO OK ----
        if (!$error) {
            $stmt = $pdo->prepare("UPDATE items SET title=?, description=?, priority=?, status=?, updated_at=NOW() WHERE id=? AND user_id=?");
            $stmt->execute([$title, $desc, $priority, $status, $id, $_SESSION['user_id']]);
            flash_set('success', 'Tarefa atualizada com sucesso!');
            header("Location: /index.php");
            exit;
        }
    }
}

require __DIR__ . '/header.php';
?>

<h2>Editar Tarefa</h2>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" id="editForm" class="needs-validation mt-3" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Título</label>
    <input name="title" class="form-control" minlength="3" value="<?= htmlspecialchars($item['title']) ?>" required>
    <div class="invalid-feedback">Digite um título com pelo menos 3 caracteres.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Prioridade (1-5)</label>
    <input name="priority" type="number" class="form-control" min="1" max="5" value="<?= $item['priority'] ?>" required style="width:120px">
    <div class="invalid-feedback">A prioridade deve ser entre 1 e 5.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
      <option value="pendente" <?= $item['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
      <option value="em progresso" <?= $item['status'] == 'em progresso' ? 'selected' : '' ?>>Em progresso</option>
      <option value="concluido" <?= $item['status'] == 'concluido' ? 'selected' : '' ?>>Concluído</option>
    </select>
    <div class="invalid-feedback">Selecione um status válido.</div>
  </div>

  <button class="btn btn-primary">Salvar</button>
</form>

<script>
document.getElementById("editForm").addEventListener("submit", function(event) {
    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    this.classList.add("was-validated");
});
</script>

<?php require __DIR__ . '/footer.php'; ?>
