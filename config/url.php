<?php

function normalize_base_url(string $baseUrl): string
{
    $baseUrl = trim(str_replace('\\', '/', $baseUrl));

    if ($baseUrl === '' || $baseUrl === '/') {
        return '';
    }

    return '/' . trim($baseUrl, '/');
}

function resolve_base_url(
    string $appRoot,
    string $documentRoot,
    string $configuredBaseUrl = '',
    string $fallback = '/student-task-manager'
): string {
    if (trim($configuredBaseUrl) !== '') {
        return normalize_base_url($configuredBaseUrl);
    }

    $appRoot = realpath($appRoot) ?: $appRoot;
    $documentRoot = realpath($documentRoot) ?: $documentRoot;
    $appRoot = rtrim(str_replace('\\', '/', $appRoot), '/');
    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

    if ($documentRoot !== '' && ($appRoot === $documentRoot || str_starts_with($appRoot, $documentRoot . '/'))) {
        return normalize_base_url(substr($appRoot, strlen($documentRoot)));
    }

    return normalize_base_url($fallback);
}

if (!defined('BASE_URL')) {
    $configuredBaseUrl = getenv('BASE_URL');
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

    define(
        'BASE_URL',
        resolve_base_url(
            __DIR__ . '/..',
            $documentRoot,
            $configuredBaseUrl === false ? '' : $configuredBaseUrl
        )
    );
}

function app_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
