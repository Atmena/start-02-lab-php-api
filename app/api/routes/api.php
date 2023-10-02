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
        require __DIR__ . '/../../controllers/TechnologyController.php';

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
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($uri[2]) && is_numeric($uri[2])) {
                $result = $technologyController->getTechnologyById($uri[2]);
        
                if ($result) {
                    $data = $result->fetch(PDO::FETCH_ASSOC);
        
                    if ($data) {
                        header('Content-Type: application/json');
                        echo json_encode($data);
                    } else {
                        http_response_code(404);
                        echo json_encode(array("message" => "La technologie n'existe pas."));
                    }
                } else {
                    http_response_code(500);
                }
            } else {
                $result = $technologyController->getAllTechnologies();
        
                if ($result) {
                    $data = $result->fetchAll(PDO::FETCH_ASSOC);
        
                    if ($data) {
                        header('Content-Type: application/json');
                        echo json_encode($data);
                    } else {
                        http_response_code(404);
                        echo json_encode(array("message" => "Aucune technologie trouvée."));
                    }
                } else {
                    http_response_code(500);
                }
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
                $result = $technologyController->deleteTechnology($uri[2]);
                if ($result === true) {
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