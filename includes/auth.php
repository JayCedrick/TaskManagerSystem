<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function app_url(string $path = ''): string
{
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/student-task-manager';

    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'])
        && filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT) !== false
        && (int) $_SESSION['user_id'] > 0;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . app_url('login.php'));
        exit;
    }
}

function current_user_id(): int
{
    return is_logged_in() ? (int) $_SESSION['user_id'] : 0;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $cookieParams = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $cookieParams['path'],
            $cookieParams['domain'],
            (bool) $cookieParams['secure'],
            (bool) $cookieParams['httponly']
        );
    }

    session_destroy();
}
