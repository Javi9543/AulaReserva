<?php
$url = $_SERVER['REQUEST_URI'];

if (file_exists(__DIR__ . $url)) {
    return false;
}

require_once __DIR__ . '/index.php';

?>