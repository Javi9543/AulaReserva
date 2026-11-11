<?php
    require_once __DIR__ . '/../bootstrap.php'; //incorporo la BD al php
    header("Content-Type: application/json; charset=utf-8"); // defino que la respuesta sera en JSON 
    header("Access-Control-Allow-Origin: *"); //permito que cualquier origen, envie peticiones a mi backend
    $metodo = $_SERVER['REQUEST_METHOD']; //defino la linea para definir el metodo para cada peticion http
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); //enlace de la api

    //estado de la API
    if ($metodo === 'GET' && $url === '/api/health') {
        echo json_encode(["ok" => true]);
        exit;
    }

    //devuelvo todas las aulas
    if ($metodo === 'GET' && $url === '/api/aulas') {
        $consulta = $db -> query("SELECT nombre FROM aulas;");
        $aulas = $consulta -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($aulas);
        exit;
    }

    //creo una nueva reserva
    if ($metodo === 'POST' && $url === '/api/reservas') {
        $input = json_decode(file_get_contents('php://input'), true); //recojo los datos que ha introducido el usuario
        $aula = $input['aula'] ?? null;
        $fecha = $input['fecha'] ?? null;
        $hora = $input['hora'] ?? null;
        $responsable = $input['responsable'] ?? null;

        //compruebo que todos los campos tienen contenido, si no es asi, le doy un mensaje de error
        if (empty($aula) || empty($fecha) || empty($hora) || empty($responsable)) {
            http_response_code(400);
            echo json_encode(['error' => 'No has completado todos los campos']);
            exit;
        }

        //compruebo que la reserva no sea duplicada antes del insert
        $comprobacion = $db->prepare("SELECT id FROM reservas WHERE aula_id = ? AND fecha = ? AND hora = ?");
        $comprobacion -> execute([$aula, $fecha, $hora]);
        if ($comprobacion -> fetch()) {
            http_response_code(400);
            echo json_encode(["error" => "Ya existe una reserva para ese aula, fecha y hora..."]);
            exit;
        }
        
        //envio a la BD la reserva
        $consulta = $db -> prepare("INSERT INTO reservas (aula_id, fecha, hora, responsable) VALUES(?,?,?,?)");
        $consulta -> execute([$aula, $fecha, $hora, $responsable]);

        http_response_code(201); //esto indica todo creado con exito
        echo json_encode(['message' => 'Reserva de aula recibida correctamente']); //Mensaje cuando se ha enviado la reserva
    }

    //consultar reservas guardadas
    if ($metodo === 'GET' && $url === '/api/reservas') {
        $consulta = $db -> query("SELECT id, aula_id, fecha, hora, responsable FROM reservas;");
        $reserva = $consulta -> fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($reserva);
        exit;
    }

    //eliminar reservas
    if ($metodo === 'DELETE' && str_starts_with($url, "/api/reservas")) {
        $enlace = explode("/", $url); //convierto el enlace a un array
        $id = $enlace[3]; //accedo al 3er elemento del enlace que es el id
        
        $consulta = $db -> prepare("DELETE FROM reservas WHERE id = ?"); //preparo la consulta para la base de datos
        $consulta->execute([$id]); //ejecuto la consulta en la base de datos
        http_response_code(200); 
        echo json_encode(["message" => "Reserva eliminada correctamente"]);
        exit;
    }
?>