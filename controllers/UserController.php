<?php

class UserController
{
    public function index(): void
    {
        Response::success(User::all());
    }

    public function show(int $id): void
    {
        $user = User::find($id);
        if ($user === null) {
            Response::error('Пользователь не найден', 404);
        }
        Response::success($user);
    }

    public function register(): void
    {
        $body = $this->readJsonBody();
        $name = trim((string)($body['name'] ?? ''));
        $email = trim((string)($body['email'] ?? ''));
        $password = (string)($body['password'] ?? '');

        $error = $this->validateUserPayload($name, $email, $password);
        if ($error !== null) {
            writeApiLog('/api/v1/register', 'POST', 'FAIL_VALIDATION', "email=$email reason=\"$error\"");
            Response::error($error, 400);
        }

        if (User::findByEmail($email) !== null) {
            writeApiLog('/api/v1/register', 'POST', 'FAIL_DUPLICATE_EMAIL', "email=$email");
            Response::error('Пользователь с таким email уже существует', 409);
        }

        $user = User::create($name, $email, password_hash($password, PASSWORD_DEFAULT));

        writeApiLog('/api/v1/register', 'POST', 'SUCCESS', "id={$user['id']} email=$email");
        Response::success($user, 201);
    }

    public function login(): void
    {
        $body = $this->readJsonBody();
        $email = trim((string)($body['email'] ?? ''));
        $password = (string)($body['password'] ?? '');

        if ($email === '' || $password === '') {
            writeApiLog('/api/v1/login', 'POST', 'FAIL_VALIDATION', "email=$email");
            Response::error('Поля email и password обязательны', 400);
        }

        $user = User::findByEmailWithHash($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            writeApiLog('/api/v1/login', 'POST', 'FAIL_INVALID_CREDENTIALS', "email=$email");
            Response::error('Неверный email или пароль', 401);
        }

        writeApiLog('/api/v1/login', 'POST', 'SUCCESS', "id={$user['id']} email=$email");

        unset($user['password_hash']);
        Response::success($user);
    }

    public function replace(int $id): void
    {
        $body = $this->readJsonBody();
        $name = trim((string)($body['name'] ?? ''));
        $email = trim((string)($body['email'] ?? ''));

        if (!array_key_exists('name', $body) || !array_key_exists('email', $body)) {
            writeApiLog("/api/v1/users/$id", 'PUT', 'FAIL_VALIDATION', 'reason="PUT requires both name and email"');
            Response::error('PUT требует одновременно поля name и email', 400);
        }
        $nameError = $this->validateName($name);
        if ($nameError !== null) {
            writeApiLog("/api/v1/users/$id", 'PUT', 'FAIL_VALIDATION', "reason=\"$nameError\"");
            Response::error($nameError, 400);
        }
        $emailError = $this->validateEmail($email);
        if ($emailError !== null) {
            writeApiLog("/api/v1/users/$id", 'PUT', 'FAIL_VALIDATION', "reason=\"$emailError\"");
            Response::error($emailError, 400);
        }

        $existing = User::find($id);
        if ($existing === null) {
            writeApiLog("/api/v1/users/$id", 'PUT', 'FAIL_NOT_FOUND');
            Response::error('Пользователь не найден', 404);
        }

        $other = User::findByEmail($email);
        if ($other !== null && (int)$other['id'] !== $id) {
            writeApiLog("/api/v1/users/$id", 'PUT', 'FAIL_DUPLICATE_EMAIL', "email=$email");
            Response::error('Email уже используется другим пользователем', 409);
        }

        User::updateName($id, $name);
        User::updateEmail($id, $email);
        $updated = User::find($id);

        writeApiLog("/api/v1/users/$id", 'PUT', 'SUCCESS', "name=$name email=$email");
        Response::success($updated);
    }

    public function patch(int $id): void
    {
        $body = $this->readJsonBody();

        $newName = null;
        $newEmail = null;
        $newPasswordHash = null;

        if (array_key_exists('name', $body)) {
            $name = trim((string)$body['name']);
            $nameError = $this->validateName($name);
            if ($nameError !== null) {
                writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_VALIDATION', "reason=\"$nameError\"");
                Response::error($nameError, 400);
            }
            $newName = $name;
        }

        if (array_key_exists('email', $body)) {
            $email = trim((string)$body['email']);
            $emailError = $this->validateEmail($email);
            if ($emailError !== null) {
                writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_VALIDATION', "reason=\"$emailError\"");
                Response::error($emailError, 400);
            }
            $newEmail = $email;
        }

        if (array_key_exists('password', $body)) {
            $password = (string)$body['password'];
            $passwordError = $this->validatePassword($password);
            if ($passwordError !== null) {
                writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_VALIDATION', "reason=\"$passwordError\"");
                Response::error($passwordError, 400);
            }
            $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($newName === null && $newEmail === null && $newPasswordHash === null) {
            writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_VALIDATION', 'reason="empty body"');
            Response::error('Не указано ни одного поля для обновления', 400);
        }

        $existing = User::find($id);
        if ($existing === null) {
            writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_NOT_FOUND');
            Response::error('Пользователь не найден', 404);
        }

        if ($newEmail !== null) {
            $other = User::findByEmail($newEmail);
            if ($other !== null && (int)$other['id'] !== $id) {
                writeApiLog("/api/v1/users/$id", 'PATCH', 'FAIL_DUPLICATE_EMAIL', "email=$newEmail");
                Response::error('Email уже используется другим пользователем', 409);
            }
        }

        $changed = [];
        if ($newName !== null) {
            User::updateName($id, $newName);
            $changed[] = 'name';
        }
        if ($newEmail !== null) {
            User::updateEmail($id, $newEmail);
            $changed[] = 'email';
        }
        if ($newPasswordHash !== null) {
            User::updatePassword($id, $newPasswordHash);
            $changed[] = 'password_hash';
        }

        $updated = User::find($id);

        writeApiLog("/api/v1/users/$id", 'PATCH', 'SUCCESS', 'changed=' . implode(',', $changed));
        Response::success($updated);
    }

    public function destroy(int $id): void
    {
        if (!User::delete($id)) {
            writeApiLog("/api/v1/users/$id", 'DELETE', 'FAIL_NOT_FOUND');
            Response::error('Пользователь не найден', 404);
        }

        writeApiLog("/api/v1/users/$id", 'DELETE', 'SUCCESS');
        Response::success(['message' => 'Пользователь удалён']);
    }

    private function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === '' || $raw === false) {
            return [];
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            Response::error('Невалидный JSON в теле запроса', 400);
        }
        return $data;
    }

    private function validateUserPayload(string $name, string $email, string $password): ?string
    {
        $error = $this->validateName($name);
        if ($error !== null) {
            return $error;
        }
        $error = $this->validateEmail($email);
        if ($error !== null) {
            return $error;
        }
        $error = $this->validatePassword($password);
        if ($error !== null) {
            return $error;
        }
        return null;
    }

    private function validateName(string $name): ?string
    {
        if ($name === '') return 'Поле name обязательно';
        if (mb_strlen($name) > 100) return 'Поле name не должно быть длиннее 100 символов';
        return null;
    }

    private function validateEmail(string $email): ?string
    {
        if ($email === '') return 'Поле email обязательно';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Некорректный формат email';
        if (mb_strlen($email) > 255) return 'Поле email не должно быть длиннее 255 символов';
        return null;
    }

    private function validatePassword(string $password): ?string
    {
        if ($password === '') return 'Поле password обязательно';
        if (mb_strlen($password) < 6) return 'Пароль должен быть не короче 6 символов';
        return null;
    }
}
