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

$assignment_id = $_GET['assignment_id'];
$class_id = $_GET['class_id'];

// Load assignment
$sql = "SELECT * FROM assignment WHERE assignment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Load class
$sql = "SELECT * FROM class WHERE class_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Load submissions + file IDs
$sql = "SELECT s.student_id, s.first_name, s.last_name,
               sub.submission_id, sub.grade, sub.submitted_at,
               f.file_id
        FROM assignment_submission sub
        JOIN student s ON sub.student_id = s.student_id
        LEFT JOIN assignment_submission_file f ON f.submission_id = sub.submission_id
        WHERE sub.assignment_id = ?
        ORDER BY s.last_name, s.first_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$submissions = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Submissions - <?php echo htmlspecialchars($assignment['name']); ?></title>
<style>
    body { font-family: Arial; background: #f4f6f9; margin: 0; padding: 0; }
    .header {
        background: linear-gradient(135deg, #4b79a1, #283e51);
        padding: 30px; color: white; text-align: center;
        font-size: 28px; font-weight: bold;
    }
    .subheader {
        text-align: center; margin-top: 10px; font-size: 18px; color: #333;
    }
    .back-btn {
        display: inline-block; margin: 20px; padding: 10px 18px;
        background: #4b79a1; color: white; text-decoration: none;
        border-radius: 6px; font-size: 16px;
    }
    .back-btn:hover { background: #35516a; }
    .container {
        width: 70%; margin: 20px auto 40px auto; background: white;
        padding: 25px; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td {
        padding: 12px; border-bottom: 1px solid #ddd; text-align: left;
    }
    th { background: #f0f0f0; font-size: 16px; }
    tr:hover { background: #f9f9f9; }
    .btn {
        padding: 8px 14px; background: #4b79a1; color: white;
        text-decoration: none; border-radius: 6px; font-size: 14px;
    }
    .btn:hover { background: #35516a; }
</style>
</head>
<body>

<div class="header"><?php echo htmlspecialchars($assignment['name']); ?></div>
<div class="subheader"><?php echo $class['course_code'] . " - " . $class['class_name']; ?></div>

<a class="back-btn" href="assignment.php?assignment_id=<?php echo $assignment_id; ?>&class_id=<?php echo $class_id; ?>">← Back to Assignment</a>

<div class="container">
    <h2>Student Submissions</h2>

    <table>
        <tr>
            <th>Student</th>
            <th>Submitted At</th>
            <th>Grade</th>
            <th>File</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $submissions->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['last_name'] . ", " . $row['first_name']); ?></td>
                <td><?php echo $row['submitted_at'] ?: "—"; ?></td>
                <td><?php echo $row['grade'] !== null ? $row['grade'] : "Not graded"; ?></td>

                <td>
                    <?php if ($row['file_id']): ?>
                        <a class="btn" href="drawing.html?file_id=<?php echo $row['file_id']; ?>">Open</a>
                    <?php else: ?>
                        No file
                    <?php endif; ?>
                </td>

                <td>
                    <a class="btn" href="grade_submission.php?submission_id=<?php echo $row['submission_id']; ?>&assignment_id=<?php echo $assignment_id; ?>&class_id=<?php echo $class_id; ?>">
                        Grade
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>
</div>

</body>
</html>
