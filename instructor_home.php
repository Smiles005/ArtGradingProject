<?php
session_start();

// Must be logged in
if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$first = $_SESSION['first_name'];
$last = $_SESSION['last_name'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instructor Home</title>
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
        .container {
            background: white;
            padding: 40px;
            width: 380px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            text-align: center;
        }
        h2 {
            color: #283e51;
            margin-bottom: 10px;
        }
        .btn {
            display: block;
            width: 90%;
            padding: 14px;
            margin: 12px auto;
            background: #4b79a1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
            transition: 0.2s;
        }
        .btn:hover {
            background: #35516a;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Hello, <?php echo htmlspecialchars($first . " " . $last); ?>!</h2>
    <p>Select an option:</p>

    <a class="btn" href="drawing.html">Go to Drawing Page</a>
    <a class="btn" href="instructor_classes.php">View Your Classes</a>
</div>

</body>
</html>
