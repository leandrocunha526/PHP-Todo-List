<?php
require_once __DIR__ . '/../src/functions.php';

$isLogged = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

$messages = flash_get() ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <title>TODO List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand text-white" href="/">TODO List</a>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button id="themeToggle" class="btn btn-sm btn-outline-light">🌓 Tema</button>

            <?php if ($isLogged): ?>
                <span class="text-white small">👤 Olá, <?= htmlspecialchars($userName) ?>!</span>
                <a href="/logout.php" class="btn btn-sm btn-danger">Sair</a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-sm btn-primary">Login</a>
                <a href="/register.php" class="btn btn-sm btn-secondary">Registrar</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <?php if (!empty($messages) && is_array($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="toast text-bg-<?= htmlspecialchars($msg['type']) ?> border-0 shadow" data-bs-delay="3500">
                    <div class="toast-body"><?= htmlspecialchars($msg['message']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- INÍCIO DO CONTEÚDO CENTRALIZADO -->
    <div class="main-wrapper container py-4">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            /* ======== TEMA AUTOMÁTICO + persistência ======== */
            (function () {

                const root = document.documentElement;
                const body = document.body;
                const toggleBtn = document.getElementById("themeToggle");

                const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
                const savedTheme = localStorage.getItem("theme");

                applyTheme(savedTheme || (prefersDark ? "dark" : "light"));

                toggleBtn?.addEventListener("click", () => {
                    const newTheme = body.classList.contains("dark") ? "light" : "dark";
                    applyTheme(newTheme);
                    localStorage.setItem("theme", newTheme);
                });

                function applyTheme(theme) {
                    if (theme === "dark") {
                        body.classList.add("dark");
                        root.setAttribute("data-bs-theme", "dark");
                    } else {
                        body.classList.remove("dark");
                        root.setAttribute("data-bs-theme", "light");
                    }
                }

            })();

            /* ======== Mostrar toasts automaticamente ======== */
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".toast").forEach(el => new bootstrap.Toast(el).show());
            });
        </script>
