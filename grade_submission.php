<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "webUser";
$password = "SuperSecurePasswordHere";
$schema = "art_grading_project";

$conn = new mysqli($servername, $username, $password, $schema);

$submission_id = $_GET['submission_id'];
$assignment_id = $_GET['assignment_id'];
$class_id = $_GET['class_id'];

// Load submission
$sql = "SELECT sub.*, s.first_name, s.last_name
        FROM assignment_submission sub
        JOIN student s ON sub.student_id = s.student_id
        WHERE sub.submission_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Load file
$sql = "SELECT * FROM assignment_submission_file WHERE submission_id=? ORDER BY uploaded_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $submission_id);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $grade = $_POST['grade'];

    $sql = "UPDATE assignment_submission SET grade=? WHERE submission_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $grade, $submission_id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_submissions.php?assignment_id=$assignment_id&class_id=$class_id");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Grade Submission</title>
<style>
    body { font-family: Arial; background: #f4f6f9; margin: 0; padding: 0; }
    .container {
        width: 50%; margin: 40px auto; background: white; padding: 30px;
        border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn {
        padding: 10px 16px; background: #4b79a1; color: white;
        text-decoration: none; border-radius: 6px; font-size: 16px;
    }
    .btn:hover { background: #35516a; }
    input[type=number] {
        width: 100%; padding: 12px; margin-top: 15px;
        border-radius: 6px; border: 1px solid #aaa;
    }
    button {
        width: 100%; padding: 14px; margin-top: 20px;
        background: #2e8b57; color: white; border: none;
        border-radius: 6px; font-size: 18px; cursor: pointer;
    }
    button:hover { background: #1f5e3c; }
</style>
</head>
<body>

<div class="container">
    <h2>Grade Submission</h2>

    <p><strong>Student:</strong> <?php echo $submission['first_name'] . " " . $submission['last_name']; ?></p>
    <p><strong>Submitted At:</strong> <?php echo $submission['submitted_at']; ?></p>

    <?php if ($file): ?>
        <a class="btn" href="drawing.html?file_id=<?php echo $file['file_id']; ?>">Open Submission</a>
    <?php else: ?>
        <p>No file uploaded.</p>
    <?php endif; ?>

    <form method="POST">
        <input type="number" name="grade" min="0" max="100" placeholder="Enter Grade" required>
        <button type="submit">Save Grade</button>
    </form>

    <br>
    <a class="btn" href="view_submissions.php?assignment_id=<?php echo $assignment_id; ?>&class_id=<?php echo $class_id; ?>">← Back</a>
</div>

</body>
</html>
