<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $profile_pic;
    public $resume;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET name=:name, email=:email, phone=:phone, 
                    profile_pic=:profile_pic, resume=:resume";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->profile_pic = htmlspecialchars(strip_tags($this->profile_pic));
        $this->resume = htmlspecialchars(strip_tags($this->resume));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":profile_pic", $this->profile_pic);
        $stmt->bindParam(":resume", $this->resume);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read users with pagination
    function readAll($page, $records_per_page, $search = '', $sort_field = 'id', $sort_order = 'ASC') {
        $offset = ($page-1) * $records_per_page;
        
        $query = "SELECT * FROM " . $this->table_name;
        
        // Add search condition if provided
        if(!empty($search)) {
            $query .= " WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search";
        }
        
        // Add sorting
        $query .= " ORDER BY " . $sort_field . " " . $sort_order;
        
        // Add pagination
        $query .= " LIMIT :offset, :records_per_page";
        
        $stmt = $this->conn->prepare($query);
        
        if(!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }
        
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }
    
    // Count all users (for pagination)
    public function countAll($search = '') {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name;
        
        if(!empty($search)) {
            $query .= " WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if(!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

    // Read single user
    function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->phone = $row['phone'];
        $this->profile_pic = $row['profile_pic'];
        $this->resume = $row['resume'];
    }

    // Update user
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET name=:name, email=:email, phone=:phone, 
                    profile_pic=:profile_pic, resume=:resume
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->profile_pic = htmlspecialchars(strip_tags($this->profile_pic));
        $this->resume = htmlspecialchars(strip_tags($this->resume));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":profile_pic", $this->profile_pic);
        $stmt->bindParam(":resume", $this->resume);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete user
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>