<?php
    $aulas = ["Biblioteca", "Pistas Deportivas", "Aula TIC", "Aula Robótica"]; //array con las aulas para reservar
    
    header("Content-Type: application/json; charset=utf-8"); // defino que la respuesta sera en JSON 
    header("Access-Control-Allow-Origin: *"); //permito que cualquier origen, envie peticiones a mi backend

    
    //Si es el caso de ser una peticion POST a la hora de recibir reservas

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $input = json_decode(file_get_contents('php://inputs'), true) //recojo los datos que ha introducido el usuario

        $aula = $input['aula'] ?? null;
        $fecha = $input['fecha'] ?? null;
        $hora = $input['hora'] ?? null;
        $responsable = $input['responsable'] ?? null;
    }

   
    //compruebo que todos los campos existen, si no es asi, le doy un mensaje de error
    if (empty($aula) || empty($fecha) || empty($hora) || empty($responsable)){
        http_response_code(400);
        echo json_encode('error' => 'No has completado todos los campos');
    }

    http_response_code(201) //esto indica todo creado con exito
    echo json_encode(['message' => 'Reserva de aula recibida correctamente']); //Convierte el array a json
    exit;

?> 