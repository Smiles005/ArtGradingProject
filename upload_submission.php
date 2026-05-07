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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Create submission if not exists
    $sql = "SELECT submission_id FROM assignment_submission WHERE assignment_id=? AND student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $assignment_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $sql = "INSERT INTO assignment_submission (assignment_id, student_id, submitted_at)
                VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("is", $assignment_id, $student_id);
        $stmt2->execute();
        $submission_id = $stmt2->insert_id;
        $stmt2->close();
    } else {
        $row = $result->fetch_assoc();
        $submission_id = $row['submission_id'];

        $sql = "UPDATE assignment_submission SET submitted_at = NOW() WHERE submission_id=?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $submission_id);
        $stmt2->execute();
        $stmt2->close();
    }

    // 2. Upload file
    $file = $_FILES['file'];
    $file_name = $file['name'];
    $mime_type = $file['type'];
    $file_size = $file['size'];
    $file_data = file_get_contents($file['tmp_name']);

    $sql = "INSERT INTO assignment_submission_file 
            (submission_id, file_name, mime_type, file_size_bytes, uploaded_at, file_data)
            VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issis", $submission_id, $file_name, $mime_type, $file_size, $file_data);
    $stmt->send_long_data(4, $file_data);
    $stmt->execute();
    $stmt->close();

    header("Location: assignment.php?assignment_id=$assignment_id&class_id=$class_id");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Upload Submission</title>
<style>
    body { font-family: Arial; background: #f4f6f9; margin: 0; padding: 0; }
    .header {
        background: linear-gradient(135deg, #4b79a1, #283e51);
        padding: 25px; color: white; text-align: center;
        font-size: 26px; font-weight: bold;
    }
    .subheader {
        text-align: center; margin-top: 10px; font-size: 18px; color: #333;
    }
    .container {
        width: 40%; margin: 40px auto; background: white; padding: 30px;
        border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    input[type=file] {
        width: 100%; padding: 12px; margin-top: 15px; border-radius: 6px;
        border: 1px solid #aaa;
    }
    button {
        width: 100%; padding: 14px; margin-top: 20px;
        background: #4b79a1; color: white; border: none; border-radius: 6px;
        font-size: 18px; cursor: pointer;
    }
    button:hover { background: #35516a; }
</style>
</head>
<body>

<div class="header"><?php echo htmlspecialchars($assignment['name']); ?></div>
<div class="subheader"><?php echo $class['course_code'] . " - " . $class['class_name']; ?></div>

<div class="container">
    <h2>Upload Your Submission</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept="application/pdf" required>
        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>
