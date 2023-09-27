<?php
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

class TechnologyController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getTechnologyById($id) {
        $id = htmlspecialchars(strip_tags($id));

        $query = "SELECT * FROM technologies WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $technology = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($technology) {
            http_response_code(200);
            echo json_encode($technology);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Technologie non trouvée."));
        }
    }
}
?>