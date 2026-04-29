<?php

function writeLog($login, $action) {
    $dir = __DIR__ . '/logs';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $file = $dir . '/auth.log';
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $line = $time
        . " | ip=" . $ip
        . " | login=" . $login
        . " | action=" . $action;

    $line .= PHP_EOL;

    file_put_contents($file, $line, FILE_APPEND);
}
