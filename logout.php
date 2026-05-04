<?php
session_start();
require_once 'logger.php';

$email = $_SESSION['email'] ?? 'unknown';
writeLog($email, 'LOGOUT');

session_unset();
session_destroy();

header('Location: login.php');
exit;
