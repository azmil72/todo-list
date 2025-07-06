<?php

require_once __DIR__ . '/../models/Todo.php';

class TodoController {
    private $todoModel;

    public function __construct() {
        $this->todoModel = new Todo();
    }

    public function handleRequest() {
        $user_id = $_SESSION['user']['id'];

        if (isset($_POST['add'])) {
            $task = trim($_POST['task']);
            $due_date = $_POST['due_date'] ?? null;
            $priority = $_POST['priority'] ?? 'Medium';
        
            if (!empty($task)) {
                $this->todoModel->addTodo($user_id, $task, $due_date, $priority);
            }
        }
        

        if (isset($_GET['delete'])) {
            $this->todoModel->deleteTodo($_GET['delete'], $user_id);
        }

        if (isset($_GET['toggle'])) {
            $this->todoModel->toggleStatus($_GET['toggle'], $user_id);
        }
    }
    public function getFilteredTodos($filter_priority = '', $filter_date = '') {
        $user_id = $_SESSION['user']['id'];
        $query = "SELECT * FROM todos WHERE user_id = :uid";
        $params = [':uid' => $user_id];
    
        if (!empty($filter_priority)) {
            $query .= " AND priority = :priority";
            $params[':priority'] = $filter_priority;
        }
    
        if (!empty($filter_date)) {
            $query .= " AND due_date = :due_date";
            $params[':due_date'] = $filter_date;
        }
    
        $query .= " ORDER BY created_at DESC";
    
        $conn = $this->todoModel->getConnection();
        $stmt = $conn->prepare($query);

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getTodos() {
        return $this->todoModel->getTodosByUser($_SESSION['user']['id']);
    }
    public function getUrgentTodos() {
        $user_id = $_SESSION['user']['id'];
        $conn = $this->todoModel->getConnection();
        
        $stmt = $conn->prepare("
            SELECT * FROM todos 
            WHERE user_id = :uid 
            AND status = 'pending'
            AND due_date IS NOT NULL 
            AND due_date <= CURDATE()
            ORDER BY due_date ASC
        ");
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
