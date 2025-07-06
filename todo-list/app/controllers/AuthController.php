<?php
session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function handleRegister($post) {
        $nama = trim($post['nama']);
        $username = trim($post['username']);
        $password = $post['password'];

        if (empty($nama) || empty($username) || empty($password)) {
            return ['status' => false, 'message' => 'Semua field harus diisi'];
        }

        return $this->userModel->register($nama, $username, $password);
    }

    public function handleLogin($post) {
        $username = trim($post['username']);
        $password = $post['password'];

        if (empty($username) || empty($password)) {
            return ['status' => false, 'message' => 'Username dan password wajib diisi'];
        }

        $result = $this->userModel->login($username, $password);
        if ($result['status']) {
            $_SESSION['user'] = [
                'id' => $result['user']['id'],
                'nama' => $result['user']['nama'],
                'username' => $result['user']['username']
            ];
        }
        return $result;
    }

    public function logout() {
        session_destroy();
        header("Location: ../views/login.php");
        exit;
    }
}
