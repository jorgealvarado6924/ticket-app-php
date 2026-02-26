<?php
declare(strict_types=1);

function start_session_if_needed(): void {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    // Hardening básico (seguro en local y producción)
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    // Cuando tengas HTTPS: ini_set('session.cookie_secure', '1');

    session_start();

    // Timeout por inactividad (30 min)
    $timeoutSeconds = 30 * 60;
    $now = time();

    if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > $timeoutSeconds) {
        session_unset();
        session_destroy();

        session_start(); // nueva sesión limpia
    }

    $_SESSION['last_activity'] = $now;
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