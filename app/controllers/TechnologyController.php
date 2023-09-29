<?php
$host = 'db';
$dbname = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
    $conn = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Erreur de connexion à la base de données : " . $exception->getMessage();
    exit();
}

class Technology {
    private $conn;
    private $table_name = "technologie";

    public $id;
    public $name;
    public $link;
    public $logoLink;
    public $categorie_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        // Écriture de la requête SQL pour insérer une nouvelle technologie
        $sql = "INSERT INTO " . $this->table_name . " (name, link, logoLink, categorie_id) VALUES (:name, :link, :logoLink, :categorie_id)";

        // Préparation de la requête
        $stmt = $this->conn->prepare($sql);

        // Protection contre les injections SQL
        $this->name = isset($data['name']) ? htmlspecialchars(strip_tags($data['name'])) : null;
        $this->link = isset($data['link']) ? htmlspecialchars(strip_tags($data['link'])) : null;
        $this->logoLink = isset($data['logoLink']) ? htmlspecialchars(strip_tags($data['logoLink'])) : null;
        $this->categorie_id = isset($data['categorie_id']) ? htmlspecialchars(strip_tags($data['categorie_id'])) : null;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':link', $this->link);
        $stmt->bindParam(':logoLink', $this->logoLink);
        $stmt->bindParam(':categorie_id', $this->categorie_id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $exception) {
            echo "Erreur lors de la création de la technologie : " . $exception->getMessage();
            return false;
        }
    }
    
    // Lire une technologie par son ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        // Récupération du résultat sous forme de tableau associatif
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Renvoi des données au format JSON
        return json_encode($row);
    }
    
    // Lire toutes les technologies
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        // Récupération du résultat sous forme de tableau associatif
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Renvoi des données au format JSON
        return json_encode($row);
    }

    // Mettre à jour une technologie
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, link = :link, logoLink = :logoLink, categorie_id = :categorie_id WHERE ('id' = :id )";
        $stmt = $this->conn->prepare($query);
    
        if (isset($this->name)) {
            $this->name = htmlspecialchars(strip_tags($this->name));
        }
        if (isset($this->link)) {
            $this->link = htmlspecialchars(strip_tags($this->link));
        }
        if (isset($this->logoLink)) {
            $this->logoLink = htmlspecialchars(strip_tags($this->logoLink));
        }
        if (isset($this->categorie_id)) {
            $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        }
        if (isset($this->id)) {
            $this->id = htmlspecialchars(strip_tags($this->id));
        }
    
        // Liaison des paramètres
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':link', $this->link);
        $stmt->bindParam(':logoLink', $this->logoLink);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':id', $this->id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Supprimer une technologie par son ID
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':id', $this->id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }    
}

class TechnologyController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createTechnology($data) {
        $technology = new Technology($this->db);
        if ($technology->create($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getTechnologyById($id) {
        $technology = new Technology($this->db);
        $technology->id = $id;
        $data = $technology->readOne();
    
        if ($data) {
            http_response_code(200);
            echo $data;
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "La technologie n'existe pas."));
        }
    }    

    public function getAllTechnologies() {
        $technology = new Technology($this->db);
        $data = $technology->readAll();
    
        if ($data) {
            http_response_code(200);
            echo $data;
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "La technologie n'existe pas."));
        }
    }    

    public function updateTechnology($id, $data) {
        if (!is_numeric($id)) {
            return false;
        }
    
        $technology = new Technology($this->db);
    
        if (isset($data['name'])) {
            $technology->name = $data['name'];
        }
        if (isset($data['link'])) {
            $technology->link = $data['link'];
        }
        if (isset($data['logoLink'])) {
            $technology->logoLink = $data['logoLink'];
        }
        if (isset($data['categorie_id'])) {
            $technology->categorie_id = $data['categorie_id'];
        }

        if ($technology->update($id)) {
            return "Technologie mise à jour avec succès.";
        } else {
            return "Impossible de mettre à jour la technologie.";
        }
    }
      

    public function deleteTechnology($id) {
        $technology = new Technology($this->db);
    
        if ($technology->delete($id)) {
            http_response_code(204);
            return;
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de supprimer la technologie."));
            return;
        }
    }    
} 
?>