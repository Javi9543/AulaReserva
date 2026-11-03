<?php
    $aulas = ["Biblioteca", "Pistas Deportivas", "Aula TIC", "Aula Robótica"];
    
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin");

    echo json_encode($aulas); //Convierte el array a json
?>