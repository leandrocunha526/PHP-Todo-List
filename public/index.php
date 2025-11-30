<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_login();

$userId = $_SESSION['user_id'];

$priorityLabels = [
  1 => 'Muito Baixa',
  2 => 'Baixa',
  3 => 'Média',
  4 => 'Alta',
  5 => 'Crítica'
];

$priorityColors = [
  1 => 'secondary',
  2 => 'info',
  3 => 'primary',
  4 => 'warning',
  5 => 'danger'
];

$order = $_GET['order'] ?? 'created_at';
$allowedOrder = ['created_at', 'title', 'priority', 'status'];
if (!in_array($order, $allowedOrder))
  $order = 'created_at';

$dir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

$priorityFilter = $_GET['priority'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$perPage = 5;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$where = "WHERE user_id = ?";
$params = [$userId];

if ($priorityFilter !== '') {
  $where .= " AND priority = ?";
  $params[] = $priorityFilter;
}

if ($statusFilter !== '') {
  $where .= " AND status = ?";
  $params[] = $statusFilter;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM items $where");
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

$sql = "SELECT * FROM items $where ORDER BY $order $dir LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

require __DIR__ . '/header.php';

function sort_link($field, $label)
{
  $currentField = $_GET['order'] ?? 'created_at';
  $currentDir = $_GET['dir'] ?? 'desc';
  $newDir = ($currentField === $field && $currentDir === 'asc') ? 'desc' : 'asc';
  $arrow = $currentField === $field ? ($currentDir === 'asc' ? '⬆' : '⬇') : '';
  return "<a href='?order=$field&dir=$newDir' class='text-decoration-none'>$label $arrow</a>";
}
?>

<div class="d-flex justify-content-between align-items-center mt-3">
  <h2>Minhas Tarefas</h2>
  <a href="/create.php" class="btn btn-success">Nova</a>
</div>

<div class="mt-3 mb-2">
  <strong>Legenda de prioridades:</strong><br>
  <?php foreach ($priorityLabels as $key => $label): ?>
    <span class="badge bg-<?= $priorityColors[$key] ?>"><?= "$key - $label" ?></span>
  <?php endforeach; ?>
</div>

<div class="card p-3 mb-3">
  <form id="filterForm" method="get" class="row g-3 align-items-end">

    <div class="col-md-3">
      <label class="form-label">Prioridade</label>
      <select name="priority" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($priorityLabels as $key => $label): ?>
          <option value="<?= $key ?>" <?= $priorityFilter == $key ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="">Todos</option>
        <option value="pendente" <?= $statusFilter === 'pendente' ? 'selected' : '' ?>>Pendente</option>
        <option value="em progresso" <?= $statusFilter === 'em progresso' ? 'selected' : '' ?>>Em Progresso</option>
        <option value="concluido" <?= $statusFilter === 'concluido' ? 'selected' : '' ?>>Concluído</option>
      </select>
    </div>

    <div class="col-md-3">
      <button class="btn btn-primary w-100" id="filterBtn">Filtrar</button>
    </div>

  </form>
</div>

<table class="table table-hover table-striped align-middle">
  <thead>
    <tr>
      <th><?= sort_link('title', 'Título') ?></th>
      <th><?= sort_link('priority', 'Prioridade') ?></th>
      <th><?= sort_link('status', 'Status') ?></th>
      <th><?= sort_link('created_at', 'Criado em') ?></th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$items): ?>
      <tr>
        <td colspan="5" class="text-center text-muted">Nenhuma tarefa encontrada.</td>
      </tr>
    <?php endif; ?>

    <?php foreach ($items as $it): ?>
      <tr class="task-row" data-task="<?= htmlspecialchars(json_encode($it), ENT_QUOTES) ?>">
        <td><?= htmlspecialchars($it['title']) ?></td>
        <td><span class="badge bg-<?= $priorityColors[$it['priority']] ?>"><?= $priorityLabels[$it['priority']] ?></span>
        </td>
        <td><?= htmlspecialchars($it['status']) ?></td>
        <td><span class="text-muted small"><?= (new DateTime($it['created_at']))->format('d/m/Y H:i'); ?></span></td>
        <td>
          <button class="btn btn-sm btn-outline-secondary open-modal">Detalhes</button>
          <a class="btn btn-sm btn-primary" href="/edit.php?id=<?= $it['id'] ?>">Editar</a>
          <form action="/delete.php" method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= $it['id'] ?>">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <button class="btn btn-sm btn-danger" onclick="return confirm('Confirmar?')">Excluir</button>
        </td>
        </form>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<nav>
  <ul class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link"
          href="?page=<?= $i ?>&order=<?= $order ?>&dir=<?= $dir ?>&priority=<?= $priorityFilter ?>&status=<?= $statusFilter ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

<!-- MODAL -->
<div class="modal fade" id="taskModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 id="modalTitle" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p><strong>Prioridade:</strong> <span id="modalPriority"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Descrição:</strong> <span id="modalDescription"></span></p>
        <p><strong>Criado em:</strong> <span id="modalCreated"></span></p>
        <p><strong>Atualizado:</strong> <span id="modalUpdated"></span></p>
      </div>

      <div class="modal-footer">
        <a id="editLink" class="btn btn-primary">Editar</a>

        <form id="deleteForm" action="/delete.php" method="post" class="d-inline">
          <input type="hidden" name="id" id="deleteId">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <button class="btn btn-danger" onclick="return confirm('Excluir tarefa?')">Excluir</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
  document.querySelectorAll(".task-row").forEach(row => {
    row.addEventListener("click", () => {

      const data = JSON.parse(row.dataset.task);

      document.getElementById("modalTitle").textContent = data.title;
      document.getElementById("modalPriority").textContent = data.priority;
      document.getElementById("modalStatus").textContent = data.status;
      document.getElementById("modalDescription").textContent = data.description;

      document.getElementById("modalCreated").textContent =
        new Date(data.created_at).toLocaleString("pt-BR");

      document.getElementById("modalUpdated").textContent =
        new Date(data.updated_at).toLocaleString("pt-BR");

      document.getElementById("editLink").href = "/edit.php?id=" + data.id;
      document.getElementById("deleteId").value = data.id;

      new bootstrap.Modal(document.getElementById("taskModal")).show();
    });
  });
</script>

<?php require __DIR__ . '/footer.php'; ?>

