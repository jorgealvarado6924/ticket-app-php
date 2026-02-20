<?php
declare(strict_types=1);

function start_session_if_needed(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function redirect(string $to): void {
    header("Location: {$to}");
    exit;
}

function require_auth(): void {
    start_session_if_needed();

    if (!isset($_SESSION['user_id'])) {
        redirect("login.php");
    }
}

function require_role(string $role): void {
    require_auth();

    $current = $_SESSION['role'] ?? null;
    if ($current !== $role) {
        http_response_code(403);
        echo "403 Forbidden";
        exit;
    }
}