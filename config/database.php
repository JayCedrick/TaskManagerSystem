<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!defined('BASE_URL')) {
    define('BASE_URL', '/student-task-manager');
}

function environment_value(string $name, string $default): string
{
    $value = getenv($name);

    return $value === false || $value === '' ? $default : $value;
}

$dbHost = environment_value('DB_HOST', 'localhost');
$dbUser = environment_value('DB_USER', 'root');
$dbPassword = getenv('DB_PASSWORD');
$dbName = environment_value('DB_NAME', 'student_task_manager');

if ($dbPassword === false) {
    $dbPassword = '';
}

try {
    $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit('Database connection is unavailable. Import database.sql and check the local MySQL service.');
}
