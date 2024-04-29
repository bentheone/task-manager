<?php 
session_start();
include '/xampp/htdocs/task-manager/styles/backend/conn.php';
$message = '';

if(isset($_POST["submit"])) {
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if($stmt->error) {
        $message = "SQL Error: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])){
                $_SESSION['user'] = $user;
                echo "<script>window.location.href = 'index.php';</script>";
                exit; 
            } else {
                $message = "Password not correct";
            }
        } else {
            $message = "Email not found";
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Login - Task Manager</title>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="text-primary"><i class="bi bi-stickies-fill"></i></h1>
            <h2 class="h2">Task Manager</h2>
            <p class="text-center h5 text-primary">Login Now !</p>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="email" name="email" class="input" placeholder="Enter your email">
                    </div>
                <div class="form-group">
                    <input type="password" name="password" class="input" placeholder="Enter your password">
                    </div>
                <p id="err-msg" class="text-center text-danger"><?php echo $message ?></p>
                    <button type="submit" class="button mt-3" name="submit">Log In </button><br>
                    <a href="signup.php" class="text-primary">Don't have an account?</a>
            </form>
        </div>
    </div>
<script>
    const errMsg = document.getElementById('err-msg')
    if(errMsg.innerHTML == "Registration successful"){
        errMsg.classList.remove("text-danger");
        errMsg.classList.add("text-success");
    }
    setTimeout(function() {
        errMsg.style.display = 'none';
    }, 3000)
</script>
</body>
</html>