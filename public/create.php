<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!csrf_verify($_POST['csrf'] ?? '')) {
    flash_set('danger', 'Token CSRF inválido');
    header('Location: /create.php');
    exit;
  }

  $title = trim($_POST['title'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $priority = (int) ($_POST['priority'] ?? 1);
  $status = $_POST['status'] ?? 'pendente';

  // --- VALIDAÇÕES DO SERVIDOR ---
  $errors = [];

  if (!$title) {
    $errors[] = 'O título é obrigatório.';
  }

  if (strlen($title) > 100) {
    $errors[] = 'O título deve ter no máximo 100 caracteres.';
  }

  if ($priority < 1 || $priority > 5) {
    $errors[] = 'A prioridade deve estar entre 1 e 5.';
  }

  if (!in_array($status, ['Pendente', 'Em progresso', 'Concluido'])) {
    $errors[] = 'Status inválido.';
  }

  if ($errors) {
    flash_set('danger', implode("<br>", $errors));
    header('Location: /create.php');
    exit;
  }

  // --- INSERÇÃO NO BANCO ---
  $stmt = $pdo->prepare("INSERT INTO items (title, description, priority, status, user_id) VALUES (?,?,?,?,?)");
  $stmt->execute([$title, $desc, $priority, $status, $_SESSION['user_id']]);

  flash_set('success', 'Tarefa criada com sucesso!');
  header('Location: /index.php');
  exit;
}

require __DIR__ . '/header.php';
?>

<h2 class="mb-3">Nova tarefa</h2>

<form method="post" id="taskForm" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <div class="mb-3">
    <label class="form-label">Título</label>
    <input name="title" class="form-control" required maxlength="100">
    <div class="invalid-feedback">O título é obrigatório e deve ter até 100 caracteres.</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control"></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Prioridade</label>
    <input name="priority" type="number" min="1" max="5" value="1" class="form-control" style="width:120px" required>
    <div class="invalid-feedback">Escolha um valor entre 1 e 5.</div>

    <!-- LEGENDA DE PRIORIDADE -->
    <small class="form-text text-muted">
      <strong>Legenda:</strong>
      <span class="text-success">1 = Baixa</span> •
      <span class="text-primary">2 = Normal</span> •
      <span class="text-warning">3 = Média</span> •
      <span class="text-orange">4 = Alta</span> •
      <span class="text-danger fw-bold">5 = Urgente</span>
    </small>
  </div>

  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option value="Pendente">Pendente</option>
      <option value="Em progresso">Em progresso</option>
      <option value="Concluido">Concluído</option>
    </select>
  </div>

  <button class="btn btn-success">Salvar</button>
</form>

<script>
  // --- VALIDAÇÃO CLIENT-SIDE BOOTSTRAP ---
  document.getElementById('taskForm').addEventListener('submit', function (e) {
    if (!this.checkValidity()) {
      e.preventDefault();
      this.classList.add('was-validated');
    }
  });
</script>

<?php require __DIR__ . '/footer.php'; ?>

