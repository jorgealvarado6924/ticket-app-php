<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/guards.php';

function is_logged_in(): bool {
    start_session_if_needed();
    return isset($_SESSION['user_id']);
}

function current_user(): ?array {
    start_session_if_needed();

    if (!isset($_SESSION['user_id'])) return null;

    return [
        'id' => (int)$_SESSION['user_id'],
        'username' => (string)($_SESSION['username'] ?? ''),
        'role' => (string)($_SESSION['role'] ?? 'user'),
    ];
}

function is_admin(): bool {
    start_session_if_needed();
    return (string)($_SESSION['role'] ?? 'user') === 'admin';
}