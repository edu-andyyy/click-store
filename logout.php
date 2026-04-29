<?php
session_start();
require_once 'logger.php';

$username = $_SESSION['username'] ?? 'unknown';
writeLog($username, 'LOGOUT');

session_unset();
session_destroy();

header('Location: login.php');
exit;
