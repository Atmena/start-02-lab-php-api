<?php
$host = 'db';
$dbname = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Erreur de connexion à la base de données : " . $exception->getMessage();
    exit();
}

if (isset($uri)) {
    if ($uri[1] === 'apitest') {
        require '../controllers/TechnologyController.php';

        $technologyController = new TechnologyController($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            if ($technologyController->createTechnology($data)) {
                http_response_code(201);
                echo json_encode(array("message" => "Technologie créée avec succès."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Impossible de créer la technologie."));
            }
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uri[2]) && is_numeric($uri[2])) {
                $technologyController->getTechnologyById($uri[2]);
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "ID de technologie invalide."));
            }
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents("php://input"), true);

            if (isset($uri[2]) && is_numeric($uri[2])) {
                if ($technologyController->updateTechnology($uri[2], $data)) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Technologie mise à jour avec succès."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Impossible de mettre à jour la technologie."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "ID de technologie invalide."));
            }
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (isset($uri[2]) && is_numeric($uri[2])) {
                if ($technologyController->deleteTechnology($uri[2])) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Technologie supprimée avec succès."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Impossible de supprimer la technologie."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "ID de technologie invalide."));
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Page non trouvée."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "URL invalide."));
}
?>