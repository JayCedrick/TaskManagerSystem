<?php

$taskFiles = [
    __DIR__ . '/../dashboard.php',
    __DIR__ . '/../tasks/index.php',
    __DIR__ . '/../tasks/create.php',
    __DIR__ . '/../tasks/edit.php',
    __DIR__ . '/../tasks/delete.php',
];

foreach ($taskFiles as $taskFile) {
    if (!is_file($taskFile)) {
        throw new RuntimeException(basename($taskFile) . ' is not implemented');
    }

    $source = file_get_contents($taskFile);
    if (!str_contains($source, 'current_user_id') || !str_contains($source, 'user_id')) {
        throw new RuntimeException(basename($taskFile) . ' is missing the user ownership contract');
    }
}

echo "Ownership source checks passed\n";
