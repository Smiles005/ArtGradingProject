<?php
session_start();

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$assignment_id = $_GET['assignment_id'];
$class_id = $_GET['class_id'];

// Load assignment
$sql = "SELECT * FROM assignment WHERE assignment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Load class info
$sql = "SELECT * FROM class WHERE class_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

$is_instructor = isset($_SESSION['instructor_id']);
$is_student = isset($_SESSION['student_id']);
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $assignment['name']; ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    .header {
        background: linear-gradient(135deg, #4b79a1, #283e51);
        padding: 30px;
        color: white;
        text-align: center;
        font-size: 28px;
        font-weight: bold;
    }
    .subheader {
        text-align: center;
        margin-top: 10px;
        font-size: 18px;
        color: #333;
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
        width: 60%;
        margin: 10px auto 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .desc {
        font-size: 17px;
        color: #444;
        line-height: 1.5;
        margin-bottom: 30px;
    }
    .btn {
        display: block;
        width: 100%;
        padding: 14px;
        margin: 12px 0;
        background: #4b79a1;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 18px;
        text-align: center;
        transition: 0.2s;
    }
    .btn:hover {
        background: #35516a;
    }
</style>
</head>
<body>

<div class="header">
    <?php echo htmlspecialchars($assignment['name']); ?>
</div>

<div class="subheader">
    <?php echo $class['course_code'] . " - " . $class['class_name'] . " (Sec " . $class['section'] . ")"; ?>
</div>

<a class="back-btn" href="class_assignments.php?class_id=<?php echo $class_id; ?>">← Back to Assignments</a>

<div class="container">

    <div class="desc">
        <?php echo nl2br(htmlspecialchars($assignment['description'])); ?>
    </div>

    <?php if ($is_student): ?>
        <a class="btn" href="upload_submission.php?assignment_id=<?php echo $assignment_id; ?>&class_id=<?php echo $class_id; ?>">
            Upload Your Submission
        </a>
    <?php endif; ?>

    <?php if ($is_instructor): ?>
        <a class="btn" href="view_submissions.php?assignment_id=<?php echo $assignment_id; ?>&class_id=<?php echo $class_id; ?>">
            View Student Submissions
        </a>
    <?php endif; ?>

</div>

</body>
</html>
