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

    public function create() {
        // Écriture de la requête SQL pour insérer une nouvelle technologie
        $sql = "INSERT INTO " . $this->table_name . " (name, link, liens, logoLink, categorie_id) VALUES (:name, :link, :logoLink), :categorie_id";

        // Préparation de la requête
        $stmt = $this->conn->prepare($sql);

        // Protection contre les injections SQL
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
        

        $stmt->bindParam(':name', $this->nom);
        $stmt->bindParam(':link', $this->liens);
        $stmt->bindParam(':logoLink', $this->logo_path);
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
    
        return $stmt;
    }   
    
    // Lire toutes les technologies
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    // Mettre à jour une technologie
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, link = :link, logoLink = :logoLink, categorie_id = :categorie_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->link = htmlspecialchars(strip_tags($this->link));
        $this->logoLink = htmlspecialchars(strip_tags($this->logoLink));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->id = htmlspecialchars(strip_tags($this->id));
    
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
        return $technology->readOne($id);
    }

    public function getAllTechnologies() {

        $technology = new Technology($this->db);
        return $technology->readAll();
    }

    public function updateTechnology($id, $data) {

        $technology = new Technology($this->db);
        return $technology->update($id, $data);
    }

    public function deleteTechnology($id) {

        $technology = new Technology($this->db);
        return $technology->delete($id);
    }
} 
?>