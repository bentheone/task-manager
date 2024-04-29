<?php 
include '/xampp/htdocs/task-manager/styles/backend/conn.php';
$message = '';

if(isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    // Validation
    if($name == '' || $email == '' || $password == '' || $confirm_password == '') {
        $message = 'Please fill all inputs';
    } else {
        if(!password_verify($confirm_password, $password)) {
            $message = 'Passwords do not match';
        } else {
            $sql = "SELECT email FROM user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = 'Email already exists';
            } else {
                $sql = "INSERT INTO user (name, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $password);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $message = 'Registration successful';
                    header("Location: login.php");
                    exit();
                } else {
                    $message = 'Registration failed';
                    echo "Error: " . $stmt->error;
                }
            }

            $stmt->close();
        }
    }
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
    <title>Create Account - Task Manager</title>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="text-primary"><i class="bi bi-stickies-fill"></i></h1>
            <h2 class="h2">Task Manager</h2>
            <p class="text-center h5 text-primary">Sign Up</p>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" name="name" id="" class="input" placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="" class="input" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="" class="input" placeholder="Enter your password">
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" id="" class="input" placeholder="Confirm your password">
                </div>
                <p id="err-msg" class="text-center text-danger"><?php echo $message ?></p>
                <button type="submit" class="button mt-3" name="submit">Get Started</button><br>
                <a href="login.php" class="text-primary">Already have an account?</a>
            </form>
        </div>
    </div>
</body>
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
</html>
