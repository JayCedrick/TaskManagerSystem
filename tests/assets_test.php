<?php

foreach (['../css/style.css', '../js/script.js', '../README.md', '../favicon.svg'] as $relativePath) {
    if (!is_file(__DIR__ . '/' . $relativePath)) {
        throw new RuntimeException($relativePath . ' is not implemented');
    }
}

echo "Asset checks passed\n";
