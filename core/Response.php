<?php

class Response
{
    public static function success($data = null, int $code = 200): void
    {
        self::json($code, ['status' => 'success', 'data' => $data]);
    }

    public static function error(string $message, int $code = 400): void
    {
        self::json($code, ['status' => 'error', 'message' => $message, 'code' => $code]);
    }

    private static function json(int $code, array $payload): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
