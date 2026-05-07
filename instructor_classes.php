<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$sql = "SELECT c.class_id, c.class_name, c.course_code, c.section
        FROM class c
        JOIN instructor_class ic ON c.class_id = ic.class_id
        WHERE ic.instructor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $instructor_id);
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
            width: 420px;
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
        .add-btn {
            background: #2e8b57;
        }
        .add-btn:hover {
            background: #1f5e3c;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Classes</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <a class="btn" href="class_assignments.php?class_id=<?php echo $row['class_id']; ?>">
            <?php echo htmlspecialchars($row['course_code'] . " - " . $row['class_name'] . " (Sec " . $row['section'] . ")"); ?>
        </a>
    <?php endwhile; ?>

    <a class="btn add-btn" href="add_class.php">+ Add New Class</a>
</div>

</body>
</html>
