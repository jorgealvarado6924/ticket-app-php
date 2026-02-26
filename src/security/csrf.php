<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/guards.php';

/**
 * Genera (si no existe) y devuelve el token CSRF de la sesión
 */
function csrf_token(): string {
    start_session_if_needed();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

/**
 * Devuelve el input hidden listo para meter en formularios
 */
function csrf_input(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Valida el token recibido por POST contra el token de sesión
 */
function csrf_validate_or_die(): void {
    start_session_if_needed();

    $sent = $_POST['csrf_token'] ?? '';
    $session = $_SESSION['csrf_token'] ?? '';

    if (!is_string($sent) || !is_string($session) || $sent === '' || $session === '') {
        http_response_code(403);
        exit('403 CSRF token missing');
    }

    if (!hash_equals($session, $sent)) {
        http_response_code(403);
        exit('403 CSRF token invalid');
    }
}