<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/guards.php';

function flash_set(string $type, string $message): void {
    start_session_if_needed();
    $_SESSION['flash'][$type] = $message;
}

function flash_get(string $type): ?string {
    start_session_if_needed();

    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $msg = (string)$_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return $msg;
}