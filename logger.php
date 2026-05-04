<?php

function writeLog(string $login, string $action): void
{
    writeLogLine('auth.log', [
        'login' => $login,
        'action' => $action,
    ]);
}

function writeApiLog(string $endpoint, string $method, string $action, string $details = ''): void
{
    $fields = [
        'method' => $method,
        'endpoint' => $endpoint,
        'action' => $action,
    ];
    if ($details !== '') {
        $fields['details'] = $details;
    }
    writeLogLine('api.log', $fields);
}

function writeLogLine(string $filename, array $fields): void
{
    $dir = __DIR__ . '/logs';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $parts = [$time, 'ip=' . $ip];
    foreach ($fields as $key => $value) {
        $parts[] = $key . '=' . $value;
    }

    $line = implode(' | ', $parts) . PHP_EOL;

    file_put_contents($dir . '/' . $filename, $line, FILE_APPEND);
}
