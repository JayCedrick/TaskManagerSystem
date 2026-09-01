<?php

$pageTitle = $page_title ?? 'Student Task Manager';

if (!function_exists('escape_html')) {
    function escape_html(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape_html($pageTitle) ?> | Student Task Manager</title>
    <link rel="icon" type="image/svg+xml" href="<?= escape_html(app_url('favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= escape_html(app_url('css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?= escape_html(app_url(is_logged_in() ? 'dashboard.php' : 'login.php')) ?>">
            Student Task Manager
        </a>
        <nav class="site-nav" aria-label="Primary navigation">
            <?php if (is_logged_in()): ?>
                <a href="<?= escape_html(app_url('dashboard.php')) ?>">Dashboard</a>
                <a href="<?= escape_html(app_url('tasks/index.php')) ?>">My Tasks</a>
                <a class="button button--small" href="<?= escape_html(app_url('tasks/create.php')) ?>">Add Task</a>
                <a href="<?= escape_html(app_url('logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= escape_html(app_url('login.php')) ?>">Login</a>
                <a class="button button--small" href="<?= escape_html(app_url('register.php')) ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container page-content">
