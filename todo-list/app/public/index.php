<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../views/login.php');
    exit;
}

require_once '../controllers/TodoController.php';
$todoController = new TodoController();
$todoController->handleRequest();
$priority = $_GET['priority'] ?? '';
$due_date = $_GET['due_date'] ?? '';
$todos = $todoController->getFilteredTodos($priority, $due_date);
$urgentTodos = $todoController->getUrgentTodos();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ToDo App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            /* Light Mode Colors */
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --low-priority: #28a745;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --white: #ffffff;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --bg-color: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            --text-color: #212529;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --input-bg: #ffffff;
        }

        /* Dark Mode Colors */
        [data-theme="dark"] {
            --primary: #4895ef;
            --primary-light: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --low-priority: #38b000;
            --light: #343a40;
            --dark: #f8f9fa;
            --gray: #adb5bd;
            --white: #212529;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            --bg-color: linear-gradient(135deg, #212529 0%, #343a40 100%);
            --text-color: #f8f9fa;
            --card-bg: #2b2d42;
            --border-color: #495057;
            --input-bg: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            min-height: 100vh;
            padding: 2rem;
            color: var(--text-color);
        }
        
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--card-bg);
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .user-greeting {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .user-greeting span {
            color: var(--primary);
            position: relative;
        }
        
        .user-greeting span::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-light);
            border-radius: 3px;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .user-greeting:hover span::after {
            transform: scaleX(1);
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(247, 37, 133, 0.2);
        }
        
        .logout-btn:hover {
            background: #d91a6d;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(247, 37, 133, 0.3);
        }
        
        /* Theme Toggle */
        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            border-radius: 15px;
            background: var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0 5px;
        }
        
        .theme-toggle::before {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--white);
            transform: translateX(0);
            transition: transform 0.3s ease;
        }
        
        [data-theme="dark"] .theme-toggle::before {
            transform: translateX(30px);
        }
        
        .theme-icon {
            font-size: 0.9rem;
            z-index: 1;
            color: var(--text-color);
        }
        
        .theme-icon.sun {
            margin-right: auto;
        }
        
        .theme-icon.moon {
            margin-left: auto;
        }
        
        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        
        /* Add Task Form */
        .add-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .form-control {
            padding: 0.8rem 1.2rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            width: 100%;
            background: var(--input-bg);
            color: var(--text-color);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .task-input {
            grid-column: 1 / -1;
        }
        
        .add-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
            transition: all 0.3s ease;
            grid-column: 1 / -1;
        }
        
        .add-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Filter Section */
        .filter-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-btn {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--text-color);
        }
        
        .filter-btn:hover {
            background: var(--light);
        }
        
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Alert Section */
        .alert-card {
            background: rgba(247, 37, 133, 0.1);
            border-left: 4px solid var(--danger);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
            color: var(--text-color);
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.01); }
        }
        
        .alert-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }
        
        .alert-list {
            padding-left: 1.5rem;
            color: var(--gray);
        }
        
        .alert-list li {
            margin-bottom: 0.5rem;
        }
        
        /* Todo List */
        .todo-list {
            list-style: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
            animation: fadeIn 1s ease-out;
        }
        
        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border-color);
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .todo-item {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .todo-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary);
            transition: all 0.3s ease;
        }
        
        .todo-item.high-priority::before {
            background: var(--danger);
        }
        
        .todo-item.medium-priority::before {
            background: var(--warning);
        }
        
        .todo-item.low-priority::before {
            background: var(--low-priority);
        }
        
        .todo-item.done {
            background: rgba(76, 201, 240, 0.05);
        }
        
        .todo-item.done::before {
            background: var(--success);
        }
        
        .todo-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .task-content {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            flex: 1;
        }
        
        .task-checkbox {
            min-width: 20px;
            height: 20px;
            cursor: pointer;
            margin-top: 3px;
        }
        
        .task-text {
            font-size: 1.1rem;
            color: var(--text-color);
            word-break: break-word;
            flex: 1;
        }
        
        .task-text.done {
            text-decoration: line-through;
            color: var(--gray);
        }
        
        .task-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .task-due {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .task-priority {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .priority-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .high-priority-dot {
            background: var(--danger);
        }
        
        .medium-priority-dot {
            background: var(--warning);
        }
        
        .low-priority-dot {
            background: var(--low-priority);
        }
        
        .task-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .action-btn:hover {
            background: rgba(0, 0, 0, 0.1);
            color: var(--text-color);
            transform: scale(1.1);
        }
        
        .toggle-btn {
            color: var(--success);
        }
        
        .delete-btn {
            color: var(--danger);
        }
        
        .fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }
        
        @keyframes fadeOut {
            to { opacity: 0; height: 0; padding: 0; margin: 0; transform: scale(0.9); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .header-actions {
                width: 100%;
                justify-content: center;
            }
            
            .user-greeting {
                font-size: 1.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .todo-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .task-actions {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="user-greeting">Halo, <span><?= htmlspecialchars($_SESSION['user']['nama']) ?></span> 👋</h1>
            <div class="header-actions">
                <div class="theme-toggle" id="themeToggle">
                    <i class="fas fa-sun theme-icon sun"></i>
                    <i class="fas fa-moon theme-icon moon"></i>
                </div>
                <button class="logout-btn" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
        
        <div class="main-content">
            <!-- Add Task Card -->
            <div class="card">
                <h2 class="section-title">Tambah Tugas Baru</h2>
                <form method="post" class="add-form" onsubmit="return validateForm()">
                    <div class="form-group task-input">
                        <label for="task">Deskripsi Tugas</label>
                        <input type="text" name="task" id="task" class="form-control" placeholder="Apa yang perlu dilakukan?" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="due_date">Jatuh Tempo</label>
                            <input type="date" name="due_date" id="due_date" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="priority">Prioritas</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="Low">Rendah</option>
                                <option value="Medium" selected>Sedang</option>
                                <option value="High">Tinggi</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="add" class="add-btn">
                        <i class="fas fa-plus"></i> Tambah Tugas
                    </button>
                </form>
            </div>
            
            <!-- Filter Card -->
            <div class="card">
                <h2 class="section-title">Filter Tugas</h2>
                <form method="get" class="filter-section">
                    <div class="form-group">
                        <label for="filter-priority">Prioritas</label>
                        <select name="priority" id="filter-priority" class="form-control">
                            <option value="">Semua Prioritas</option>
                            <option value="High" <?= ($_GET['priority'] ?? '') === 'High' ? 'selected' : '' ?>>Tinggi</option>
                            <option value="Medium" <?= ($_GET['priority'] ?? '') === 'Medium' ? 'selected' : '' ?>>Sedang</option>
                            <option value="Low" <?= ($_GET['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>Rendah</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter-date">Deadline</label>
                        <input type="date" name="due_date" id="filter-date" class="form-control" value="<?= $_GET['due_date'] ?? '' ?>">
                    </div>
                    
                    <div class="form-group" style="align-self: flex-end;">
                        <button type="submit" class="add-btn" style="padding: 0.8rem;">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Urgent Tasks Alert -->
            <?php if (count($urgentTodos) > 0): ?>
                <div class="alert-card">
                    <div class="alert-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Peringatan Deadline!</strong>
                    </div>
                    <p>Anda memiliki <?= count($urgentTodos) ?> tugas yang melewati atau mencapai deadline:</p>
                    <ul class="alert-list">
                        <?php foreach ($urgentTodos as $todo): ?>
                            <li>
                                <strong><?= htmlspecialchars($todo['task']) ?></strong>
                                (Deadline: <?= $todo['due_date'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Todo List Card -->
            <div class="card">
                <h2 class="section-title">Daftar Tugas Anda</h2>
                
                <ul class="todo-list">
                    <?php if (count($todos) === 0): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <p>Belum ada tugas. Yuk, tambahkan satu!</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php foreach ($todos as $todo): ?>
                        <li class="todo-item <?= $todo['priority'] === 'High' ? 'high-priority' : ($todo['priority'] === 'Medium' ? 'medium-priority' : 'low-priority') ?> <?= $todo['status'] === 'done' ? 'done' : '' ?>" id="todo-<?= $todo['id'] ?>">
                            <div class="task-content">
                                <input type="checkbox" class="task-checkbox" <?= $todo['status'] === 'done' ? 'checked' : '' ?> onclick="window.location.href='?toggle=<?= $todo['id'] ?>'">
                                <div>
                                    <div class="task-text <?= $todo['status'] === 'done' ? 'done' : '' ?>">
                                        <?= htmlspecialchars($todo['task']) ?>
                                    </div>
                                    <div class="task-meta">
                                        <?php if ($todo['due_date']): ?>
                                            <div class="task-due">
                                                <i class="far fa-calendar-alt"></i>
                                                <?= $todo['due_date'] ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="task-priority">
                                            <span class="priority-dot <?= $todo['priority'] === 'High' ? 'high-priority-dot' : ($todo['priority'] === 'Medium' ? 'medium-priority-dot' : 'low-priority-dot') ?>"></span>
                                            <?= $todo['priority'] === 'High' ? 'Tinggi' : ($todo['priority'] === 'Medium' ? 'Sedang' : 'Rendah') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="task-actions">
                                <a href="?toggle=<?= $todo['id'] ?>" class="action-btn toggle-btn" title="<?= $todo['status'] === 'done' ? 'Tandai Belum Selesai' : 'Tandai Selesai' ?>">
                                    <i class="fas <?= $todo['status'] === 'done' ? 'fa-undo' : 'fa-check' ?>"></i>
                                </a>
                                <a href="?delete=<?= $todo['id'] ?>" class="action-btn delete-btn" title="Hapus" onclick="return confirmDelete(event, <?= $todo['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        // Apply the saved theme
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
        
        // Form Validation
        function validateForm() {
            const task = document.getElementById('task').value.trim();
            if (task === '') {
                alert('Tugas tidak boleh kosong!');
                return false;
            }
            return true;
        }
        
        // Task Deletion Confirmation
        function confirmDelete(event, id) {
            event.preventDefault();
            if (confirm('Yakin ingin menghapus tugas ini?')) {
                const todoItem = document.getElementById(`todo-${id}`);
                todoItem.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = event.target.closest('a').href;
                }, 300);
            }
            return false;
        }
        
        // Hover Effects for Todo Items
        document.querySelectorAll('.todo-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = 'translateY(-3px)';
                item.style.boxShadow = '0 6px 15px rgba(0, 0, 0, 0.1)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.transform = '';
                item.style.boxShadow = '';
            });
        });
        
        // Animation for Priority Dots
        document.querySelectorAll('.priority-dot').forEach(dot => {
            dot.addEventListener('mouseenter', () => {
                dot.style.transform = 'scale(1.3)';
            });
            
            dot.addEventListener('mouseleave', () => {
                dot.style.transform = '';
            });
        });
    </script>
</body>
</html>