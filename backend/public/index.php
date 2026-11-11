<?php
header("Content-Type: application/json; charset=utf-8"); // defino que la respuesta sera en JSON 
header("Access-Control-Allow-Origin: *"); //permito que cualquier origen, envie peticiones a mi backend
$aulas = ["Biblioteca", "Pistas Deportivas", "Aula TIC", "Aula Robótica"]; //array con las aulas para reservar

//Cargo las aulas en el select
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($aulas);
    exit;
}



?>