<?php

$urlPath = __DIR__ . '/../config/url.php';
if (!is_file($urlPath)) {
    throw new RuntimeException('config/url.php is not implemented');
}

require_once $urlPath;

function assert_base_url(string $expected, string $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

assert_base_url(
    '/student-task-manager',
    resolve_base_url('/var/www/student-task-manager', '/var/www'),
    'nested application should use its document-root-relative path'
);
assert_base_url(
    '',
    resolve_base_url('/var/www/student-task-manager', '/var/www/student-task-manager'),
    'application at the document root should use root-relative URLs'
);
assert_base_url(
    '/student-task-manager',
    resolve_base_url('/var/www/student-task-manager', ''),
    'missing document root should use the safe fallback'
);
assert_base_url(
    '/custom-app',
    resolve_base_url('/var/www/student-task-manager', '/var/www', '/custom-app/'),
    'explicit base URL should take precedence'
);
assert_base_url(
    '/student-task-manager',
    resolve_base_url('/var/www/student-task-manager-extra', '/var/www/student-task-manager'),
    'similar but unrelated paths should use the fallback'
);

echo "Base URL tests passed\n";
