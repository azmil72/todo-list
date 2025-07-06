<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function register($nama, $username, $password) {
        // Cek apakah username sudah digunakan
        $query = "SELECT id FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return ['status' => false, 'message' => 'Username sudah terdaftar'];
        }

        // Simpan user baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO {$this->table} (nama, username, password) VALUES (:nama, :username, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Registrasi berhasil'];
        }

        return ['status' => false, 'message' => 'Registrasi gagal'];
    }

    public function login($username, $password) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                return ['status' => true, 'user' => $user];
            }
        }

        return ['status' => false, 'message' => 'Login gagal, periksa username atau password'];
    }
}
