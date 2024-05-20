<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ .'/getDistance.php';
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

// Carga el archivo .env desde el directorio padre
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$response = array();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar y sanitizar los datos

    $name = htmlspecialchars($_POST['name']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $phone = htmlspecialchars($_POST['phone']);
    $numVuelo = htmlspecialchars($_POST['numVuelo']);
    $destination = htmlspecialchars($_POST['destination']);
    $origin = htmlspecialchars($_POST['origin']);
    $date1 = htmlspecialchars($_POST['date1']);
    // $departure_date = htmlspecialchars($_POST['departure_date']);
    $suitcases = htmlspecialchars($_POST['suitcases']);
    $adults = htmlspecialchars($_POST['adults']);
    $children = htmlspecialchars($_POST['children']);
    $infants = htmlspecialchars($_POST['infants']);
    $hour = htmlspecialchars($_POST['hour']);
    $total = 0;
    $distance = getDistanceAndTime($destination, $origin, "K");
    $kl = $distance;

    if (empty($name) || empty($lastname) || empty($phone) || empty($destination) || empty($origin) || empty($date1) || empty($hour) || empty($suitcases) || empty($adults)) {
        $response['status'] = 'error';
        $response['message'] = 'Todos los campos obligatorios deben ser completados.';
        echo json_encode($response);
        exit;
    }

    // Configurar conexión a la base de datos
    $servername = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_DATABASE'];

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        $response['success'] = false;
        $response['message'] = "Conexión fallida: " . $conn->connect_error;
        echo json_encode($response);
        exit();
    }
    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeroServicio = mt_rand(100000, 999999);
        $letraAleatoria = $letras[rand(0, strlen($letras) - 1)];
        $numeroServicioConLetra = $numeroServicio . $letraAleatoria;

        // Insertar en la tabla clients
        $stmt1 = $conn->prepare("INSERT INTO clients (name, lastName, phone, airport, hotel,num_air,numChildren,numInfant,arrivalDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt1) {
            throw new Exception("Error en la preparación de la declaración (clients): " . $conn->error);
        }
        $stmt1->bind_param("ssssssiis", $name, $lastname, $phone, $destination, $origin, $numVuelo,$children,$infants,$date1);

        if (!$stmt1->execute()) {
            throw new Exception("Error al insertar en clients: " . $stmt1->error);
        }

        // Obtener el ID del cliente insertado
        $clientId = $conn->insert_id;

        // Insertar en la tabla reservations
        $stmt2 = $conn->prepare("INSERT INTO reservations (total_cost, min_KM, suitcases, numPeople, numServcice, arrivalDate,hour, clientId) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt2) {
            throw new Exception("Error en la preparación de la declaración (reservations): " . $conn->error);
        }
        $stmt2->bind_param("dsiisssi", $total, $kl, $suitcases, $adults, $numeroServicioConLetra, $date1,$hour, $clientId);

        if (!$stmt2->execute()) {
            throw new Exception("Error al insertar en reservations: " . $stmt2->error);
        }

        // Confirmar la transacción
        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Datos insertados correctamente";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();

        $response['success'] = false;
        $response['message'] = $e->getMessage();
    } finally {
        // Cerrar las declaraciones si fueron inicializadas
        if (isset($stmt1) && $stmt1 !== false) {
            $stmt1->close();
        }
        if (isset($stmt2) && $stmt2 !== false) {
            $stmt2->close();
        }
        // Cerrar la conexión
        $conn->close();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Método de solicitud no válido.';
    echo json_encode($response);
}

echo json_encode($response);

