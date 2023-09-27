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

class Technology {
    private $conn;
    private $table_name = "technologies";

    public $id;
    public $nom;
    public $categorie_id;
    public $liens;
    public $logo_path;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer une nouvelle technologie
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nom, categorie_id, liens, logo_path) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->categorie_id);
        $stmt->bindParam(3, $this->liens);
        $stmt->bindParam(4, $this->logo_path);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Lire une technologie par son ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Mettre à jour une technologie
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom = ?, categorie_id = ?, liens = ?, logo_path = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->categorie_id);
        $stmt->bindParam(3, $this->liens);
        $stmt->bindParam(4, $this->logo_path);
        $stmt->bindParam(5, $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Supprimer une technologie par son ID
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

require 'api/routes/api.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] === 'technologies') {
    require 'controllers/TechnologyController.php';
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}
?>