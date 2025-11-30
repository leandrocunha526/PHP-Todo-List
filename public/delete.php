<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        flash_set('danger', 'CSRF');
        header('Location:/index.php');
        exit;
    }
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    flash_set('success', 'Excluído');
    header('Location: /index.php');
    exit;
}
header('Location: /index.php');
exit;
