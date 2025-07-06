<?php
require_once __DIR__ . '/../config/Database.php';

class Todo {
    private $conn;
    private $table = 'todos';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getTodosByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTodo($user_id, $task, $due_date, $priority) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, task, due_date, priority) 
                                      VALUES (:uid, :task, :due_date, :priority)");
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':task', $task);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':priority', $priority);
        return $stmt->execute();
    }
    

    public function deleteTodo($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :uid");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':uid', $user_id);
        return $stmt->execute();
    }

    public function toggleStatus($id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} 
            SET status = IF(status = 'done', 'pending', 'done') 
            WHERE id = :id AND user_id = :uid");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':uid', $user_id);
        return $stmt->execute();
    }
    public function getConnection() {
        return $this->conn;
    }
    
}
