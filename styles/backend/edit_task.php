<?php
session_start();
include "/xampp/htdocs/task-manager/styles/backend/conn.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['update'])) {
    $taskId = $_POST['taskId'];
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date'];
    $status = $_POST['status'];
    $formattedDate = date('Y-m-d', strtotime($date));

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, date=?, status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssii", $title, $description, $formattedDate, $status, $taskId, $_SESSION['user']['id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Task updated successfully";
    } else {
        $message = "Failed to update task";
        echo $stmt->error;
    }
}

header("Location: index.php"); 
?>
