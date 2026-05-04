<?php

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $path = __DIR__ . '/../db/click_store.sqlite';
            $needsInit = !file_exists($path);

            self::$connection = new PDO('sqlite:' . $path);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($needsInit) {
                $sql = file_get_contents(__DIR__ . '/../db/init.sql');
                self::$connection->exec($sql);
            }
        }

        return self::$connection;
    }
}
