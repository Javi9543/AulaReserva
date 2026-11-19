<?php
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$archivo = __DIR__ . $url;

if ($path !== '/' && is_file($archivo)) {
    return false;
};

require_once __DIR__ . '/index.php';

?>