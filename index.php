<?php 
session_start();
include "/xampp/htdocs/task-manager/styles/backend/conn.php";
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$userName = $_SESSION["user"]["name"];
$userId = $_SESSION["user"]["id"];

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if(isset($_POST['submit'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date'];
    $status = $_POST['status'];
    $formattedDate = date('Y-m-d', strtotime($date));


    // Validation
    if($title == '' || $date == '') {
        $message = "Please fill in title and date fields";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, date, status, user_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssssi",$title, $description, $formattedDate, $status, $userId);
        $stmt->execute();

        if($stmt->affected_rows > 0) {
            $message = "Data inserted successfully";
        } else {
            $message = "Adding task failed";
            echo $stmt->error; 
        }
    }
}

if(isset($_POST['delete'])) {
    $taskId = $_POST['delete'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $taskId);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        $message = "Task deleted successfully";
    } else {
        $message = "Failed to delete task";
        echo $conn->error;
    }
}

if(isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/index-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Login - Task Manager</title>
</head>
<body>
    <div class="container">
        <nav class="navigation">
            <div class="logo my-3 mx-5" style="display: flex; cursor: pointer;">
                <div class="logo-icon mx-2">
                    <h2><i class="bi bi-stickies-fill text-primary"></i></h2>
                </div>
                <div class="logo-text mx-2">
                    <h1>Task Manager</h1>
                </div>
            </div>
            <div class="nav-links">
                <div class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </div>
                <div class="nav-item">
                    <form method="POST">
                        <button type="submit" name="logout">Log Out</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="main">
            <div class="content">
                <h1 class="m-3 mt-5">Welcome to <span class="text-primary">Tasks Manager</span></h1>
                <h3 class="text-center text-primary"><?php echo $userName ?></h3>
                <button id="newTaskBtn" data-bs-toggle="modal" data-bs-target="#taskModal" style="width: auto;">New Task  <i class="bi bi-plus fw-bold"></i></button>
            </div>
            <div class="tasks row g-3 m-3">
            <div class="tasks row g-3 m-3">
        <?php 
        if($result->num_rows === 0) {
            echo '<div class="col-12"><p>No tasks created yet.</p></div>';
        } else {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card col-4">';
                echo '<div class="card-title h2">' . $row['title'] . '</div>';
                echo '<div class="card-body">';
                echo '<p class="text-start">' . $row['description'] . '</p>';
                echo '<p class="text-primary text-start">' . $row['date'] . '</p>';
                echo '<p class="text-end fw-bold">' . $row['status'] . '</p>';
                echo '<form method="POST" action=""><button style="background-color: brown;" type="submit" name="delete" value="' . $row['id'] . '" onclick="reloadPage()">Delete</button></form>';
                echo '<button onclick="editTask(\'' . $row['title'] . '\', \'' . $row['description'] . '\', \'' . $row['date'] . '\', \'' . $row['status'] . '\')">Edit</button>';
                echo '<button style="background-color: greenyellow;">Completed</button>';
                echo '</div></div>';
            }
        }
        ?>
    </div>
        </div>
        <div class="footer">
            <p class="text-center bg-primary p-3">&copy; rben</p>
        </div>
    </div>

 
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true" style="border: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Task Form -->
                    <form id="taskForm" action="" method="POST">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Title</label>
                            <input type="text" class="" id="taskName" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Description</label><br>
                            <textarea name="description" id="" cols="30" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskDate" class="form-label">Due Date</label>
                            <input type="datetime-local" name="date" class="" id="taskDate" required>
                        </div>
                        <div class="mb-3" style="justify-content: space-between; font-size: 10px;">
                            <label for="taskCategory" class="form-label">Status</label>
                            <div class="d-flex">
                                <div>To Do<input type="radio" name="status" id="" checked></div>
                                <div> In Progress<input type="radio" name="status" id=""></div>
                                <div> Done<input type="radio" name="status" id=""></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="" style="background-color: gray;" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="" name="submit" onclick="addTask()">Add Task</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    
    <script>

        function addTask() {
            if (document.getElementById('taskForm').checkValidity()) {
                document.getElementById('taskForm').action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>";
                document.getElementById('taskForm').submit();
            } else {
                document.getElementById('taskForm').classList.add('was-validated');
            }
        }




        function logout() {
            alert('Logout successful!');
        }
        document.getElementById('taskForm').addEventListener('submit', function() {
            location.reload();
        });
        function reloadPage() {
            location.reload()
        } 
        function editTask(title, description, date, status) {
        document.getElementById('taskModalLabel').innerText = 'Edit Task';
        document.getElementById('taskName').value = title;
        document.getElementById('taskDescription').value = description;
        document.getElementById('taskDate').value = date;
        document.getElementById('taskStatus').value = status;
        document.getElementById('taskForm').action = 'update_task.php';
        $('#taskModal').modal('show');
    }
    </script>
</body>
</html>
