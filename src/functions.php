<?php
session_start();

/* -------------------- FLASH MESSAGES -------------------- */

function flash_set(string $type, string $message): void
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message,
        'time' => time()
    ];
}

function flash_get(): array
{
    if (!isset($_SESSION['flash'])) {
        return [];
    }

    $messages = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $messages;
}

// atalhos úteis
function flash_success(string $msg): void
{
    flash_set('success', $msg);
}
function flash_error(string $msg): void
{
    flash_set('danger', $msg);
}
function flash_warning(string $msg): void
{
    flash_set('warning', $msg);
}


/* -------------------- AUTH -------------------- */

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

/* -------------------- CSRF -------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $token): bool
{
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }

    $valid = hash_equals($_SESSION['csrf_token'], $token);

    // opcional: renovar token após validação para aumentar segurança
    if ($valid) {
        unset($_SESSION['csrf_token']);
    }

    return $valid;
}


/* -------------------- HELPERS -------------------- */

function redirect(string $url): never
{
    header("Location: $url");
    exit;
}

function safe_input(?string $value): string
{
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

