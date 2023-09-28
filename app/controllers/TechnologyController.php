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
    public $nom;
    public $categorie_id;
    public $liens;
    public $logo_path;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Écriture de la requête SQL pour insérer une nouvelle technologie
        $sql = "INSERT INTO " . $this->table_name . " (nom, categorie_id, liens, logo_path) VALUES (:nom, :categorie_id, :liens, :logo_path)";

        // Préparation de la requête
        $stmt = $this->conn->prepare($sql);

        // Protection contre les injections SQL
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie_id = intval($this->categorie_id); // Assurez-vous que categorie_id est un entier
        $this->liens = htmlspecialchars(strip_tags($this->liens));
        $this->logo_path = htmlspecialchars(strip_tags($this->logo_path));

        // Remplacement des valeurs liées
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie_id', $this->categorie_id, PDO::PARAM_INT); // Précisez que c'est un entier
        $stmt->bindParam(':liens', $this->liens);
        $stmt->bindParam(':logo_path', $this->logo_path);

        // Exécution de la requête
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
        // Écriture de la requête SQL pour lire une technologie par son ID
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
    
        // Liaison de l'ID de la technologie à la requête
        $stmt->bindParam(1, $this->id);
    
        // Exécution de la requête
        $stmt->execute();
    
        return $stmt;
    }   
    
    // Lire toutes les technologies
    public function readAll() {
        // Écriture de la requête SQL pour lire toutes les technologies
        $query = "SELECT * FROM " . $this->table_name;

        // Préparation de la requête
        $stmt = $this->conn->prepare($query);

        // Exécution de la requête
        $stmt->execute();

        return $stmt;
    }

    // Mettre à jour une technologie
    public function update() {
        // Écriture de la requête SQL pour mettre à jour une technologie
        $query = "UPDATE " . $this->table_name . " SET nom = :nom, categorie_id = :categorie_id, liens = :liens, logo_path = :logo_path WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        // Protection contre les injections SQL
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->liens = htmlspecialchars(strip_tags($this->liens));
        $this->logo_path = htmlspecialchars(strip_tags($this->logo_path));
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Remplacement des valeurs liées
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':liens', $this->liens);
        $stmt->bindParam(':logo_path', $this->logo_path);
        $stmt->bindParam(':id', $this->id);
    
        // Exécution de la requête
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }    

    // Supprimer une technologie par son ID
    public function delete() {
        // Écriture de la requête SQL pour supprimer une technologie par son ID
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
    
        // Liaison de l'ID de la technologie à la requête
        $stmt->bindParam(1, $this->id);
    
        // Exécution de la requête
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

    public function getTechnologyById($id) {
        $id = htmlspecialchars(strip_tags($id));

        $technology = new Technology($this->db);
        $technology->id = $id;
        $result = $technology->readOne();

        if ($result) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "La technologie n'existe pas."));
        }
    }
} 
?>