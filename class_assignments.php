<?php
session_start();

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$class_id = $_GET['class_id'];

$is_instructor = isset($_SESSION['instructor_id']);
$is_student = isset($_SESSION['student_id']);

// Load class info
$sql = "SELECT * FROM class WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Load assignments
$sql = "SELECT * FROM assignment WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$assignments = $stmt->get_result();
$stmt->close();

// Back button destination
$back_link = $is_instructor ? "instructor_classes.php" : "student_classes.php";
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $class['course_code'] . " - " . $class['class_name']; ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    .header {
        background: linear-gradient(135deg, #4b79a1, #283e51);
        padding: 25px;
        color: white;
        text-align: center;
        font-size: 26px;
        font-weight: bold;
    }
    .back-btn {
        display: inline-block;
        margin: 20px;
        padding: 10px 18px;
        background: #4b79a1;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 16px;
    }
    .back-btn:hover {
        background: #35516a;
    }
    .container {
        width: 70%;
        margin: 10px auto 40px auto;
    }
    .assignment-card {
        background: white;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: 0.2s;
        text-decoration: none;
        color: #333;
        display: block;
    }
    .assignment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .assignment-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 6px;
    }
    .assignment-desc {
        color: #555;
        font-size: 15px;
    }
    .add-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #2e8b57;
        color: white;
        padding: 16px 22px;
        border-radius: 50px;
        font-size: 18px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transition: 0.2s;
    }
    .add-btn:hover {
        background: #1f5e3c;
    }
</style>
</head>
<body>

<div class="header">
    <?php echo $class['course_code'] . " - " . $class['class_name'] . " (Sec " . $class['section'] . ")"; ?>
</div>

<a class="back-btn" href="<?php echo $back_link; ?>">← Back to Classes</a>

<div class="container">

    <?php while ($a = $assignments->fetch_assoc()): ?>
        <a class="assignment-card" href="assignment.php?assignment_id=<?php echo $a['assignment_id']; ?>&class_id=<?php echo $class_id; ?>">
            <div class="assignment-title"><?php echo htmlspecialchars($a['name']); ?></div>
            <div class="assignment-desc"><?php echo htmlspecialchars(substr($a['description'], 0, 120)); ?>...</div>
        </a>
    <?php endwhile; ?>

</div>

<?php if ($is_instructor): ?>
    <a class="add-btn" href="edit_assignment.php?class_id=<?php echo $class_id; ?>">+ Add Assignment</a>
<?php endif; ?>

</body>
</html>
