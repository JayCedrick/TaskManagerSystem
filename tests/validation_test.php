<?php

$validationPath = __DIR__ . '/../includes/validation.php';
if (!is_file($validationPath)) {
    throw new RuntimeException('includes/validation.php is not implemented');
}

require_once $validationPath;

function assert_same(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

$registrationErrors = validate_registration([
    'full_name' => '',
    'username' => '',
    'email' => 'wrong-email',
    'password' => '',
    'confirm_password' => 'different',
]);
assert_same(true, isset($registrationErrors['full_name']), 'full name is required');
assert_same(true, isset($registrationErrors['email']), 'email must be valid');
assert_same(true, isset($registrationErrors['confirm_password']), 'passwords must match');

assert_same([], validate_registration([
    'full_name' => 'Juan Dela Cruz',
    'username' => 'juan',
    'email' => 'juan@example.com',
    'password' => 'secret123',
    'confirm_password' => 'secret123',
]), 'valid registration should have no validation errors');

$taskErrors = validate_task([
    'title' => '',
    'description' => 'Finish the module',
    'due_date' => '2026-09-05',
    'priority' => 'Urgent',
    'status' => 'Pending',
]);
assert_same(true, isset($taskErrors['title']), 'task title is required');
assert_same(true, isset($taskErrors['priority']), 'priority must be allowed');

assert_same([], validate_task([
    'title' => 'Complete PHP Project',
    'description' => 'Finish authentication module',
    'due_date' => '2026-09-05',
    'priority' => 'High',
    'status' => 'Pending',
]), 'valid task should have no validation errors');

echo "Validation tests passed\n";
