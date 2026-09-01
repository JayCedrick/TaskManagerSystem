<?php

$authPath = __DIR__ . '/../includes/auth.php';
if (!is_file($authPath)) {
    throw new RuntimeException('includes/auth.php is not implemented');
}

require_once $authPath;

function assert_auth(bool $expected, bool $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message);
    }
}

$_SESSION = [];
assert_auth(false, is_logged_in(), 'empty session must be logged out');
$_SESSION['user_id'] = 7;
assert_auth(true, is_logged_in(), 'user id must mark the session as logged in');
if (current_user_id() !== 7) {
    throw new RuntimeException('current_user_id must return the session user id');
}
logout_user();
assert_auth(false, is_logged_in(), 'logout must clear authentication');

echo "Auth tests passed\n";
