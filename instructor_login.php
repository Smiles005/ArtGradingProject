<?php
session_start();

// Database connection setup
$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $instructor_id = trim($_POST['instructor_id']);
    $password_input = $_POST['password'];

    // Query instructor table
    $sql = "SELECT password, first_name, last_name 
            FROM instructor 
            WHERE first_name = ? AND last_name = ? AND instructor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $first, $last, $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password_input === $row['password']) {

            // Store session info
            $_SESSION['instructor_id'] = $instructor_id;
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];

            header("Location: instructor_home.php");
            exit();
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "Instructor not found.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instructor Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4b79a1, #283e51);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #283e51;
        }

        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #aaa;
        }

        button {
            width: 95%;
            padding: 12px;
            background: #4b79a1;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #35516a;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Instructor Login</h2>

    <?php if ($login_error): ?>
        <div class="error"><?php echo $login_error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="instructor_id" placeholder="Instructor ID" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Sign In</button>
    </form>
</div>

</body>
</html>
