<?php

$tests = [
    __DIR__ . '/validation_test.php',
    __DIR__ . '/auth_test.php',
    __DIR__ . '/base_url_test.php',
    __DIR__ . '/ownership_source_test.php',
    __DIR__ . '/assets_test.php',
];
$failures = 0;

foreach ($tests as $test) {
    echo 'Running ' . basename($test) . "\n";
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($test), $exitCode);
    if ($exitCode !== 0) {
        $failures++;
    }
}

if ($failures > 0) {
    fwrite(STDERR, $failures . " test script(s) failed\n");
    exit(1);
}

echo "All deterministic tests passed\n";
