<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$front = realpath(__DIR__ . '/../../frontend');
$file = realpath($front . ($path === '/' ? '/index.html' : $path));

if ($file && str_starts_with($file, $front) && is_file($file)) {
    $mimes = ['html' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (isset($mimes[$ext])) header("Content-Type: {$mimes[$ext]}; charset=utf-8");
    readfile($file);
    exit;
}

if ($path !== '/' && is_file(__DIR__ . $path)) return false;

require_once __DIR__ . '/index.php';

