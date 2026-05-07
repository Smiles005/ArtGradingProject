<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$sql = "SELECT c.class_id, c.class_name
        FROM class c
        JOIN student_class sc ON c.class_id = sc.class_id
        WHERE sc.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Classes</title>
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
            margin-bottom: 25px;
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
    <h2>Your Classes</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <a class="btn" href="UploadAssignment.php?class_id=<?php echo $row['class_id']; ?>">
            <?php echo htmlspecialchars($row['class_name']); ?>
        </a>
    <?php endwhile; ?>

</div>

</body>
</html>
