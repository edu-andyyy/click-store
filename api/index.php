<?php

$root = dirname(__DIR__);

require_once $root . '/config/database.php';
require_once $root . '/core/Response.php';
require_once $root . '/core/Router.php';
require_once $root . '/models/User.php';
require_once $root . '/controllers/UserController.php';
require_once $root . '/logger.php';

$router = new Router();
require __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);
