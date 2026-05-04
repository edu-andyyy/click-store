<?php

$users = new UserController();

$router->add('POST', '/api/v1/register', fn() => $users->register());
$router->add('POST', '/api/v1/login', fn() => $users->login());
$router->add('GET', '/api/v1/users', fn() => $users->index());
$router->add('GET', '/api/v1/users/{id}', fn($m) => $users->show((int)$m[1]));
$router->add('PUT', '/api/v1/users/{id}', fn($m) => $users->replace((int)$m[1]));
$router->add('PATCH', '/api/v1/users/{id}', fn($m) => $users->patch((int)$m[1]));
$router->add('DELETE', '/api/v1/users/{id}', fn($m) => $users->destroy((int)$m[1]));
