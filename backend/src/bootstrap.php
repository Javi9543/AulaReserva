<?php
// Conexión a la base de datos SQLite usando PDO

// Leemos la ruta de la BD desde la variable de entorno DB_PATH
$rutaBD = getenv('DB_PATH');

// Si no está definida, usamos la ruta por defecto
if ($rutaBD === false || $rutaBD === '') {
    $rutaBD = __DIR__ . '/../../data/aulareserva.sqlite';
}

// Creamos la conexión (la variable se llama $db, como en el código de Javi)
$db = new PDO('sqlite:' . $rutaBD);

// Que PDO lance excepciones si hay errores
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Activamos las claves foráneas en SQLite
$db->exec('PRAGMA foreign_keys = ON');